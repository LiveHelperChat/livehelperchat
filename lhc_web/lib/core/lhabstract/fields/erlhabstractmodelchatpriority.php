<?php

return array(
    'dep_id' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Department rule to apply'),
        'required' => false,
        'frontend' => 'dep',
        'source' => 'erLhcoreClassModelDepartament::getList',
        'hide_optional' => false,
        'params_call' => array('limit' => false, 'sort' => '`name` ASC'),
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'dest_dep_id' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'To what department transfer chat'),
        'required' => false,
        'frontend' => 'dest_dep_role',
        'source' => 'erLhcoreClassModelDepartament::getList',
        'hide_optional' => false,
        'params_call' => array(),
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'value' => array(
        'type' => 'text',
        'frontend' => 'value_frontend',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Expected variable value'),
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'role_destination' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Destination department by brand role'),
        'required' => false,
        'hidden' => true,
        'list_identifier' => 'brand-role-list',
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'present_role_is' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Change department only if present department role is'),
        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Condition is always checked independently of transfer method. Department or Department brand role.'),
        'required' => false,
        'hidden' => true,
        'list_identifier' => 'brand-role-list',
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'priority' => array(
        'type' => 'text',
        'placeholder' => 'Chat priority to set',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Set chat priority to'),
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'sort_priority' => array(
        'type' => 'text',
        'placeholder' => 'If multiple priority rules will be found for the same department. The higher number will be executed first.',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Rule priority'),
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    )
);

