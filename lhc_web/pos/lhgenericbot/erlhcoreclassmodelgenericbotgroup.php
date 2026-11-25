<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_generic_bot_group";
$def->class = "erLhcoreClassModelGenericBotGroup";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['bot_id'] = new ezcPersistentObjectProperty();
$def->properties['bot_id']->columnName   = 'bot_id';
$def->properties['bot_id']->propertyName = 'bot_id';
$def->properties['bot_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['is_collapsed'] = new ezcPersistentObjectProperty();
$def->properties['is_collapsed']->columnName   = 'is_collapsed';
$def->properties['is_collapsed']->propertyName = 'is_collapsed';
$def->properties['is_collapsed']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['pos'] = new ezcPersistentObjectProperty();
$def->properties['pos']->columnName   = 'pos';
$def->properties['pos']->propertyName = 'pos';
$def->properties['pos']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>