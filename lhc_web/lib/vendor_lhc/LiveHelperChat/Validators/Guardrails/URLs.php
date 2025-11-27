<?php
// Source https://github.com/n8n-io/n8n/tree/master/packages/%40n8n/nodes-langchain/nodes/Guardrails/actions/checks
namespace LiveHelperChat\Validators\Guardrails;

class URLs
{
    // URL block reason constants
    const REASON_NOT_IN_ALLOW_LIST = 'URL_NOT_IN_ALLOW_LIST';//Not in allow list';
    const REASON_IN_DENY_LIST = 'URL_IN_DENY_LIST';//In deny list';
    const REASON_INVALID_URL_FORMAT = 'URL_FORMAT';//Invalid URL format';
    const REASON_CONTAINS_USERINFO = 'URL_USER_INFO';//Contains userinfo';
    const REASON_BLOCKED_SCHEME = 'URL_BLOCKED_SCHEME';//Blocked scheme';

    /**
     * Convert IPv4 address string to 32-bit integer for CIDR calculations.
     */
    private static function ipToInt($ip)
    {
        $long = ip2long($ip);
        if ($long === false) {
            throw new \Exception("Invalid IP address: {$ip}");
        }
        return $long;
    }

    /**
     * Detect URLs in text using robust regex patterns.
     */
    private static function detectUrls($text)
    {
        // Pattern for cleaning trailing punctuation (] must be escaped)
        $PUNCTUATION_CLEANUP = '/[.,;:!?)\\]]+$/';

        $detectedUrls = [];

        // Pattern 1: URLs with schemes (highest priority)
        $schemePatterns = [
            '/https?:\/\/[^\s<>"{}|\\\\^`\[\]]+/i',
            '/ftp:\/\/[^\s<>"{}|\\\\^`\[\]]+/i',
            '/data:[^\s<>"{}|\\\\^`\[\]]+/i',
            '/javascript:[^\s<>"{}|\\\\^`\[\]]+/i',
            '/vbscript:[^\s<>"{}|\\\\^`\[\]]+/i',
            '/mailto:[^\s<>"{}|\\\\^`\[\]]+/i',
        ];

        $schemeUrls = []; // Set equivalent
        foreach ($schemePatterns as $pattern) {
            preg_match_all($pattern, $text, $matches);
            foreach ($matches[0] as $match) {
                // Clean trailing punctuation
                $match = preg_replace($PUNCTUATION_CLEANUP, '', $match);
                if ($match) {
                    $detectedUrls[] = $match;
                    // Track the domain part to avoid duplicates
                    if (strpos($match, '://') !== false) {
                        $parts = explode('://', $match, 2);
                        $domainPart = explode('/', $parts[1])[0];
                        $domainPart = explode('?', $domainPart)[0];
                        $domainPart = explode('#', $domainPart)[0];
                        $schemeUrls[strtolower($domainPart)] = true;
                    }
                }
            }
        }

        // Pattern 2: Domain-like patterns without schemes (exclude already found)
        $domainPattern = '/\b(?:www\.)?[a-zA-Z0-9][a-zA-Z0-9.-]*\.[a-zA-Z]{2,}(?:\/[^\s]*)?/i';
        preg_match_all($domainPattern, $text, $domainMatches, PREG_OFFSET_CAPTURE);

        foreach ($domainMatches[0] as $matchInfo) {
            $match = $matchInfo[0];
            $offset = $matchInfo[1];

            // Check if preceded by @
            if ($offset > 0 && $text[$offset - 1] === '@') {
                continue;
            }

            // Clean trailing punctuation
            $match = preg_replace($PUNCTUATION_CLEANUP, '', $match);
            if ($match) {
                // Extract just the domain part for comparison
                $domainPart = explode('/', $match)[0];
                $domainPart = explode('?', $domainPart)[0];
                $domainPart = explode('#', $domainPart)[0];
                $domainPart = strtolower($domainPart);
                // Only add if we haven't already found this domain with a scheme
                if (!isset($schemeUrls[$domainPart])) {
                    $detectedUrls[] = $match;
                }
            }
        }

        // Pattern 3: IP addresses (exclude already found)
        $ipPattern = '/\b(?:[0-9]{1,3}\.){3}[0-9]{1,3}(?::[0-9]+)?(?:\/[^\s]*)?/';
        preg_match_all($ipPattern, $text, $ipMatches);

        foreach ($ipMatches[0] as $match) {
            // Clean trailing punctuation
            $match = preg_replace($PUNCTUATION_CLEANUP, '', $match);
            if ($match) {
                // Extract IP part for comparison
                $ipPart = explode('/', $match)[0];
                $ipPart = explode('?', $ipPart)[0];
                $ipPart = explode('#', $ipPart)[0];
                $ipPart = strtolower($ipPart);
                if (!isset($schemeUrls[$ipPart])) {
                    $detectedUrls[] = $match;
                }
            }
        }

        // Advanced deduplication: Remove domains that are already part of full URLs
        $finalUrls = [];
        $schemeUrlDomains = [];

        // First pass: collect all domains from scheme-ful URLs
        foreach ($detectedUrls as $url) {
            if (strpos($url, '://') !== false) {
                try {
                    $parsed = parse_url($url);
                    if (isset($parsed['host'])) {
                        $schemeUrlDomains[strtolower($parsed['host'])] = true;
                        // Also add www-stripped version
                        $bareDomain = preg_replace('/^www\./', '', strtolower($parsed['host']));
                        $schemeUrlDomains[$bareDomain] = true;
                    }
                } catch (\Exception $error) {
                    // Skip URLs with parsing errors
                }
                $finalUrls[] = $url;
            }
        }

        // Second pass: only add scheme-less URLs if their domain isn't already covered
        foreach ($detectedUrls as $url) {
            if (strpos($url, '://') === false) {
                // Check if this domain is already covered by a full URL
                $urlLower = preg_replace('/^www\./', '', strtolower($url));
                if (!isset($schemeUrlDomains[$urlLower])) {
                    $finalUrls[] = $url;
                }
            }
        }

        // Remove empty URLs and return unique list
        return array_values(array_unique(array_filter($finalUrls)));
    }

