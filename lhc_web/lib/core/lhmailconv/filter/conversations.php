<?php

$fieldsSearch = array();

$fieldsSearch['email'] = array (
    'type' => 'text',
    'trans' => 'Sort by',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filter',
    'filter_sub_type' => 'email',
    'filter_table_field' => 'from_address_clean',
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'string'
    )
);

$fieldsSearch['phone'] = array (
    'type' => 'text',
    'trans' => 'Sort by',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filter',
    'filter_table_field' => 'phone',
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'string'
    )
);

$fieldsSearch['conversation_id'] = array (
    'type' => 'text',
    'trans' => 'id',
    'required' => false,
    'valid_if_filled' => false,
    'multiple_id' => true,
    'requires_positive' => true,
    'filter_type' => 'filter',
    'filter_table_field' => '`lhc_mailconv_conversation`.`id`',
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'string'
    )
);

$fieldsSearch['ids'] = array (
    'type' => 'text',
    'trans' => 'id',
    'required' => false,
    'valid_if_filled' => false,
    'multiple_id' => true,
    'requires_positive' => true,
    'filter_type' => 'filterin',
    'filter_table_field' => '`lhc_mailconv_conversation`.`id`',
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 0), FILTER_REQUIRE_ARRAY
    )
);

$fieldsSearch['undelivered'] = array (
    'type' => 'checkbox',
    'trans' => 'Group results',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filter',
    'filter_table_field' => '`lhc_mailconv_conversation`.`undelivered`',
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
    )
);

$fieldsSearch['view'] = array (
    'type' => 'text',
    'trans' => 'View',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'none',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 1)
    )
);

$fieldsSearch['ipp'] = array (
    'type' => 'text',
    'trans' => 'View',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'none',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 20, 'max_range' => 200)
    )
);

$fieldsSearch['subject'] = array (
    'type' => 'text',
    'trans' => 'Sort by',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'like',
    'filter_table_field' => 'subject',
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'string'
    )
);

$fieldsSearch['mailbox_ids'] = array (
    'type' => 'text',
    'trans' => 'Mailbox',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filterin',
    'filter_table_field' => '`lhc_mailconv_conversation`.`mailbox_id`',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 0), FILTER_REQUIRE_ARRAY
    )
);

$fieldsSearch['is_external'] = array (
    'type' => 'text',
    'trans' => 'Is external',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => false,
    'filter_table_field' => '`lhc_mailconv_msg`.`is_external`',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 0, 'max_range' => 1)
    )
);

$fieldsSearch['subject_id'] = array (
    'type' => 'text',
    'trans' => 'id',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => false,
    'filter_table_field' => 'id',
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 0), FILTER_REQUIRE_ARRAY
    )
);

$fieldsSearch['timefromts'] = array (
    'type' => 'text',
    'trans' => 'id',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filtergte',
    'filter_table_field' => '`lhc_mailconv_conversation`.`udate`',
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'int'
    )
);

$fieldsSearch['timetots'] = array (
    'type' => 'text',
    'trans' => 'id',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filterlte',
    'filter_table_field' => '`lhc_mailconv_conversation`.`udate`',
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'int'
    )
);

$fieldsSearch['id_gt'] = array (
    'type' => 'text',
    'trans' => 'id',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filtergt',
    'filter_table_field' => '`lhc_mailconv_conversation`.`id`',
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
    'filter_table_field' => '`lhc_mailconv_conversation`.`udate`',
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
    'filter_table_field' => '`lhc_mailconv_conversation`.`udate`',
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
    'filter_table_field' => 'dep_id',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 1)
    )
);

$fieldsSearch['group_id'] = array (
    'type' => 'text',
    'trans' => 'Group',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => false,
    'filter_table_field' => 'user_id',
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
    'filter_table_field' => '`lhc_mailconv_conversation`.`user_id`',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 1)
    )
);

