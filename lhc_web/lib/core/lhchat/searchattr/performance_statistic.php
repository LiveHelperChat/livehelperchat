<?php

$fieldsSearch = array();


$fieldsSearch['timefrom'] = array (
    'type' => 'text',
    'trans' => 'Timefrom',
    'required' => false,
    'valid_if_filled' => false,
    'datatype' => 'datetime',
    'filter_type' => 'filtergte',
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
    'filter_type' => 'filterlte',
    'filter_table_field' => 'time',
    'validation_definition' => new ezcInputFormDefinitionElement (
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
    )
);

$fieldsSearch['department_id'] = array (
    'type' => 'text',
    'trans' => 'Department',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filter',
    'filter_table_field' => 'dep_id',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 1)
    )
);

$fieldsSearch['department_group_id'] = array (
    'type' => 'text',
    'trans' => 'Department group',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => false,
    'filter_table_field' => 'department_group_id',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 1)
    )
);

$fieldsSearch['timefrom_include_hours'] = array (
    'type' => 'text',
    'trans' => 'Include hours from',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => false,
    'filter_table_field' => 'timefrom_include_hours',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 0)
    )
);

$fieldsSearch['timeto_include_hours'] = array (
    'type' => 'text',
    'trans' => 'Department group',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => false,
    'filter_table_field' => 'timeto_include_hours',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 0)
    )
);

$fieldsSearch['group_id'] = array (
    'type' => 'text',
    'trans' => 'Group',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => false,
    'filter_table_field' => 'group_id',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 1)
    )
);

$fieldsSearch['user_id'] = array (
	'type' => 'text',
	'trans' => 'User',
	'required' => false,
	'valid_if_filled' => false,
	'filter_type' => 'filter',
	'filter_table_field' => 'lh_chat.user_id',
	'validation_definition' => new ezcInputFormDefinitionElement(
		ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 1)
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