    /**
     * Validate URL against security configuration.
     */
    private static function validateUrlSecurity($urlString, $config)
    {
        try {
            $parsedUrl = null;
            $originalScheme = '';

            // Parse URL - preserve original scheme for validation
            if (strpos($urlString, '://') !== false) {
                // Standard URL with double-slash scheme (http://, https://, ftp://, etc.)
                $parsedUrl = parse_url($urlString);
                if (isset($parsedUrl['scheme'])) {
                    $originalScheme = $parsedUrl['scheme'];
                }
            } elseif (strpos($urlString, ':') !== false && preg_match('/^(data|javascript|vbscript|mailto)$/', explode(':', $urlString, 2)[0])) {
                // Special single-colon schemes
                $parsedUrl = parse_url($urlString);
                if (isset($parsedUrl['scheme'])) {
                    $originalScheme = $parsedUrl['scheme'];
                }
            } else {
                // Add http scheme for parsing, but remember this is a default
                $parsedUrl = parse_url('http://' . $urlString);
                $originalScheme = 'http'; // Default scheme for scheme-less URLs
            }

            // Basic validation: must have scheme and hostname (except for special schemes)
            if (!isset($parsedUrl['scheme']) && $originalScheme === '') {
                 return ['parsedUrl' => null, 'reason' => self::REASON_INVALID_URL_FORMAT];
            }
            
            // Special schemes like data: and javascript: don't need hostname
            $specialSchemes = ['data', 'javascript', 'vbscript', 'mailto'];
            if (!in_array($originalScheme, $specialSchemes) && !isset($parsedUrl['host'])) {
                return ['parsedUrl' => null, 'reason' => self::REASON_INVALID_URL_FORMAT];
            }

            // Security validations - use original scheme
            if (!in_array($originalScheme, $config['allowedSchemes'])) {
                return ['parsedUrl' => null, 'reason' => self::REASON_BLOCKED_SCHEME];
            }

            if ($config['blockUserinfo'] && (isset($parsedUrl['user']) || isset($parsedUrl['pass']))) {
                return ['parsedUrl' => null, 'reason' => self::REASON_CONTAINS_USERINFO];
            }

            // Everything else (IPs, localhost, private IPs) goes through allow list logic
            return ['parsedUrl' => $parsedUrl, 'reason' => ''];
        } catch (\Exception $error) {
            // Provide specific error information for debugging
            return ['parsedUrl' => null, 'reason' => self::REASON_INVALID_URL_FORMAT];
        }
    }

