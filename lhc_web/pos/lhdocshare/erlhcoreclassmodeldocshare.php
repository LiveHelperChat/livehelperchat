<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_doc_share";
$def->class = "erLhcoreClassModelDocShare";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['desc'] = new ezcPersistentObjectProperty();
$def->properties['desc']->columnName   = 'desc';
$def->properties['desc']->propertyName = 'desc';
$def->properties['desc']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['user_id'] = new ezcPersistentObjectProperty();
$def->properties['user_id']->columnName   = 'user_id';
$def->properties['user_id']->propertyName = 'user_id';
$def->properties['user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['active'] = new ezcPersistentObjectProperty();
$def->properties['active']->columnName   = 'active';
$def->properties['active']->propertyName = 'active';
$def->properties['active']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['converted'] = new ezcPersistentObjectProperty();
$def->properties['converted']->columnName   = 'converted';
$def->properties['converted']->propertyName = 'converted';
$def->properties['converted']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['file_name'] = new ezcPersistentObjectProperty();
$def->properties['file_name']->columnName   = 'file_name';
$def->properties['file_name']->propertyName = 'file_name';
$def->properties['file_name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['file_path'] = new ezcPersistentObjectProperty();
$def->properties['file_path']->columnName   = 'file_path';
$def->properties['file_path']->propertyName = 'file_path';
$def->properties['file_path']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['file_name_upload'] = new ezcPersistentObjectProperty();
$def->properties['file_name_upload']->columnName   = 'file_name_upload';
$def->properties['file_name_upload']->propertyName = 'file_name_upload';
$def->properties['file_name_upload']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['file_size'] = new ezcPersistentObjectProperty();
$def->properties['file_size']->columnName   = 'file_size';
$def->properties['file_size']->propertyName = 'file_size';
$def->properties['file_size']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['type'] = new ezcPersistentObjectProperty();
$def->properties['type']->columnName   = 'type';
$def->properties['type']->propertyName = 'type';
$def->properties['type']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['ext'] = new ezcPersistentObjectProperty();
$def->properties['ext']->columnName   = 'ext';
$def->properties['ext']->propertyName = 'ext';
$def->properties['ext']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['pdf_file'] = new ezcPersistentObjectProperty();
$def->properties['pdf_file']->columnName   = 'pdf_file';
$def->properties['pdf_file']->propertyName = 'pdf_file';
$def->properties['pdf_file']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['pages_pdf_count'] = new ezcPersistentObjectProperty();
$def->properties['pages_pdf_count']->columnName   = 'pages_pdf_count';
$def->properties['pages_pdf_count']->propertyName = 'pages_pdf_count';
$def->properties['pages_pdf_count']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['pdf_to_img_converted'] = new ezcPersistentObjectProperty();
$def->properties['pdf_to_img_converted']->columnName   = 'pdf_to_img_converted';
$def->properties['pdf_to_img_converted']->propertyName = 'pdf_to_img_converted';
$def->properties['pdf_to_img_converted']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>