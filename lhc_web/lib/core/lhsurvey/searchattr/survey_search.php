<?php

$fieldsSearch = array();

$fieldsSearch['timefrom'] = array (
    'type' => 'text',
    'trans' => 'Timefrom',
    'required' => false,
    'valid_if_filled' => false,
    'datatype' => 'date',
    'filter_type' => 'filtergte',
    'filter_table_field' => 'ftime',
    'validation_definition' => new ezcInputFormDefinitionElement (
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
    )
);

$fieldsSearch['timeto'] = array (
    'type' => 'text',
    'trans' => 'Timeto',
    'required' => false,
    'valid_if_filled' => false,
    'datatype' => 'date',
    'filter_type' => 'filterlte',
    'filter_table_field' => 'ftime',
    'validation_definition' => new ezcInputFormDefinitionElement (
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
    )
);

$fieldsSearch['group_results'] = array (
    'type' => 'checkbox',
    'trans' => 'Group results',
    'required' => false,
    'valid_if_filled' => false,
    'datatype' => 'date',
    'filter_type' => false,
    'filter_table_field' => 'ftime',
    'validation_definition' => new ezcInputFormDefinitionElement (
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
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

$fieldsSearch['user_id'] = array (
	'type' => 'text',
	'trans' => 'User',
	'required' => false,
	'valid_if_filled' => false,
	'filter_type' => 'filter',
	'filter_table_field' => 'user_id',
	'validation_definition' => new ezcInputFormDefinitionElement(
		ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 1)
	)
);

$fieldsSearch['minimum_chats'] = array (
	'type' => 'text',
	'trans' => 'Minimum chats',
	'required' => false,
	'valid_if_filled' => false,
	'filter_type' => false,
	'filter_table_field' => false,
	'validation_definition' => new ezcInputFormDefinitionElement(
		ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 1)
	)
);

for ($i = 1; $i <= 5; $i++) {
	$fieldsSearch['max_stars_' . $i] = array (
			'type' => 'text',
			'trans' => 'Stars',
			'required' => false,
			'valid_if_filled' => false,
			'filter_type' => 'filterin',
			'filter_table_field' => 'max_stars_' . $i,
			'validation_definition' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 1), FILTER_REQUIRE_ARRAY
					)
	);	
	$fieldsSearch['question_options_' . $i] = array (
			'type' => 'text',
			'trans' => 'Options questions',
			'required' => false,
			'valid_if_filled' => false,
			'filter_type' => 'filter',
			'filter_table_field' => 'question_options_' . $i,
			'validation_definition' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 1)
					)
	);	
}

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