<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_webhook";
$def->class = "erLhcoreClassModelChatWebhook";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['event'] = new ezcPersistentObjectProperty();
$def->properties['event']->columnName   = 'event';
$def->properties['event']->propertyName = 'event';
$def->properties['event']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['trigger_id'] = new ezcPersistentObjectProperty();
$def->properties['trigger_id']->columnName   = 'trigger_id';
$def->properties['trigger_id']->propertyName = 'trigger_id';
$def->properties['trigger_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['bot_id'] = new ezcPersistentObjectProperty();
$def->properties['bot_id']->columnName   = 'bot_id';
$def->properties['bot_id']->propertyName = 'bot_id';
$def->properties['bot_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['trigger_id_alt'] = new ezcPersistentObjectProperty();
$def->properties['trigger_id_alt']->columnName   = 'trigger_id_alt';
$def->properties['trigger_id_alt']->propertyName = 'trigger_id_alt';
$def->properties['trigger_id_alt']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['bot_id_alt'] = new ezcPersistentObjectProperty();
$def->properties['bot_id_alt']->columnName   = 'bot_id_alt';
$def->properties['bot_id_alt']->propertyName = 'bot_id_alt';
$def->properties['bot_id_alt']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['disabled'] = new ezcPersistentObjectProperty();
$def->properties['disabled']->columnName   = 'disabled';
$def->properties['disabled']->propertyName = 'disabled';
$def->properties['disabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// 0 - One time event based on hook
// 1 - Based on multiple conditions
$def->properties['type'] = new ezcPersistentObjectProperty();
$def->properties['type']->columnName   = 'type';
$def->properties['type']->propertyName = 'type';
$def->properties['type']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['configuration'] = new ezcPersistentObjectProperty();
$def->properties['configuration']->columnName   = 'configuration';
$def->properties['configuration']->propertyName = 'configuration';
$def->properties['configuration']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>