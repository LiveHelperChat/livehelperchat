<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_cobrowse";
$def->class = "erLhcoreClassModelCoBrowse";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['chat_id'] = new ezcPersistentObjectProperty();
$def->properties['chat_id']->columnName   = 'chat_id';
$def->properties['chat_id']->propertyName = 'chat_id';
$def->properties['chat_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['mtime'] = new ezcPersistentObjectProperty();
$def->properties['mtime']->columnName   = 'mtime';
$def->properties['mtime']->propertyName = 'mtime';
$def->properties['mtime']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['url'] = new ezcPersistentObjectProperty();
$def->properties['url']->columnName   = 'url';
$def->properties['url']->propertyName = 'url';
$def->properties['url']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['initialize'] = new ezcPersistentObjectProperty();
$def->properties['initialize']->columnName   = 'initialize';
$def->properties['initialize']->propertyName = 'initialize';
$def->properties['initialize']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['modifications'] = new ezcPersistentObjectProperty();
$def->properties['modifications']->columnName   = 'modifications';
$def->properties['modifications']->propertyName = 'modifications';
$def->properties['modifications']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['finished'] = new ezcPersistentObjectProperty();
$def->properties['finished']->columnName   = 'finished';
$def->properties['finished']->propertyName = 'finished';
$def->properties['finished']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>