<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_auto_responder";
$def->class = "erLhAbstractModelAutoResponder";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

foreach (['name','operator','siteaccess','languages','wait_message','timeout_message','timeout_message_2','timeout_message_3','timeout_message_4','timeout_message_5','wait_timeout_hold','bot_configuration'] as $posAttr) {
    $def->properties[$posAttr] = new ezcPersistentObjectProperty();
    $def->properties[$posAttr]->columnName   = $posAttr;
    $def->properties[$posAttr]->propertyName = $posAttr;
    $def->properties[$posAttr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
}

// only_proactive - This applies only to proactive chats
// wait_timeout - Timeout in seconds.
// How many times repeat timeout message - repeat_number
// 0 - infinity times
// 1 - one time
// ignore_pa_chat - // Ignore pending and assigned chat.
// Pending messages won't be send if chat is assigned
foreach (['only_proactive','wait_timeout','wait_timeout_2','wait_timeout_3',
             'wait_timeout_4','wait_timeout_5',
             'dep_id','position','repeat_number','ignore_pa_chat',
             'survey_timeout','survey_id','user_id','disabled'] as $posAttr) {
    $def->properties[$posAttr] = new ezcPersistentObjectProperty();
    $def->properties[$posAttr]->columnName   = $posAttr;
    $def->properties[$posAttr]->propertyName = $posAttr;
    $def->properties[$posAttr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

for ($i = 1; $i <= 5; $i++) {
    $def->properties['wait_timeout_hold_' . $i] = new ezcPersistentObjectProperty();
    $def->properties['wait_timeout_hold_' . $i]->columnName   = 'wait_timeout_hold_' . $i;
    $def->properties['wait_timeout_hold_' . $i]->propertyName = 'wait_timeout_hold_' . $i;
    $def->properties['wait_timeout_hold_' . $i]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

    $def->properties['timeout_hold_message_' . $i] = new ezcPersistentObjectProperty();
    $def->properties['timeout_hold_message_' . $i]->columnName   = 'timeout_hold_message_' . $i;
    $def->properties['timeout_hold_message_' . $i]->propertyName = 'timeout_hold_message_' . $i;
    $def->properties['timeout_hold_message_' . $i]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

    $def->properties['timeout_reply_message_' . $i] = new ezcPersistentObjectProperty();
    $def->properties['timeout_reply_message_' . $i]->columnName   = 'timeout_reply_message_' . $i;
    $def->properties['timeout_reply_message_' . $i]->propertyName = 'timeout_reply_message_' . $i;
    $def->properties['timeout_reply_message_' . $i]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

    $def->properties['wait_timeout_reply_' . $i] = new ezcPersistentObjectProperty();
    $def->properties['wait_timeout_reply_' . $i]->columnName   = 'wait_timeout_reply_' . $i;
    $def->properties['wait_timeout_reply_' . $i]->propertyName = 'wait_timeout_reply_' . $i;
    $def->properties['wait_timeout_reply_' . $i]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

return $def;

?>