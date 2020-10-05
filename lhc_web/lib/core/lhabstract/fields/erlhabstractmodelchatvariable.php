<?php

return array(
    'dep_id' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Department'),
        'required' => false,
        'frontend' => 'dep',
        'source' => 'erLhcoreClassModelDepartament::getList',
        'hide_optional' => false,
        'params_call' => array(),
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'js_variable' => array(
        'type' => 'text',
        'placeholder' => 'window.lhc_var.username',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Javascript variable value'),
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'var_name' => array(
        'type' => 'text',
        'placeholder' => 'Username',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Variable name'),
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'var_identifier' => array(
        'type' => 'text',
        'placeholder' => 'username',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Variable identifier'),
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'change_message' => array(
        'type' => 'text',
        'hidden' => true,
        'maxlength' => 250,
        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Variable changed from [b]{old_val}[/b] to [b]{new_val}[/b]'),
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Log message for for variable. Variables you can use {old_val}, {new_val}'),
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'persistent' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'If variable is not passed should we keep previously recorded value?'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'inv' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'This variable is invisible for operator and will be stored in chat_variables attribute'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'type' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Variable type'),
        'required' => false,
        'hide_optional' => true,
        'name_attr' => 'name',
        'params_call' => array(),
        'source' => 'erLhAbstractModelChatVariable::getDataTypes',
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    )
);

