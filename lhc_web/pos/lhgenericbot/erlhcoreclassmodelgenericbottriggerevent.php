<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_generic_bot_trigger_event";
$def->class = "erLhcoreClassModelGenericBotTriggerEvent";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['trigger_id'] = new ezcPersistentObjectProperty();
$def->properties['trigger_id']->columnName   = 'trigger_id';
$def->properties['trigger_id']->propertyName = 'trigger_id';
$def->properties['trigger_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['bot_id'] = new ezcPersistentObjectProperty();
$def->properties['bot_id']->columnName   = 'bot_id';
$def->properties['bot_id']->propertyName = 'bot_id';
$def->properties['bot_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['pattern'] = new ezcPersistentObjectProperty();
$def->properties['pattern']->columnName   = 'pattern';
$def->properties['pattern']->propertyName = 'pattern';
$def->properties['pattern']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['pattern_exc'] = new ezcPersistentObjectProperty();
$def->properties['pattern_exc']->columnName   = 'pattern_exc';
$def->properties['pattern_exc']->propertyName = 'pattern_exc';
$def->properties['pattern_exc']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['configuration'] = new ezcPersistentObjectProperty();
$def->properties['configuration']->columnName   = 'configuration';
$def->properties['configuration']->propertyName = 'configuration';
$def->properties['configuration']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Trigger event type 0 - text, 1 - click
$def->properties['type'] = new ezcPersistentObjectProperty();
$def->properties['type']->columnName   = 'type';
$def->properties['type']->propertyName = 'type';
$def->properties['type']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Do not check on chat start
// Instant (Executes and continues workflow)
// Instant block (Executes and blocks workflow)
// Schedule (Just schedules for futher execution)
$def->properties['on_start_type'] = new ezcPersistentObjectProperty();
$def->properties['on_start_type']->columnName   = 'on_start_type';
$def->properties['on_start_type']->propertyName = 'on_start_type';
$def->properties['on_start_type']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['priority'] = new ezcPersistentObjectProperty();
$def->properties['priority']->columnName   = 'priority';
$def->properties['priority']->propertyName = 'priority';
$def->properties['priority']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>