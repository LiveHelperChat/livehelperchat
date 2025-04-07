<?php

$fieldsSearch = array();

$fieldsSearch['category'] = array (
    'type' => 'text',
    'trans' => 'User',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filterin',
    'filter_table_field' => 'category',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'string', array(), FILTER_REQUIRE_ARRAY
    ),
    'valid_options' => array (
        'chat_open',
        'chat_view',
        'chat_search',
        'chat_export',
        'chat_export_elastic',
        'mail_open',
        'mail_view',
        'mail_search',
        'mail_export',
        'mail_export_elastic',
    )
);

$fieldsSearch['sortby'] = array (
    'type' => 'text',
    'trans' => 'Sort by',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => false,
    'filter_table_field' => 'user_id',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'string')
);

$fieldsSearch['timefrom'] = array (
    'type' => 'text',
    'trans' => 'Timefrom',
    'required' => false,
    'valid_if_filled' => false,
    'datatype' => 'datetime',
    'datetype_sub' => 'mysql_ts',
    'filter_type' => 'filtergtenbind',
    'filter_table_field' => 'time',
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'string'
    )
);

$fieldsSearch['timeto'] = array (
    'type' => 'text',
    'trans' => 'Timeto',
    'required' => false,
    'valid_if_filled' => false,
    'datatype' => 'datetime',
    'datetype_sub' => 'mysql_ts',
    'filter_type' => 'filterltenbind',
    'filter_table_field' => 'time',
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'string'
    )
);

$fieldSortAttr = array (
    'field'      => 'sortby',
    'default'    => 'iddesc',
    'serialised' => true,
    'options'    => array(
        'iddesc' => array('sort_column' => '`lh_audits`.`id` DESC'),
        'idasc' => array('sort_column' => '`lh_audits`.`id` ASC')
    )
);

return array(
    'filterAttributes' => $fieldsSearch,
    'sortAttributes'   => $fieldSortAttr
);