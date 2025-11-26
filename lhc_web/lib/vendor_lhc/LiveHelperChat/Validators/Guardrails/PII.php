<?php
// Source https://github.com/n8n-io/n8n/tree/master/packages/%40n8n/nodes-langchain/nodes/Guardrails/actions/checks
namespace LiveHelperChat\Validators\Guardrails;

class PII
{
    // Global
    const CREDIT_CARD = 'CREDIT_CARD';
    const CRYPTO = 'CRYPTO';
    const DATE_TIME = 'DATE_TIME';
    const EMAIL_ADDRESS = 'EMAIL_ADDRESS';
    const IBAN_CODE = 'IBAN_CODE';
    const IP_ADDRESS = 'IP_ADDRESS';
    const LOCATION = 'LOCATION';
    const PHONE_NUMBER = 'PHONE_NUMBER';
    const MEDICAL_LICENSE = 'MEDICAL_LICENSE';

    // USA
    const US_BANK_NUMBER = 'US_BANK_NUMBER';
    const US_DRIVER_LICENSE = 'US_DRIVER_LICENSE';
    const US_ITIN = 'US_ITIN';
    const US_PASSPORT = 'US_PASSPORT';
    const US_SSN = 'US_SSN';

    // UK
    const UK_NHS = 'UK_NHS';
    const UK_NINO = 'UK_NINO';

    // Spain
    const ES_NIF = 'ES_NIF';
    const ES_NIE = 'ES_NIE';

    // Italy
    const IT_FISCAL_CODE = 'IT_FISCAL_CODE';
    const IT_DRIVER_LICENSE = 'IT_DRIVER_LICENSE';
    const IT_VAT_CODE = 'IT_VAT_CODE';
    const IT_PASSPORT = 'IT_PASSPORT';
    const IT_IDENTITY_CARD = 'IT_IDENTITY_CARD';

    // Poland
    const PL_PESEL = 'PL_PESEL';

    // Singapore
    const SG_NRIC_FIN = 'SG_NRIC_FIN';
    const SG_UEN = 'SG_UEN';

    // Australia
    const AU_ABN = 'AU_ABN';
    const AU_ACN = 'AU_ACN';
    const AU_TFN = 'AU_TFN';
    const AU_MEDICARE = 'AU_MEDICARE';

    // India
    const IN_PAN = 'IN_PAN';
    const IN_AADHAAR = 'IN_AADHAAR';
    const IN_VEHICLE_REGISTRATION = 'IN_VEHICLE_REGISTRATION';
    const IN_VOTER = 'IN_VOTER';
    const IN_PASSPORT = 'IN_PASSPORT';

    // Finland
    const FI_PERSONAL_IDENTITY_CODE = 'FI_PERSONAL_IDENTITY_CODE';

    public static $PII_NAME_MAP = [
        self::CREDIT_CARD => 'Credit Card',
        self::CRYPTO => 'Crypto',
        self::DATE_TIME => 'Date Time',
        self::EMAIL_ADDRESS => 'Email Address',
        self::IBAN_CODE => 'IBAN Code',
        self::IP_ADDRESS => 'IP Address',
        self::LOCATION => 'Location',
        self::PHONE_NUMBER => 'Phone Number',
        self::MEDICAL_LICENSE => 'Medical License',
        self::US_BANK_NUMBER => 'US Bank Number',
        self::US_DRIVER_LICENSE => 'US Driver License',
        self::US_ITIN => 'US ITIN',
        self::US_PASSPORT => 'US Passport',
        self::US_SSN => 'US SSN',
        self::UK_NHS => 'UK NHS',
        self::UK_NINO => 'UK NINO',
        self::ES_NIF => 'ES NIF',
        self::ES_NIE => 'ES NIE',
        self::IT_FISCAL_CODE => 'IT Fiscal Code',
        self::IT_DRIVER_LICENSE => 'IT Driver License',
        self::IT_VAT_CODE => 'IT VAT Code',
        self::IT_PASSPORT => 'IT Passport',
        self::IT_IDENTITY_CARD => 'IT Identity Card',
        self::PL_PESEL => 'PL PESEL',
        self::SG_NRIC_FIN => 'SG NRIC FIN',
        self::SG_UEN => 'SG UEN',
        self::AU_ABN => 'AU ABN',
        self::AU_ACN => 'AU ACN',
        self::AU_TFN => 'AU TFN',
        self::AU_MEDICARE => 'AU Medicare',
        self::IN_PAN => 'IN PAN',
        self::IN_AADHAAR => 'IN AADHAAR',
        self::IN_VEHICLE_REGISTRATION => 'IN Vehicle Registration',
        self::IN_VOTER => 'IN Voter',
        self::IN_PASSPORT => 'IN Passport',
        self::FI_PERSONAL_IDENTITY_CODE => 'FI Personal Identity Code',
    ];