    /**
     * Check if URL is allowed based on the allow list configuration.
     */
    private static function isUrlAllowed($parsedUrl, $allowList, $allowSubdomains)
    {
        if (empty($allowList)) {
            return false;
        }

        $urlHost = isset($parsedUrl['host']) ? strtolower($parsedUrl['host']) : null;
        if (!$urlHost) {
            return false;
        }

        foreach ($allowList as $allowedEntry) {
            $entry = strtolower(trim($allowedEntry));

            // Handle full URLs with specific paths
            if (strpos($entry, '://') !== false) {
                try {
                    $allowedUrl = parse_url($entry);
                    $allowedHost = isset($allowedUrl['host']) ? strtolower($allowedUrl['host']) : null;
                    $allowedPath = isset($allowedUrl['path']) ? $allowedUrl['path'] : null;

                    if ($urlHost === $allowedHost) {
                        // Check if the URL path starts with the allowed path
                        $currentPath = isset($parsedUrl['path']) ? $parsedUrl['path'] : '/';
                        if (!$allowedPath || $allowedPath === '/' || strpos($currentPath, $allowedPath) === 0) {
                            return true;
                        }
                    }
                } catch (\Exception $error) {
                    throw new \Exception("Invalid URL in allow list: \"{$entry}\" - " . $error->getMessage());
                }
                continue;
            }

            // Handle IP addresses and CIDR blocks
            try {
                // Basic IP pattern check
                $entryParts = explode('/', $entry);
                if (preg_match('/^\d+\.\d+\.\d+\.\d+/', $entryParts[0])) {
                    if ($entry === $urlHost) {
                        return true;
                    }
                    // Proper CIDR validation
                    if (strpos($entry, '/') !== false && preg_match('/^\d+\.\d+\.\d+\.\d+$/', $urlHost)) {
                        list($network, $prefixStr) = explode('/', $entry);
                        $prefix = intval($prefixStr);

                        if ($prefix >= 0 && $prefix <= 32) {
                            // Convert IPs to 32-bit integers for bitwise comparison
                            $networkInt = self::ipToInt($network);
                            $hostInt = self::ipToInt($urlHost);

                            // Create subnet mask
                            $mask = -1 << (32 - $prefix);
                            
                            if (($networkInt & $mask) === ($hostInt & $mask)) {
                                return true;
                            }
                        }
                    }
                    continue;
                }
            } catch (\Exception $error) {
                // Expected: entry is not an IP address/CIDR, continue to domain matching
                if (preg_match('/^\d+\.\d+/', $entry)) {
                    // error_log("Warning: Malformed IP address in allow list: \"{$entry}\" - " . $error->getMessage());
                }
            }

            // Handle domain matching
            $allowedDomain = preg_replace('/^www\./', '', $entry);
            $urlDomain = preg_replace('/^www\./', '', $urlHost);

            // Exact match always allowed
            if ($urlDomain === $allowedDomain) {
                return true;
            }

            // Subdomain matching if enabled
            if ($allowSubdomains && substr($urlDomain, -strlen('.' . $allowedDomain)) === '.' . $allowedDomain) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if URL is in the deny list.
     */
    private static function isUrlDenied($parsedUrl, $denyList, $allowSubdomains)
    {
        if (empty($denyList)) {
            return false;
        }

        $urlHost = isset($parsedUrl['host']) ? strtolower($parsedUrl['host']) : null;
        if (!$urlHost) {
            return false;
        }

        foreach ($denyList as $deniedEntry) {
            $entry = strtolower(trim($deniedEntry));

            // Handle full URLs with specific paths
            if (strpos($entry, '://') !== false) {
                try {
                    $deniedUrl = parse_url($entry);
                    $deniedHost = isset($deniedUrl['host']) ? strtolower($deniedUrl['host']) : null;
                    $deniedPath = isset($deniedUrl['path']) ? $deniedUrl['path'] : null;

                    if ($urlHost === $deniedHost) {
                        // Check if the URL path starts with the denied path
                        $currentPath = isset($parsedUrl['path']) ? $parsedUrl['path'] : '/';
                        if (!$deniedPath || $deniedPath === '/' || strpos($currentPath, $deniedPath) === 0) {
                            return true;
                        }
                    }
                } catch (\Exception $error) {
                    throw new \Exception("Invalid URL in deny list: \"{$entry}\" - " . $error->getMessage());
                }
                continue;
            }

            // Handle IP addresses and CIDR blocks
            try {
                // Basic IP pattern check
                $entryParts = explode('/', $entry);
                if (preg_match('/^\d+\.\d+\.\d+\.\d+/', $entryParts[0])) {
                    if ($entry === $urlHost) {
                        return true;
                    }
                    // Proper CIDR validation
                    if (strpos($entry, '/') !== false && preg_match('/^\d+\.\d+\.\d+\.\d+$/', $urlHost)) {
                        list($network, $prefixStr) = explode('/', $entry);
                        $prefix = intval($prefixStr);

                        if ($prefix >= 0 && $prefix <= 32) {
                            // Convert IPs to 32-bit integers for bitwise comparison
                            $networkInt = self::ipToInt($network);
                            $hostInt = self::ipToInt($urlHost);

                            // Create subnet mask
                            $mask = -1 << (32 - $prefix);
                            
                            if (($networkInt & $mask) === ($hostInt & $mask)) {
                                return true;
                            }
                        }
                    }
                    continue;
                }
            } catch (\Exception $error) {
                // Expected: entry is not an IP address/CIDR, continue to domain matching
                if (preg_match('/^\d+\.\d+/', $entry)) {
                    // error_log("Warning: Malformed IP address in deny list: \"{$entry}\" - " . $error->getMessage());
                }
            }

            // Handle domain matching
            $deniedDomain = preg_replace('/^www\./', '', $entry);
            $urlDomain = preg_replace('/^www\./', '', $urlHost);

            // Exact match - denied
            if ($urlDomain === $deniedDomain) {
                return true;
            }

            // Subdomain matching if enabled
            if ($allowSubdomains && substr($urlDomain, -strlen('.' . $deniedDomain)) === '.' . $deniedDomain) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if URL matches the hosted host.
     */
    private static function isHostedHost($parsedUrl)
    {
        // Check if HTTP_HOST is available
        if (!isset($_SERVER['HTTP_HOST']) || empty($_SERVER['HTTP_HOST'])) {
            return false;
        }

        $urlHost = isset($parsedUrl['host']) ? strtolower($parsedUrl['host']) : null;
        if (!$urlHost) {
            return false;
        }

        $hostedHost = strtolower($_SERVER['HTTP_HOST']);
        
        // Remove port from hosted host if present
        if (strpos($hostedHost, ':') !== false) {
            $hostedHost = explode(':', $hostedHost)[0];
        }

        // Remove www. prefix for comparison
        $urlDomain = preg_replace('/^www\./', '', $urlHost);
        $hostedDomain = preg_replace('/^www\./', '', $hostedHost);

        return $urlDomain === $hostedDomain;
    }

    /**
     * Main URL filtering function.
     */
    public static function urls($data, $config)
    {
        // Detect URLs in the text
        $detectedUrls = self::detectUrls($data);

        $allowed = [];
        $blocked = [];
        $blockedReasons = [];

        foreach ($detectedUrls as $urlString) {
            // Validate URL with security checks
            $result = self::validateUrlSecurity($urlString, $config);
            $parsedUrl = $result['parsedUrl'];
            $reason = $result['reason'];

            if ($parsedUrl === null) {
                $blocked[] = $urlString;
                $blockedReasons[] = $reason;
                continue;
            }

            // Check against allow list
            // Special schemes (data:, javascript:, mailto:) don't have meaningful hosts
            // so they only need scheme validation, not host-based allow list checking
            $hostlessSchemes = ['data', 'javascript', 'vbscript', 'mailto'];
            $scheme = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] : '';
            
            if (in_array($scheme, $hostlessSchemes)) {
                // For hostless schemes, only scheme permission matters (no allow list needed)
                // They were already validated for scheme permission in validateUrlSecurity
                $allowed[] = $urlString;
            } else {
                // Check if allowHostedHost is enabled and URL matches the hosted host
                $allowHostedHost = isset($config['allowHostedHost']) && $config['allowHostedHost'] === true;
                if ($allowHostedHost && self::isHostedHost($parsedUrl)) {
                    $allowed[] = $urlString;
                    continue;
                }

                // Get allow list and deny list
                $allowList = isset($config['allowList']) ? $config['allowList'] : (isset($config['allowedUrls']) ? $config['allowedUrls'] : []);
                $denyList = isset($config['denyList']) ? $config['denyList'] : [];
                $allowSubdomains = isset($config['allowSubdomains']) ? $config['allowSubdomains'] : false;

                // Determine mode based on which list is populated
                $hasAllowList = !empty($allowList);
                $hasDenyList = !empty($denyList);

                if ($hasDenyList && !$hasAllowList) {
                    // Deny list mode: block URLs in deny list, allow all others
                    if (self::isUrlDenied($parsedUrl, $denyList, $allowSubdomains)) {
                        $blocked[] = $urlString;
                        $blockedReasons[] = self::REASON_IN_DENY_LIST;
                    } else {
                        $allowed[] = $urlString;
                    }
                } elseif ($hasAllowList) {
                    // Allow list mode: allow only URLs in allow list, block all others
                    if (self::isUrlAllowed($parsedUrl, $allowList, $allowSubdomains)) {
                        $allowed[] = $urlString;
                    } else {
                        $blocked[] = $urlString;
                        $blockedReasons[] = self::REASON_NOT_IN_ALLOW_LIST;
                    }
                } else {
                    // No lists configured - block all URLs
                    $blocked[] = $urlString;
                    $blockedReasons[] = self::REASON_NOT_IN_ALLOW_LIST;
                }
            }
        }

        $tripwireTriggered = count($blocked) > 0;

        return [
            'guardrailName' => 'urls',
            'tripwireTriggered' => $tripwireTriggered,
            'info' => [
                'maskEntities' => [
                    'URL' => $blocked,
                ],
                'detected' => $detectedUrls,
                'allowed' => $allowed,
                'blocked' => $blocked,
                'blockedReasons' => $blockedReasons,
            ],
        ];
    }
}
