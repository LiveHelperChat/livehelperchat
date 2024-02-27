<?php

$fieldsSearch = array();

$fieldsSearch['opened'] = array (
    'type' => 'text',
    'trans' => 'Name',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => false,
    'filter_table_field' => ['customfilter' => ['(`opened_at` > 0)']],
    'filter_table_by_value' => [
        0 => ['customfilter' => ['(`opened_at` = 0)']],
        1 => ['customfilter' => ['(`opened_at` > 0)']],
    ],
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 0, 'max_range' => 1)
    )
);

$fieldsSearch['report'] = array (
    'type' => 'text',
    'trans' => 'Report',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'none',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 1)
    )
);

$fieldsSearch['is_external'] = array (
    'type' => 'text',
    'trans' => 'Department',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filter',
    'filter_table_field' => 'is_external',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 0, 'max_range' => 1)
    )
);

$fieldsSearch['subject_ids'] = array (
    'type' => 'text',
    'trans' => 'id',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => false,
    'filter_table_field' => 'id',
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 1), FILTER_REQUIRE_ARRAY
    )
);

$fieldsSearch['group_limit'] = array (
    'type' => 'text',
    'trans' => 'Group Field',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => false,
    'filter_table_field' => 'group_limit',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 3, 'max_range' => 50)
    )
);

$fieldsSearch['group_field'] = array (
    'type' => 'text',
    'trans' => 'Group Field',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => false,
    'filter_table_field' => 'group_field',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'string', array()
    )
);

$fieldsSearch['chart_type'] = array (
    'type' => 'text',
    'trans' => 'Department',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => false,
    'filter_table_field' => 'chart_type',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'string', array(), FILTER_REQUIRE_ARRAY
    )
);

$fieldsSearch['timefrom'] = array (
    'type' => 'text',
    'trans' => 'Timefrom',
    'required' => false,
    'valid_if_filled' => false,
    'datatype' => 'datetime',
    'filter_type' => 'filtergte',
    'filter_table_field' => 'udate',
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
    'filter_table_field' => 'udate',
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'string'
    )
);

$fieldsSearch['group_id'] = array (
    'type' => 'text',
    'trans' => 'Group',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => false,
    'filter_table_field' => 'dep_id',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 1)
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

$fieldsSearch['groupby'] = array (
    'type' => 'text',
    'trans' => 'groupby',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'none',
    'filter_table_field' => 'user_id',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 0)
    )
);

$fieldsSearch['user_ids'] = array (
    'type' => 'text',
    'trans' => 'Department',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filterin',
    'filter_table_field' => 'user_id',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 0), FILTER_REQUIRE_ARRAY
    )
);

$fieldsSearch['conv_user_ids'] = array (
    'type' => 'text',
    'trans' => 'Department',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filterin',
    'filter_table_field' => 'conv_user_id',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 0), FILTER_REQUIRE_ARRAY
    )
);

$fieldsSearch['has_attachment'] = array (
    'type' => 'text',
    'trans' => 'Department',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'none',
    'filter_table_field' => 'conv_user_id',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 0, 'max_range' => 5)
    )
);

$fieldsSearch['response_type'] = array (
    'type' => 'text',
    'trans' => 'Department',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filterin',
    'filter_table_field' => 'response_type',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 0), FILTER_REQUIRE_ARRAY
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

$fieldsSearch['conv_group_ids'] = array (
    'type' => 'text',
    'trans' => 'Group',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => false,
    'filter_table_field' => 'conv_user_id',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 1), FILTER_REQUIRE_ARRAY
    )
);

$fieldsSearch['department_ids'] = array (
    'type' => 'text',
    'trans' => 'Department',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filterin',
    'filter_table_field' => 'dep_id',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 0), FILTER_REQUIRE_ARRAY
    )
);

$fieldsSearch['department_group_ids'] = array (
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

// Boolean filters
$fieldsSearch['no_operator'] = array (
    'type' => 'boolean',
    'trans' => 'groupby',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'manual',
    'filter_table_field' => ['filter' => ['user_id' => 0]],
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
    )
);

$fieldsSearch['has_operator'] = array (
    'type' => 'boolean',
    'trans' => 'groupby',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'manual',
    'filter_table_field' => ['filtergt' => ['user_id' => 0]],
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