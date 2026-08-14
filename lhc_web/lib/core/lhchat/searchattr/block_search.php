<?php

$fieldsSearch = array();

$fieldsSearch['ip'] = array (
    'type' => 'text',
    'trans' => 'Title',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filter',
    'filter_table_field' => 'ip',
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'string'
    )
);

$fieldsSearch['block_id'] = array (
    'type' => 'text',
    'trans' => 'Title',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filter',
    'filter_table_field' => 'id',
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'string'
    )
);

$fieldsSearch['nick'] = array (
    'type' => 'text',
    'trans' => 'Title',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filter',
    'filter_table_field' => 'nick',
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'string'
    )
);

$fieldsSearch['btype'] = array (
    'type' => 'text',
    'trans' => 'Block type',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filter',
    'filter_table_field' => 'btype',
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
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