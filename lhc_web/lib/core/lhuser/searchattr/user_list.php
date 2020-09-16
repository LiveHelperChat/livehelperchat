<?php

$fieldsSearch = array();

$fieldsSearch['email'] = array (
    'type' => 'text',
    'trans' => 'Sort by',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filter',
    'filter_table_field' => 'email',
    'validation_definition' => new ezcInputFormDefinitionElement (
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
    )
);

$fieldsSearch['name'] = array (
    'type' => 'text',
    'trans' => 'Nick',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filter',
    'filter_table_field' => 'name',
    'validation_definition' => new ezcInputFormDefinitionElement (
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
    )
);

$fieldsSearch['username'] = array (
    'type' => 'text',
    'trans' => 'Nick',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filter',
    'filter_table_field' => 'username',
    'validation_definition' => new ezcInputFormDefinitionElement (
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
    )
);

$fieldsSearch['disabled'] = array (
    'type' => 'text',
    'trans' => 'Disabled',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filter',
    'filter_table_field' => 'disabled',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 0,'max_range' => 1)
    )
);

$fieldsSearch['chat_nickname'] = array (
    'type' => 'text',
    'trans' => 'Nick',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filter',
    'filter_table_field' => 'chat_nickname',
    'validation_definition' => new ezcInputFormDefinitionElement (
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
    )
);

$fieldsSearch['surname'] = array (
    'type' => 'text',
    'trans' => 'Nick',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filter',
    'filter_table_field' => 'surname',
    'validation_definition' => new ezcInputFormDefinitionElement (
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
    )
);

$fieldsSearch['group_ids'] = array (
    'type' => 'text',
    'trans' => 'Group',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => false,
    'filter_table_field' => 'dep_id',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 1), FILTER_REQUIRE_ARRAY
    )
);

$fieldSortAttr = array (
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