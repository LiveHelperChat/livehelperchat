<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_chat_file";
$def->class = "erLhcoreClassModelChatFile";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['upload_name'] = new ezcPersistentObjectProperty();
$def->properties['upload_name']->columnName   = 'upload_name';
$def->properties['upload_name']->propertyName = 'upload_name';
$def->properties['upload_name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['size'] = new ezcPersistentObjectProperty();
$def->properties['size']->columnName   = 'size';
$def->properties['size']->propertyName = 'size';
$def->properties['size']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['type'] = new ezcPersistentObjectProperty();
$def->properties['type']->columnName   = 'type';
$def->properties['type']->propertyName = 'type';
$def->properties['type']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['file_path'] = new ezcPersistentObjectProperty();
$def->properties['file_path']->columnName   = 'file_path';
$def->properties['file_path']->propertyName = 'file_path';
$def->properties['file_path']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>