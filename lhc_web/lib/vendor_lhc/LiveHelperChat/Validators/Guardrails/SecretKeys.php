<?php

namespace LiveHelperChat\Validators\Guardrails;

class SecretKeys
{
    public static $COMMON_KEY_PREFIXES = [
        'key-',
        'sk-',
        'sk_',
        'pk_',
        'pk-',
        'ghp_',
        'AKIA',
        'xox',
        'SG.',
        'hf_',
        'api-',
        'apikey-',
        'token-',
        'secret-',
        'SHA:',
        'Bearer ',
    ];

    public static $ALLOWED_EXTENSIONS = [
        '.py',
        '.js',
        '.html',
        '.css',
        '.json',
        '.md',
        '.txt',
        '.csv',
        '.xml',
        '.yaml',
        '.yml',
        '.ini',
        '.conf',
        '.config',
        '.log',
        '.sql',
        '.sh',
        '.bat',
        '.dll',
        '.so',
        '.dylib',
        '.jar',
        '.war',
        '.php',
        '.rb',
        '.go',
        '.rs',
        '.ts',
        '.jsx',
        '.vue',
        '.cpp',
        '.c',
        '.h',
        '.cs',
        '.fs',
        '.vb',
        '.doc',
        '.docx',
        '.xls',
        '.xlsx',
        '.ppt',
        '.pptx',
        '.pdf',
        '.jpg',
        '.jpeg',
        '.png',
    ];

    public static $CONFIGS = [
        'strict' => [
            'min_length' => 10,
            'min_entropy' => 3.0,
            'min_diversity' => 2,
            'strict_mode' => true,
        ],
        'balanced' => [
            'min_length' => 10,
            'min_entropy' => 3.8,
            'min_diversity' => 3,
            'strict_mode' => false,
        ],
        'permissive' => [
            'min_length' => 30,
            'min_entropy' => 4.0,
            'min_diversity' => 2,
            'strict_mode' => false,
        ],
    ];

    /**
     * Calculate the Shannon entropy of a string.
     */
    public static function entropy($s)
    {
        $len = mb_strlen($s);
        if ($len === 0) {
            return 0;
        }

        $counts = [];
        
        if (function_exists('mb_str_split')) {
            $chars = mb_str_split($s);
        } else {
            $chars = preg_split('//u', $s, -1, PREG_SPLIT_NO_EMPTY);
        }

        foreach ($chars as $c) {
            if (!isset($counts[$c])) {
                $counts[$c] = 0;
            }
            $counts[$c]++;
        }

        $entropy = 0;
        foreach ($counts as $count) {
            $probability = $count / $len;
            $entropy -= $probability * log($probability, 2);
        }

        return $entropy;
    }

    /**
     * Count the number of character types present in a string.
     */
    public static function charDiversity($s)
    {
        $hasLower = preg_match('/[a-z]/', $s);
        $hasUpper = preg_match('/[A-Z]/', $s);
        $hasDigit = preg_match('/\d/', $s);
        $hasSpecial = preg_match('/[^\w]/', $s); // \w is [a-zA-Z0-9_]

        return ($hasLower ? 1 : 0) + ($hasUpper ? 1 : 0) + ($hasDigit ? 1 : 0) + ($hasSpecial ? 1 : 0);
    }

    /**
     * Check if text is already masked by another rule.
     */
    public static function isAlreadyMasked($text)
    {
        return preg_match('/\[mask\].*\[\/mask\]/', $text);
    }

    /**
     * Check if text contains allowed URL or file extension patterns.
     */
    public static function containsAllowedPattern($text)
    {
        // Check if it's a URL pattern
        $urlPattern = '/^https?:\/\/[a-zA-Z0-9.-]+\/?[a-zA-Z0-9.\/_-]*$/i';
        if (preg_match($urlPattern, $text)) {
            // If it's a URL, check if it contains any secret patterns
            // If it contains secrets, don't allow it
            foreach (self::$COMMON_KEY_PREFIXES as $prefix) {
                if (strpos($text, $prefix) !== false) {
                    return false;
                }
            }
            return true;
        }

        // Regex for allowed file extensions - must end with the extension
        $extensions = array_map(function($ext) { return str_replace('.', '\.', $ext); }, self::$ALLOWED_EXTENSIONS);
        $extPattern = '/^[^\\s]*(' . implode('|', $extensions) . ')$/i';
        return preg_match($extPattern, $text);
    }

    /**
     * Check if a string is a secret key using the specified criteria.
     */
    public static function isSecretCandidate($s, $cfg, $customRegex = [])
    {
        // Skip strings that are already masked by another rule
        if (self::isAlreadyMasked($s)) {
            return false;
        }

        // Check custom patterns first if provided
        if (!empty($customRegex)) {
            foreach ($customRegex as $pattern) {
                try {
                    // Ensure delimiters
                    if (strpos($pattern, '/') !== 0 && strpos($pattern, '#') !== 0 && strpos($pattern, '~') !== 0) {
                        $pattern = '/' . str_replace('/', '\/', $pattern) . '/';
                    }
                    if (@preg_match($pattern, $s)) {
                        return true;
                    }
                } catch (\Exception $e) {
                    // Invalid regex pattern, skip
                    continue;
                }
            }
        }

        if (!$cfg['strict_mode'] && self::containsAllowedPattern($s)) {
            return false;
        }

        $longEnough = mb_strlen($s) >= $cfg['min_length'];
        $diverse = self::charDiversity($s) >= $cfg['min_diversity'];

        // Check common prefixes first - these should always be detected
        foreach (self::$COMMON_KEY_PREFIXES as $prefix) {
            if (strpos($s, $prefix) === 0) {
                return true;
            }
        }

        // For other candidates, check length and diversity
        if (!($longEnough && $diverse)) {
            return false;
        }

        return self::entropy($s) >= $cfg['min_entropy'];
    }

    /**
     * Detect potential secret keys in text.
     */
    public static function detectSecretKeys($text, $cfg, $config)
    {
        $words = preg_split('/\s+/', $text);
        $words = array_map(function($w) { return str_replace(['*', '#'], '', $w); }, $words);
        
        $secrets = [];
        foreach ($words as $w) {
            if (empty($w)) continue;
            if (self::isSecretCandidate($w, $cfg, isset($config['customRegex']) ? $config['customRegex'] : [])) {
                $secrets[] = $w;
            }
        }

        return [
            'guardrailName' => 'secretKeys',
            'tripwireTriggered' => count($secrets) > 0,
            'info' => [
                'maskEntities' => ['SECRET_KEY' => $secrets],
                'detectedSecrets' => $secrets,
            ],
        ];
    }

    /**
     * Async guardrail function for secret key and credential detection.
     *
     * Scans the input for likely secrets or credentials (e.g., API keys, tokens)
     * using entropy, diversity, and pattern rules.
     *
     * @param string $data Input text to scan.
     * @param array $config Configuration for secret detection.
     * @return array GuardrailResult indicating if secrets were detected, with findings in info.
     */
    public static function check($data, $config)
    {
        $threshold = isset($config['threshold']) ? $config['threshold'] : 'balanced';
        $cfg = self::$CONFIGS[$threshold];
        return self::detectSecretKeys($data, $cfg, $config);
    }
}
