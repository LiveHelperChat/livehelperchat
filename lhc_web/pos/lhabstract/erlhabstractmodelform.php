<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_form";
$def->class = "erLhAbstractModelForm";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['content'] = new ezcPersistentObjectProperty();
$def->properties['content']->columnName   = 'content';
$def->properties['content']->propertyName = 'content';
$def->properties['content']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['name_attr'] = new ezcPersistentObjectProperty();
$def->properties['name_attr']->columnName   = 'name_attr';
$def->properties['name_attr']->propertyName = 'name_attr';
$def->properties['name_attr']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['intro_attr'] = new ezcPersistentObjectProperty();
$def->properties['intro_attr']->columnName   = 'intro_attr';
$def->properties['intro_attr']->propertyName = 'intro_attr';
$def->properties['intro_attr']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['xls_columns'] = new ezcPersistentObjectProperty();
$def->properties['xls_columns']->columnName   = 'xls_columns';
$def->properties['xls_columns']->propertyName = 'xls_columns';
$def->properties['xls_columns']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['recipient'] = new ezcPersistentObjectProperty();
$def->properties['recipient']->columnName   = 'recipient';
$def->properties['recipient']->propertyName = 'recipient';
$def->properties['recipient']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['active'] = new ezcPersistentObjectProperty();
$def->properties['active']->columnName   = 'active';
$def->properties['active']->propertyName = 'active';
$def->properties['active']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['pagelayout'] = new ezcPersistentObjectProperty();
$def->properties['pagelayout']->columnName   = 'pagelayout';
$def->properties['pagelayout']->propertyName = 'pagelayout';
$def->properties['pagelayout']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['post_content'] = new ezcPersistentObjectProperty();
$def->properties['post_content']->columnName   = 'post_content';
$def->properties['post_content']->propertyName = 'post_content';
$def->properties['post_content']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>