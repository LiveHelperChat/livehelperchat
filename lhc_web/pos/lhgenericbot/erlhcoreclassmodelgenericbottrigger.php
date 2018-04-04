<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_generic_bot_trigger";
$def->class = "erLhcoreClassModelGenericBotTrigger";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['group_id'] = new ezcPersistentObjectProperty();
$def->properties['group_id']->columnName   = 'group_id';
$def->properties['group_id']->propertyName = 'group_id';
$def->properties['group_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['actions'] = new ezcPersistentObjectProperty();
$def->properties['actions']->columnName   = 'actions';
$def->properties['actions']->propertyName = 'actions';
$def->properties['actions']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>