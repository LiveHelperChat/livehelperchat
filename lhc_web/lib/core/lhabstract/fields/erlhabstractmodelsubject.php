<?php

return array(
    'name' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Name'),
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'dep_id' => array(
        'type' => 'checkbox_multi',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Department'),
        'required' => false,
        'col_size' => 4,
        'hidden' => true,
        'source' => 'erLhcoreClassModelDepartament::getList',
        'params_call' => ($userDepartments === true) ? array() : array(
            'filterin' => array(
                'id' => $userDepartments
            )
        ),
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1), FILTER_REQUIRE_ARRAY)
    ),
);