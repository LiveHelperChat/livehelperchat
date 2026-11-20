<?php

$fieldsSearch = array();

$fieldsSearch['dep_id'] = array(
    'type' => 'text',
    'trans' => 'Department',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => false,
    'filter_table_field' => '`lh_abstract_proactive_chat_invitation`.`dep_id`',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1), FILTER_REQUIRE_ARRAY
    )
);

$fieldSortAttr = array(
    'field' => false,
    'default' => false,
    'serialised' => true,
    'disabled' => true,
    'options' => array()
);

return array(
    'filterAttributes' => $fieldsSearch,
    'sortAttributes' => $fieldSortAttr
);
