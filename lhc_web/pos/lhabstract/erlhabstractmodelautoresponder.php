<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_auto_responder";
$def->class = "erLhAbstractModelAutoResponder";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['siteaccess'] = new ezcPersistentObjectProperty();
$def->properties['siteaccess']->columnName   = 'siteaccess';
$def->properties['siteaccess']->propertyName = 'siteaccess';
$def->properties['siteaccess']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['wait_message'] = new ezcPersistentObjectProperty();
$def->properties['wait_message']->columnName   = 'wait_message';
$def->properties['wait_message']->propertyName = 'wait_message';
$def->properties['wait_message']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// This applies only to proactive chats
$def->properties['only_proactive'] = new ezcPersistentObjectProperty();
$def->properties['only_proactive']->columnName   = 'only_proactive';
$def->properties['only_proactive']->propertyName = 'only_proactive';
$def->properties['only_proactive']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Timeout in seconds.
$def->properties['wait_timeout'] = new ezcPersistentObjectProperty();
$def->properties['wait_timeout']->columnName   = 'wait_timeout';
$def->properties['wait_timeout']->propertyName = 'wait_timeout';
$def->properties['wait_timeout']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['wait_timeout_2'] = new ezcPersistentObjectProperty();
$def->properties['wait_timeout_2']->columnName   = 'wait_timeout_2';
$def->properties['wait_timeout_2']->propertyName = 'wait_timeout_2';
$def->properties['wait_timeout_2']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['wait_timeout_3'] = new ezcPersistentObjectProperty();
$def->properties['wait_timeout_3']->columnName   = 'wait_timeout_3';
$def->properties['wait_timeout_3']->propertyName = 'wait_timeout_3';
$def->properties['wait_timeout_3']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['wait_timeout_4'] = new ezcPersistentObjectProperty();
$def->properties['wait_timeout_4']->columnName   = 'wait_timeout_4';
$def->properties['wait_timeout_4']->propertyName = 'wait_timeout_4';
$def->properties['wait_timeout_4']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['wait_timeout_5'] = new ezcPersistentObjectProperty();
$def->properties['wait_timeout_5']->columnName   = 'wait_timeout_5';
$def->properties['wait_timeout_5']->propertyName = 'wait_timeout_5';
$def->properties['wait_timeout_5']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Department ID
$def->properties['dep_id'] = new ezcPersistentObjectProperty();
$def->properties['dep_id']->columnName   = 'dep_id';
$def->properties['dep_id']->propertyName = 'dep_id';
$def->properties['dep_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Position
$def->properties['position'] = new ezcPersistentObjectProperty();
$def->properties['position']->columnName   = 'position';
$def->properties['position']->propertyName = 'position';
$def->properties['position']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Then timeout passes show visitor this message.
$def->properties['timeout_message'] = new ezcPersistentObjectProperty();
$def->properties['timeout_message']->columnName   = 'timeout_message';
$def->properties['timeout_message']->propertyName = 'timeout_message';
$def->properties['timeout_message']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['timeout_message_2'] = new ezcPersistentObjectProperty();
$def->properties['timeout_message_2']->columnName   = 'timeout_message_2';
$def->properties['timeout_message_2']->propertyName = 'timeout_message_2';
$def->properties['timeout_message_2']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['timeout_message_3'] = new ezcPersistentObjectProperty();
$def->properties['timeout_message_3']->columnName   = 'timeout_message_3';
$def->properties['timeout_message_3']->propertyName = 'timeout_message_3';
$def->properties['timeout_message_3']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['timeout_message_4'] = new ezcPersistentObjectProperty();
$def->properties['timeout_message_4']->columnName   = 'timeout_message_4';
$def->properties['timeout_message_4']->propertyName = 'timeout_message_4';
$def->properties['timeout_message_4']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['timeout_message_5'] = new ezcPersistentObjectProperty();
$def->properties['timeout_message_5']->columnName   = 'timeout_message_5';
$def->properties['timeout_message_5']->propertyName = 'timeout_message_5';
$def->properties['timeout_message_5']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['wait_timeout_hold'] = new ezcPersistentObjectProperty();
$def->properties['wait_timeout_hold']->columnName   = 'wait_timeout_hold';
$def->properties['wait_timeout_hold']->propertyName = 'wait_timeout_hold';
$def->properties['wait_timeout_hold']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

for ($i = 1; $i <= 5; $i++) {
    $def->properties['wait_timeout_hold_' . $i] = new ezcPersistentObjectProperty();
    $def->properties['wait_timeout_hold_' . $i]->columnName   = 'wait_timeout_hold_' . $i;
    $def->properties['wait_timeout_hold_' . $i]->propertyName = 'wait_timeout_hold_' . $i;
    $def->properties['wait_timeout_hold_' . $i]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

    $def->properties['timeout_hold_message_' . $i] = new ezcPersistentObjectProperty();
    $def->properties['timeout_hold_message_' . $i]->columnName   = 'timeout_hold_message_' . $i;
    $def->properties['timeout_hold_message_' . $i]->propertyName = 'timeout_hold_message_' . $i;
    $def->properties['timeout_hold_message_' . $i]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
}

// How many times repeat timeout message
// 0 - infinity times
// 1 - one time
$def->properties['repeat_number'] = new ezcPersistentObjectProperty();
$def->properties['repeat_number']->columnName   = 'repeat_number';
$def->properties['repeat_number']->propertyName = 'repeat_number';
$def->properties['repeat_number']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['wait_timeout_reply_1'] = new ezcPersistentObjectProperty();
$def->properties['wait_timeout_reply_1']->columnName   = 'wait_timeout_reply_1';
$def->properties['wait_timeout_reply_1']->propertyName = 'wait_timeout_reply_1';
$def->properties['wait_timeout_reply_1']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['timeout_reply_message_1'] = new ezcPersistentObjectProperty();
$def->properties['timeout_reply_message_1']->columnName   = 'timeout_reply_message_1';
$def->properties['timeout_reply_message_1']->propertyName = 'timeout_reply_message_1';
$def->properties['timeout_reply_message_1']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['wait_timeout_reply_2'] = new ezcPersistentObjectProperty();
$def->properties['wait_timeout_reply_2']->columnName   = 'wait_timeout_reply_2';
$def->properties['wait_timeout_reply_2']->propertyName = 'wait_timeout_reply_2';
$def->properties['wait_timeout_reply_2']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['timeout_reply_message_2'] = new ezcPersistentObjectProperty();
$def->properties['timeout_reply_message_2']->columnName   = 'timeout_reply_message_2';
$def->properties['timeout_reply_message_2']->propertyName = 'timeout_reply_message_2';
$def->properties['timeout_reply_message_2']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['wait_timeout_reply_3'] = new ezcPersistentObjectProperty();
$def->properties['wait_timeout_reply_3']->columnName   = 'wait_timeout_reply_3';
$def->properties['wait_timeout_reply_3']->propertyName = 'wait_timeout_reply_3';
$def->properties['wait_timeout_reply_3']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['timeout_reply_message_3'] = new ezcPersistentObjectProperty();
$def->properties['timeout_reply_message_3']->columnName   = 'timeout_reply_message_3';
$def->properties['timeout_reply_message_3']->propertyName = 'timeout_reply_message_3';
$def->properties['timeout_reply_message_3']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['wait_timeout_reply_4'] = new ezcPersistentObjectProperty();
$def->properties['wait_timeout_reply_4']->columnName   = 'wait_timeout_reply_4';
$def->properties['wait_timeout_reply_4']->propertyName = 'wait_timeout_reply_4';
$def->properties['wait_timeout_reply_4']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['timeout_reply_message_4'] = new ezcPersistentObjectProperty();
$def->properties['timeout_reply_message_4']->columnName   = 'timeout_reply_message_4';
$def->properties['timeout_reply_message_4']->propertyName = 'timeout_reply_message_4';
$def->properties['timeout_reply_message_4']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['wait_timeout_reply_5'] = new ezcPersistentObjectProperty();
$def->properties['wait_timeout_reply_5']->columnName   = 'wait_timeout_reply_5';
$def->properties['wait_timeout_reply_5']->propertyName = 'wait_timeout_reply_5';
$def->properties['wait_timeout_reply_5']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['timeout_reply_message_5'] = new ezcPersistentObjectProperty();
$def->properties['timeout_reply_message_5']->columnName   = 'timeout_reply_message_5';
$def->properties['timeout_reply_message_5']->propertyName = 'timeout_reply_message_5';
$def->properties['timeout_reply_message_5']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Ignore pending and assigned chat.
// Pending messages won't be send if chat is assigned
$def->properties['ignore_pa_chat'] = new ezcPersistentObjectProperty();
$def->properties['ignore_pa_chat']->columnName   = 'ignore_pa_chat';
$def->properties['ignore_pa_chat']->propertyName = 'ignore_pa_chat';
$def->properties['ignore_pa_chat']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['survey_timeout'] = new ezcPersistentObjectProperty();
$def->properties['survey_timeout']->columnName   = 'survey_timeout';
$def->properties['survey_timeout']->propertyName = 'survey_timeout';
$def->properties['survey_timeout']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['survey_id'] = new ezcPersistentObjectProperty();
$def->properties['survey_id']->columnName   = 'survey_id';
$def->properties['survey_id']->propertyName = 'survey_id';
$def->properties['survey_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>