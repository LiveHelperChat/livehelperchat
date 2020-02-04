<?php

$fieldsSearch = array();

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

$fieldsSearch['visitor'] = array (
	'type' => 'text',
	'trans' => 'User',
	'required' => false,
	'valid_if_filled' => false,
	'filter_type' => 'filter',
	'filter_table_field' => 'user_id',
	'validation_definition' => new ezcInputFormDefinitionElement(
		ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 0, 'max_range' => 0)
	)
);

$fieldsSearch['upload_name'] = array (
    'type' => 'text',
    'trans' => 'Upload Name',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'like',
    'filter_table_field' => 'upload_name',
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
    )
);

$fieldsSearch['persistent'] = array (
	'type' => 'boolean',
	'trans' => 'User',
	'required' => false,
	'valid_if_filled' => false,
	'filter_type' => 'filter',
	'filter_table_field' => 'persistent',
	'validation_definition' => new ezcInputFormDefinitionElement(
		ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
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