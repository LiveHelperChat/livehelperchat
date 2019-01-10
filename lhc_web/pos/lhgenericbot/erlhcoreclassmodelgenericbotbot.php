<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_generic_bot_bot";
$def->class = "erLhcoreClassModelGenericBotBot";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['nick'] = new ezcPersistentObjectProperty();
$def->properties['nick']->columnName   = 'nick';
$def->properties['nick']->propertyName = 'nick';
$def->properties['nick']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['configuration'] = new ezcPersistentObjectProperty();
$def->properties['configuration']->columnName   = 'configuration';
$def->properties['configuration']->propertyName = 'configuration';
$def->properties['configuration']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['attr_str_1'] = new ezcPersistentObjectProperty();
$def->properties['attr_str_1']->columnName   = 'attr_str_1';
$def->properties['attr_str_1']->propertyName = 'attr_str_1';
$def->properties['attr_str_1']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['attr_str_2'] = new ezcPersistentObjectProperty();
$def->properties['attr_str_2']->columnName   = 'attr_str_2';
$def->properties['attr_str_2']->propertyName = 'attr_str_2';
$def->properties['attr_str_2']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['attr_str_3'] = new ezcPersistentObjectProperty();
$def->properties['attr_str_3']->columnName   = 'attr_str_3';
$def->properties['attr_str_3']->propertyName = 'attr_str_3';
$def->properties['attr_str_3']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['filepath'] = new ezcPersistentObjectProperty();
$def->properties['filepath']->columnName   = 'filepath';
$def->properties['filepath']->propertyName = 'filepath';
$def->properties['filepath']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['filename'] = new ezcPersistentObjectProperty();
$def->properties['filename']->columnName   = 'filename';
$def->properties['filename']->propertyName = 'filename';
$def->properties['filename']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>