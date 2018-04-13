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

$def->properties['pattern'] = new ezcPersistentObjectProperty();
$def->properties['pattern']->columnName   = 'pattern';
$def->properties['pattern']->propertyName = 'pattern';
$def->properties['pattern']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Trigger event type 0 - text, 1 - click
$def->properties['type'] = new ezcPersistentObjectProperty();
$def->properties['type']->columnName   = 'type';
$def->properties['type']->propertyName = 'type';
$def->properties['type']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>