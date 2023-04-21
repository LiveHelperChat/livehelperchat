<?php

return array(
    'enabled' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Enabled'),
        'required' => false,
        'hide_optional' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean')
    ),
    'remove' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Remove permanently matched content'),
        'hidden' => true,
        'required' => false,
        'hide_optional' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean')
    ),
    'pattern' => array(
        'type' => 'textarea',
        'placeholder' => "Place you replacement pattern here.",
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Pattern for replacement'),
        'required' => false,
        'maxlength' => 255,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    )
);

