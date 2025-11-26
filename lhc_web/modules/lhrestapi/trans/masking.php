<?php

$transItems = array(
    // Rule types
    "masking.type_regex" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Regex'),
    "masking.type_email" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'E-mail'),
    "masking.type_credit_card" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Credit Card'),
    "masking.type_pii" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'PII'),
    "masking.type_secret_keys" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Secret Keys'),
    "masking.type_urls" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'URLs'),

    // Common labels
    "masking.delete" => erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons', 'Delete'),
    "masking.add_rule" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Add Rule'),
    "masking.pattern" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Pattern'),
    "masking.replacement" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Replacement'),
    "masking.replacement_domain" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Replacement Domain'),
    "masking.replacement_mask_hint" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Replacement (leave empty to use [mask] tags)'),
    "masking.entities" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Entities'),

    // Secret keys
    "masking.threshold" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Threshold'),
    "masking.threshold_strict" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Strict'),
    "masking.threshold_balanced" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Balanced'),
    "masking.threshold_permissive" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Permissive'),

    // URLs
    "masking.block_user_info" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Block User Info'),
    "masking.allow_subdomains" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Allow Subdomains'),
    "masking.allow_hosted_host" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Allow host where chat is hosted. Required for images and other media stored on the server to load properly.'),
    "masking.allow_list" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Allow List (one per line). All other URLs will be blocked.'),
    "masking.deny_list" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Deny List (one per line). All other URLs will be allowed.'),
    "masking.list_exclusive_note" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Note: Only one list can be filled at a time. Allow List and Deny List are mutually exclusive.'),
);

echo json_encode($transItems);
