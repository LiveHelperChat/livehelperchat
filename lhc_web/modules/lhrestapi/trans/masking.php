<?php

$transItems = array(
    // Rule types
    "masking.type_regex" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection', 'Regex'),
    "masking.type_email" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection', 'E-mail'),
    "masking.type_credit_card" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection', 'Credit Card'),
    "masking.type_pii" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection', 'PII'),
    "masking.type_secret_keys" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection', 'Secret Keys'),
    "masking.type_urls" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection', 'URLs'),

    // Common labels
    "masking.delete" => erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons', 'Delete'),
    "masking.add_rule" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection', 'Add Rule'),
    "masking.pattern" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection', 'Pattern'),
    "masking.replacement" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection', 'Replacement'),
    "masking.replacement_optional" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection', 'Replacement (optional)'),
    "masking.replacement_domain" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection', 'Replacement Domain'),
    "masking.replacement_mask_hint" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection', 'Replacement (leave empty to use [mask] tags)'),
    "masking.entities" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection', 'Entities'),
    "masking.name" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection', 'Name'),

    // Secret keys
    "masking.threshold" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection', 'Threshold'),
    "masking.threshold_strict" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection', 'Strict'),
    "masking.threshold_balanced" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection', 'Balanced'),
    "masking.threshold_permissive" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection', 'Permissive'),

    // Placeholders
    "masking.placeholder_replacement" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection', 'e.g. * or leave empty for badge'),
    "masking.placeholder_name" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection', 'e.g. Phone Number'),

    // URLs
    "masking.block_user_info" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection', 'Block User Info'),
    "masking.allow_subdomains" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection', 'Allow Subdomains'),
    "masking.allow_hosted_host" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection', 'Allow host where chat is hosted. Required for images and other media stored on the server to load properly.'),
    "masking.allow_list" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection', 'Allow List (one per line). All other URLs will be blocked.'),
    "masking.deny_list" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection', 'Deny List (one per line). All other URLs will be allowed.'),
    "masking.list_exclusive_note" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection', 'Note: Only one list can be filled at a time. Allow List and Deny List are mutually exclusive.'),
    "masking.email_domain_allow_list" => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection', 'Email Domain Allow List (one per line). Use __mailbox__ to allow all mailbox domains'),
);

echo json_encode($transItems);
