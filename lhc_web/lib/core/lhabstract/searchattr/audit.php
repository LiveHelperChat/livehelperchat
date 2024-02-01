<?php

$fieldsSearch = array();

$fieldsSearch['source'] = array (
    'type' => 'text',
    'trans' => 'Nick',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filter',
    'filter_table_field' => '`lh_audits`.`source`',
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
    )
);

$fieldsSearch['message'] = array (
    'type' => 'text',
    'trans' => 'Nick',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'like',
    'filter_table_field' => '`lh_audits`.`message`',
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
    )
);

$fieldsSearch['category'] = array (
    'type' => 'text',
    'trans' => 'Nick',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filter',
    'filter_table_field' => '`lh_audits`.`category`',
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
    )
);

$fieldsSearch['object_id'] = array (
    'type' => 'text',
    'trans' => 'id',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filter',
    'filter_table_field' => '`lh_audits`.`object_id`',
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
    'datetype_sub' => 'mysql_ts',
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
    'datetype_sub' => 'mysql_ts',
    'filter_type' => 'filterlte',
    'filter_table_field' => 'time',
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'string'
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