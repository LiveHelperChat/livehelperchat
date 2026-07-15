<?php

$fieldsSearch = array();

$fieldsSearch['department_ids'] = array(
    'type' => 'text',
    'trans' => 'Department',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filter',
    'filter_table_field' => '`lh_chat`.`dep_id`',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0), FILTER_REQUIRE_ARRAY
    )
);

$fieldsSearch['user_ids'] = array(
    'type' => 'text',
    'trans' => 'Chat operator',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filter',
    'filter_table_field' => '`lh_chat`.`user_id`',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0), FILTER_REQUIRE_ARRAY
    )
);

$fieldsSearch['creator_user_ids'] = array(
    'type' => 'text',
    'trans' => 'Creator',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filter',
    'filter_table_field' => '`lh_abstract_form_collected`.`user_id`',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0), FILTER_REQUIRE_ARRAY
    )
);

$fieldsSearch['timefrom'] = array(
    'type' => 'text',
    'trans' => 'Time from',
    'required' => false,
    'valid_if_filled' => false,
    'datatype' => 'datetime',
    'filter_type' => 'filtergte',
    'filter_table_field' => 'ctime',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'string'
    )
);

$fieldsSearch['timeto'] = array(
    'type' => 'text',
    'trans' => 'Time to',
    'required' => false,
    'valid_if_filled' => false,
    'datatype' => 'datetime',
    'filter_type' => 'filterlte',
    'filter_table_field' => 'ctime',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'string'
    )
);

$fieldsSearch['chat_time'] = array (
    'type' => 'checkbox',
    'trans' => 'Search in chat time',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'none',
    'filter_table_field' => '',
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
    )
);

$fieldSortAttr = array(
    'field'      => false,
    'default'    => false,
    'serialised' => true,
    'disabled'   => true,
    'options'    => array()
);

return array(
    'filterAttributes' => $fieldsSearch,
    'sortAttributes'   => $fieldSortAttr
);
