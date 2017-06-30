<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_auto_responder_chat";
$def->class = "erLhAbstractModelAutoResponderChat";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['chat_id'] = new ezcPersistentObjectProperty();
$def->properties['chat_id']->columnName   = 'chat_id';
$def->properties['chat_id']->propertyName = 'chat_id';
$def->properties['chat_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['auto_responder_id'] = new ezcPersistentObjectProperty();
$def->properties['auto_responder_id']->columnName   = 'auto_responder_id';
$def->properties['auto_responder_id']->propertyName = 'auto_responder_id';
$def->properties['auto_responder_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['wait_timeout_send'] = new ezcPersistentObjectProperty();
$def->properties['wait_timeout_send']->columnName   = 'wait_timeout_send';
$def->properties['wait_timeout_send']->propertyName = 'wait_timeout_send';
$def->properties['wait_timeout_send']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['pending_send_status'] = new ezcPersistentObjectProperty();
$def->properties['pending_send_status']->columnName   = 'pending_send_status';
$def->properties['pending_send_status']->propertyName = 'pending_send_status';
$def->properties['pending_send_status']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['active_send_status'] = new ezcPersistentObjectProperty();
$def->properties['active_send_status']->columnName   = 'active_send_status';
$def->properties['active_send_status']->propertyName = 'active_send_status';
$def->properties['active_send_status']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>