    public static $DEFAULT_PII_PATTERNS = [
        self::CREDIT_CARD => '/\b\d{4}[-\s]?\d{4}[-\s]?\d{4}[-\s]?\d{4}\b/',
        self::CRYPTO => '/\b[13][a-km-zA-HJ-NP-Z1-9]{25,34}\b/',
        self::DATE_TIME => '/\b(0[1-9]|1[0-2])[\/\-](0[1-9]|[12]\d|3[01])[\/\-](19|20)\d{2}\b/',
        self::EMAIL_ADDRESS => '/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/',
        self::IBAN_CODE => '/\b[A-Z]{2}[0-9]{2}[A-Z0-9]{4}[0-9]{7}([A-Z0-9]?){0,16}\b/',
        self::IP_ADDRESS => '/\b(?:[0-9]{1,3}\.){3}[0-9]{1,3}\b/',
        self::LOCATION => '/\b[A-Za-z\s]+(?:Street|St|Avenue|Ave|Road|Rd|Boulevard|Blvd|Drive|Dr|Lane|Ln|Place|Pl|Court|Ct|Way|Highway|Hwy)\b/',
        self::PHONE_NUMBER => '/\b[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}\b/',
        self::MEDICAL_LICENSE => '/\b[A-Z]{2}\d{6}\b/',

        // USA
        self::US_BANK_NUMBER => '/\b\d{8,17}\b/',
        self::US_DRIVER_LICENSE => '/\b[A-Z]\d{7}\b/',
        self::US_ITIN => '/\b9\d{2}-\d{2}-\d{4}\b/',
        self::US_PASSPORT => '/\b[A-Z]\d{8}\b/',
        self::US_SSN => '/\b\d{3}-\d{2}-\d{4}\b|\b\d{9}\b/',

        // UK
        self::UK_NHS => '/\b\d{3} \d{3} \d{4}\b/',
        self::UK_NINO => '/\b[A-Z]{2}\d{6}[A-Z]\b/',

        // Spain
        self::ES_NIF => '/\b[A-Z]\d{8}\b/',
        self::ES_NIE => '/\b[A-Z]\d{8}\b/',

        // Italy
        self::IT_FISCAL_CODE => '/\b[A-Z]{6}\d{2}[A-Z]\d{2}[A-Z]\d{3}[A-Z]\b/',
        self::IT_DRIVER_LICENSE => '/\b[A-Z]{2}\d{7}\b/',
        self::IT_VAT_CODE => '/\bIT\d{11}\b/',
        self::IT_PASSPORT => '/\b[A-Z]{2}\d{7}\b/',
        self::IT_IDENTITY_CARD => '/\b[A-Z]{2}\d{7}\b/',

        // Poland
        self::PL_PESEL => '/\b\d{11}\b/',

        // Singapore
        self::SG_NRIC_FIN => '/\b[A-Z]\d{7}[A-Z]\b/',
        self::SG_UEN => '/\b\d{8}[A-Z]\b|\b\d{9}[A-Z]\b/',

        // Australia
        self::AU_ABN => '/\b\d{2} \d{3} \d{3} \d{3}\b/',
        self::AU_ACN => '/\b\d{3} \d{3} \d{3}\b/',
        self::AU_TFN => '/\b\d{9}\b/',
        self::AU_MEDICARE => '/\b\d{4} \d{5} \d{1}\b/',

        // India
        self::IN_PAN => '/\b[A-Z]{5}\d{4}[A-Z]\b/',
        self::IN_AADHAAR => '/\b\d{4} \d{4} \d{4}\b/',
        self::IN_VEHICLE_REGISTRATION => '/\b[A-Z]{2}\d{2}[A-Z]{2}\d{4}\b/',
        self::IN_VOTER => '/\b[A-Z]{3}\d{7}\b/',
        self::IN_PASSPORT => '/\b[A-Z]\d{7}\b/',

        // Finland
        self::FI_PERSONAL_IDENTITY_CODE => '/\b\d{6}[+-A]\d{3}[A-Z0-9]\b/',
    ];

    public static function detectPii($text, $config = [])
    {
        if (empty($text)) {
            return [
                'mapping' => [],
                'analyzerResults' => []
            ];
        }

        $grouped = [];
        $analyzerResults = [];

        $matchAgainstPattern = function($name, $pattern) use (&$grouped, &$analyzerResults, $text) {
            if (preg_match_all($pattern, $text, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches[0] as $match) {
                    $entityType = $name;
                    $matchedText = $match[0];
                    
                    if (!isset($grouped[$entityType])) {
                        $grouped[$entityType] = [];
                    }
                    $grouped[$entityType][] = $matchedText;

                    $analyzerResults[] = [
                        'entityType' => $entityType,
                        'text' => $matchedText
                    ];
                }
            }
        };

        $entities = isset($config['entities']) ? $config['entities'] : array_keys(self::$PII_NAME_MAP);

        foreach ($entities as $entity) {
            if (isset(self::$DEFAULT_PII_PATTERNS[$entity])) {
                $matchAgainstPattern($entity, self::$DEFAULT_PII_PATTERNS[$entity]);
            }
        }

        if (isset($config['customRegex']) && is_array($config['customRegex'])) {
            foreach ($config['customRegex'] as $regex) {
                $pattern = $regex['value'];
                if (strpos($pattern, '/') !== 0 && strpos($pattern, '#') !== 0 && strpos($pattern, '~') !== 0) {
                     $pattern = '/' . str_replace('/', '\/', $pattern) . '/';
                }
                $matchAgainstPattern($regex['name'], $pattern);
            }
        }

        return [
            'mapping' => $grouped,
            'analyzerResults' => $analyzerResults
        ];
    }

    public static function check($input, $config = [])
    {
        $detection = self::detectPii($input, $config);
        $piiFound = !empty($detection['mapping']);

        return [
            'guardrailName' => 'personalData',
            'tripwireTriggered' => $piiFound,
            'info' => [
                'maskEntities' => $detection['mapping'],
                'analyzerResults' => $detection['analyzerResults']
            ]
        ];
    }
    
    public static function checkCustomRegex($input, $config = [])
    {
        $detection = self::detectPii($input, ['customRegex' => isset($config['customRegex']) ? $config['customRegex'] : [], 'entities' => []]);
        $customRegexFound = !empty($detection['mapping']);

        return [
            'guardrailName' => 'customRegex',
            'tripwireTriggered' => $customRegexFound,
            'info' => [
                'maskEntities' => $detection['mapping'],
                'analyzerResults' => $detection['analyzerResults']
            ]
        ];
    }
}
