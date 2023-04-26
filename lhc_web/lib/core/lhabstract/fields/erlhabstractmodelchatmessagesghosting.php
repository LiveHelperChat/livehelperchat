<?php

return array(
    'enabled' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Enabled'),
        'required' => false,
        'hide_optional' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean')
    ),
    /*'remove' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Remove permanently matched content'),
        'hidden' => true,
        'required' => false,
        'hide_optional' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean')
    ),*/
    'pattern' => array(
        'type' => 'textarea',
        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Place you replacement pattern here.'),
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Pattern for replacement'),
        'required' => false,
        'maxlength' => 255,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'v_warning' => array(
        'type' => 'textarea',
        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Message to visitor after posting sensitive information'),
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Auto reply warning if visitor is posting information to agent which does not have permission to view sensitive data.'),
        'required' => false,
        'maxlength' => 255,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    )
);

