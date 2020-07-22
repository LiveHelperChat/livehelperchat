<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_mailconv_file";
$def->class = "erLhcoreClassModelMailconvFile";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['message_id'] = new ezcPersistentObjectProperty();
$def->properties['message_id']->columnName   = 'message_id';
$def->properties['message_id']->propertyName = 'message_id';
$def->properties['message_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['size'] = new ezcPersistentObjectProperty();
$def->properties['size']->columnName   = 'size';
$def->properties['size']->propertyName = 'size';
$def->properties['size']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['description'] = new ezcPersistentObjectProperty();
$def->properties['description']->columnName   = 'description';
$def->properties['description']->propertyName = 'description';
$def->properties['description']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['extension'] = new ezcPersistentObjectProperty();
$def->properties['extension']->columnName   = 'extension';
$def->properties['extension']->propertyName = 'extension';
$def->properties['extension']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['type'] = new ezcPersistentObjectProperty();
$def->properties['type']->columnName   = 'type';
$def->properties['type']->propertyName = 'type';
$def->properties['type']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['attachment_id'] = new ezcPersistentObjectProperty();
$def->properties['attachment_id']->columnName   = 'attachment_id';
$def->properties['attachment_id']->propertyName = 'attachment_id';
$def->properties['attachment_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['file_path'] = new ezcPersistentObjectProperty();
$def->properties['file_path']->columnName   = 'file_path';
$def->properties['file_path']->propertyName = 'file_path';
$def->properties['file_path']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['file_name'] = new ezcPersistentObjectProperty();
$def->properties['file_name']->columnName   = 'file_name';
$def->properties['file_name']->propertyName = 'file_name';
$def->properties['file_name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['content_id'] = new ezcPersistentObjectProperty();
$def->properties['content_id']->columnName   = 'content_id';
$def->properties['content_id']->propertyName = 'content_id';
$def->properties['content_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['disposition'] = new ezcPersistentObjectProperty();
$def->properties['disposition']->columnName   = 'disposition';
$def->properties['disposition']->propertyName = 'disposition';
$def->properties['disposition']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>