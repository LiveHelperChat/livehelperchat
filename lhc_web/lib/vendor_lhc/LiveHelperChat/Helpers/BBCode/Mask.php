<?php

namespace LiveHelperChat\Helpers\BBCode;

use LiveHelperChat\Validators\Guardrails\PII;

class Mask
{
    /**
     * Entity type to category mapping
     */
    public static $ENTITY_CATEGORY_MAP = [
        // PII entities
        'CREDIT_CARD' => 'PII',
        'CRYPTO' => 'PII',
        'DATE_TIME' => 'PII',
        'EMAIL_ADDRESS' => 'PII',
        'IBAN_CODE' => 'PII',
        'IP_ADDRESS' => 'PII',
        'LOCATION' => 'PII',
        'PHONE_NUMBER' => 'PII',
        'MEDICAL_LICENSE' => 'PII',
        'US_BANK_NUMBER' => 'PII',
        'US_DRIVER_LICENSE' => 'PII',
        'US_ITIN' => 'PII',
        'US_PASSPORT' => 'PII',
        'US_SSN' => 'PII',
        'UK_NHS' => 'PII',
        'UK_NINO' => 'PII',
        'ES_NIF' => 'PII',
        'ES_NIE' => 'PII',
        'IT_FISCAL_CODE' => 'PII',
        'IT_DRIVER_LICENSE' => 'PII',
        'IT_VAT_CODE' => 'PII',
        'IT_PASSPORT' => 'PII',
        'IT_IDENTITY_CARD' => 'PII',
        'PL_PESEL' => 'PII',
        'SG_NRIC_FIN' => 'PII',
        'SG_UEN' => 'PII',
        'AU_ABN' => 'PII',
        'AU_ACN' => 'PII',
        'AU_TFN' => 'PII',
        'AU_MEDICARE' => 'PII',
        'IN_PAN' => 'PII',
        'IN_AADHAAR' => 'PII',
        'IN_VEHICLE_REGISTRATION' => 'PII',
        'IN_VOTER' => 'PII',
        'IN_PASSPORT' => 'PII',
        'FI_PERSONAL_IDENTITY_CODE' => 'PII',
        
        // URL entities
        'URL' => 'URL',

        'URL_NOT_IN_ALLOW_LIST' => 'URL',
        'URL_IN_DENY_LIST' => 'URL',
        'URL_FORMAT' => 'URL',
        'URL_USER_INFO' => 'URL',
        'URL_BLOCKED_SCHEME' => 'URL',
        
        // Secret Key entities
        'SECRET_KEY' => 'Secret Key',
     ];

    /**
     * Category to Bootstrap badge class mapping
     */
    public static $CATEGORY_BADGE_CLASS = [
        'PII' => 'text-bg-warning',
        'URL' => 'text-bg-warning',
        'Secret Key' => 'text-bg-warning',
        'Regex' => 'text-bg-warning',
    ];

    /**
     * Category to Material Icon mapping
     */
    public static $CATEGORY_ICON_MAP = [
        'PII' => 'shield_person',
        'URL' => 'link',
        'Secret Key' => 'key',
        'Regex' => 'code',
    ];

    /**
     * Get the category for an entity type
     * 
     * @param string $entityType
     * @return string
     */
    public static function getCategory($entityType)
    {
        if (isset(self::$ENTITY_CATEGORY_MAP[$entityType])) {
            return self::$ENTITY_CATEGORY_MAP[$entityType];
        }
        
        // Handle REGEX:Name format - category is Regex
        if (strpos($entityType, 'REGEX:') === 0) {
            return 'Regex';
        }
        
        // Default to Regex for custom regex patterns
        return 'Regex';
    }

    /**
     * Get the human-readable name for an entity type
     * 
     * @param string $entityType
     * @return string
     */
    public static function getEntityName($entityType)
    {
        // Check PII name map first
        if (isset(PII::$PII_NAME_MAP[$entityType])) {
            return PII::$PII_NAME_MAP[$entityType];
        }
        
        // For URL type
        if ($entityType === 'URL' || $entityType === 'SECRET_KEY') {
            return '';
        }
        
        // Handle REGEX:Name format - return the name part after REGEX:
        if (strpos($entityType, 'REGEX:') === 0) {
            return substr($entityType, 6); // Return everything after 'REGEX:'
        }
        
        // For secret key types, return a formatted name
        if (strpos($entityType, '_') !== false) {
            return str_replace('_', ' ', ucwords(strtolower($entityType), '_'));
        }
        
        return $entityType;
    }

    /**
     * Get the Bootstrap badge class for an entity type
     * 
     * @param string $entityType
     * @return string
     */
    public static function getBadgeClass($entityType)
    {
        $category = self::getCategory($entityType);
        
        if (isset(self::$CATEGORY_BADGE_CLASS[$category])) {
            return self::$CATEGORY_BADGE_CLASS[$category];
        }
        
        return 'text-bg-secondary';
    }

    /**
     * Get the Material Icon for an entity type
     * 
     * @param string $entityType
     * @return string
     */
    public static function getIcon($entityType)
    {
        $category = self::getCategory($entityType);
        
        if (isset(self::$CATEGORY_ICON_MAP[$category])) {
            return self::$CATEGORY_ICON_MAP[$category];
        }
        
        return 'label';
    }

    /**
     * Render a masked entity as HTML badge
     * 
     * @param string $entityType The entity type (e.g., CREDIT_CARD, URL, SECRET_KEY)
     * @return string HTML output
     */
    public static function render($entityType)
    {
        $category = self::getCategory($entityType);
        $entityName = self::getEntityName($entityType);
        $badgeClass = self::getBadgeClass($entityType);
        $icon = self::getIcon($entityType);
        
        $label = htmlspecialchars($category, ENT_QUOTES, 'UTF-8');
        if ($entityName !== '') {
            $label .= ': ' . htmlspecialchars($entityName, ENT_QUOTES, 'UTF-8');
        }
        
        return '<span class="badge fw-normal ' . $badgeClass . ' fs13 me-1 mb-1 pe-1 ps-1" title="'.htmlspecialchars($entityType, ENT_QUOTES, 'UTF-8').'">' 
            . '<span class="material-icons fs16 me-1">' . $icon . '</span>'
            . $label 
            . '<span title="' . \erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','Redacted field: Please contact your administrator for details').'" class="material-icons fs16 ms-1 me-0">info</span></span>';
    }
}
