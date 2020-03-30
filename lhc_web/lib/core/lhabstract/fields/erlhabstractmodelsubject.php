<?php

$departmentFilterdefault = erLhcoreClassUserDep::conditionalDepartmentFilter();

return array(
    'name' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Name'),
        'required' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'dep_id' => array(
        'type' => 'checkbox_multi',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Department'),
        'required' => !empty($departmentFilterdefault),
        'col_size' => 4,
        'hidden' => true,
        'source' => 'erLhcoreClassModelDepartament::getList',
        'params_call' => $departmentFilterdefault,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1), FILTER_REQUIRE_ARRAY)
    ),
);