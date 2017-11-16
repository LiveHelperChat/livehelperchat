<?php

$fieldsSearch = array();

$fieldsSearch['email'] = array (
    'type' => 'text',
    'trans' => 'Sort by',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'like',
    'filter_table_field' => 'email',
    'validation_definition' => new ezcInputFormDefinitionElement (
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
    )
);

$fieldsSearch['nick'] = array (
    'type' => 'text',
    'trans' => 'Nick',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'like',
    'filter_table_field' => 'nick',
    'validation_definition' => new ezcInputFormDefinitionElement (
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
    )
);

$fieldsSearch['chat_id'] = array (
    'type' => 'text',
    'trans' => 'id',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filter',
    'filter_table_field' => 'id',
    'validation_definition' => new ezcInputFormDefinitionElement (
            ezcInputFormDefinitionElement::OPTIONAL, 'int'
    )
);

$fieldsSearch['wait_time_from'] = array (
    'type' => 'text',
    'trans' => 'id',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filtergt',
    'filter_table_field' => 'wait_time',
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'int'
    )
);

$fieldsSearch['wait_time_till'] = array (
    'type' => 'text',
    'trans' => 'id',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filterlte',
    'filter_table_field' => 'wait_time',
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'int'
    )
);

$fieldsSearch['chat_duration_from'] = array (
    'type' => 'text',
    'trans' => 'id',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filtergt',
    'filter_table_field' => 'chat_duration',
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'int'
    )
);

$fieldsSearch['chat_duration_till'] = array (
    'type' => 'text',
    'trans' => 'id',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filterlte',
    'filter_table_field' => 'chat_duration',
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'int'
    )
);

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

$fieldsSearch['product_id'] = array (
	'type' => 'text',
	'trans' => 'Product',
	'required' => false,
	'valid_if_filled' => false,
	'filter_type' => 'filter',
	'filter_table_field' => 'product_id',
	'validation_definition' => new ezcInputFormDefinitionElement(
		ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 1)
	)
);

$fieldsSearch['fbst'] = array (
	'type' => 'text',
	'trans' => 'Feedback status',
	'required' => false,
	'valid_if_filled' => false,
	'filter_type' => 'filter',
	'filter_table_field' => 'fbst',
	'validation_definition' => new ezcInputFormDefinitionElement(
		ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 0)
	)
);

$fieldsSearch['chat_status'] = array (
	'type' => 'text',
	'trans' => 'Chats status',
	'required' => false,
	'valid_if_filled' => false,
	'filter_type' => 'filter',
	'filter_table_field' => 'status',
	'validation_definition' => new ezcInputFormDefinitionElement(
		ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 0,'max_range' => 4)
	)
);

$fieldsSearch['hum'] = array (
    'type' => 'checkbox',
    'trans' => 'Group results',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filter',
    'filter_table_field' => 'has_unread_messages',
    'validation_definition' => new ezcInputFormDefinitionElement (
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