$fieldsSearch['conversation_status'] = array (
    'type' => 'text',
    'trans' => 'Chats status',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filter',
    'filter_table_field' => '`lhc_mailconv_conversation`.`status`',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 0,'max_range' => 1000)
    )
);

$fieldsSearch['conversation_status_ids'] = array (
    'type' => 'text',
    'trans' => 'Chat status',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filterin',
    'filter_table_field' => '`lhc_mailconv_conversation`.`status`',
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 0), FILTER_REQUIRE_ARRAY
    )
);

$fieldsSearch['lang_ids'] = array (
    'type' => 'text',
    'trans' => 'Language',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filterin',
    'filter_table_field' => '`lhc_mailconv_conversation`.`lang`',
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'string', null, FILTER_REQUIRE_ARRAY
    )
);

$fieldsSearch['has_attachment'] = array (
    'type' => 'text',
    'trans' => 'Has attachment',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'none',
    'filter_table_field' => 'has_attachment',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 0,'max_range' => 1000)
    )
);

$fieldsSearch['department_ids'] = array (
    'type' => 'text',
    'trans' => 'Department',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filterin',
    'filter_table_field' => '`lhc_mailconv_conversation`.`dep_id`',
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
    'filter_table_field' => 'lhc_mailconv_conversation`.`dep_id`',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 1), FILTER_REQUIRE_ARRAY
    )
);

$fieldsSearch['user_ids'] = array (
    'type' => 'text',
    'trans' => 'Department',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'filterin',
    'filter_table_field' => '`lhc_mailconv_conversation`.`user_id`',
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
    'filter_table_field' => '`lhc_mailconv_conversation`.`dep_id`',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 1), FILTER_REQUIRE_ARRAY
    )
);

$fieldsSearch['sortby'] = array (
    'type' => 'text',
    'trans' => 'Sort by',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => false,
    'filter_table_field' => 'user_id',
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'string')
);

$fieldsSearch['is_followup'] = array (
    'type' => 'boolean',
    'trans' => 'groupby',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'manual',
    'filter_table_field' => ['customfilter' => ['(`lhc_mailconv_conversation`.`follow_up_id` > 0)']],
    'validation_definition' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
    )
);

$fieldsSearch['opened'] = array (
    'type' => 'text',
    'trans' => 'Name',
    'required' => false,
    'valid_if_filled' => false,
    'filter_type' => 'manual',
    'filter_table_field' => ['customfilter' => ['(`lhc_mailconv_conversation`.`opened_at` > 0)']],
    'filter_table_by_value' => [
        0 => ['customfilter' => ['(`lhc_mailconv_conversation`.`opened_at` = 0)']],
        1 => ['customfilter' => ['(`lhc_mailconv_conversation`.`opened_at` > 0)']],
    ],
    'validation_definition' => new ezcInputFormDefinitionElement (
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 0, 'max_range' => 1)
    )
);

$fieldSortAttr = array (
    'field'      => 'sortby',
    'default'    => 'iddesc',
    'serialised' => true,
    'options'    => array(
        'iddesc' => array('sort_column' => '`lhc_mailconv_conversation`.`id` DESC'),
        'idasc' => array('sort_column' => '`lhc_mailconv_conversation`.`id` ASC'),
        'highprioritynew' => array('sort_column' => '`lhc_mailconv_conversation`.`priority` DESC, `lhc_mailconv_conversation`.`id` DESC'),
        'lowpriorityold' => array('sort_column' => '`lhc_mailconv_conversation`.`priority_asc` ASC, `lhc_mailconv_conversation`.`id` ASC'),
        'statuspriority' => array('sort_column' => '`lhc_mailconv_conversation`.`status` DESC, `lhc_mailconv_conversation`.`priority` DESC'),
        'lastupdateasc' => array('sort_column' => '`lhc_mailconv_conversation`.`udate` ASC'),
        'lastupdatedesc' => array('sort_column' => '`lhc_mailconv_conversation`.`udate` DESC'),
    )
);

return array(
    'filterAttributes' => $fieldsSearch,
    'sortAttributes'   => $fieldSortAttr
);