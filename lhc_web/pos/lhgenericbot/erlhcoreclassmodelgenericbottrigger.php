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

$def->properties['bot_id'] = new ezcPersistentObjectProperty();
$def->properties['bot_id']->columnName   = 'bot_id';
$def->properties['bot_id']->propertyName = 'bot_id';
$def->properties['bot_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['actions'] = new ezcPersistentObjectProperty();
$def->properties['actions']->columnName   = 'actions';
$def->properties['actions']->propertyName = 'actions';
$def->properties['actions']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['default'] = new ezcPersistentObjectProperty();
$def->properties['default']->columnName   = 'default';
$def->properties['default']->propertyName = 'default';
$def->properties['default']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['default_unknown'] = new ezcPersistentObjectProperty();
$def->properties['default_unknown']->columnName   = 'default_unknown';
$def->properties['default_unknown']->propertyName = 'default_unknown';
$def->properties['default_unknown']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['default_always'] = new ezcPersistentObjectProperty();
$def->properties['default_always']->columnName   = 'default_always';
$def->properties['default_always']->propertyName = 'default_always';
$def->properties['default_always']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['default_unknown_btn'] = new ezcPersistentObjectProperty();
$def->properties['default_unknown_btn']->columnName   = 'default_unknown_btn';
$def->properties['default_unknown_btn']->propertyName = 'default_unknown_btn';
$def->properties['default_unknown_btn']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>