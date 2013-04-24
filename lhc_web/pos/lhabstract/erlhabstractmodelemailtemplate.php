<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_email_template";
$def->class = "erLhAbstractModelEmailTemplate";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['from_name'] = new ezcPersistentObjectProperty();
$def->properties['from_name']->columnName   = 'from_name';
$def->properties['from_name']->propertyName = 'from_name';
$def->properties['from_name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['from_email'] = new ezcPersistentObjectProperty();
$def->properties['from_email']->columnName   = 'from_email';
$def->properties['from_email']->propertyName = 'from_email';
$def->properties['from_email']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['content'] = new ezcPersistentObjectProperty();
$def->properties['content']->columnName   = 'content';
$def->properties['content']->propertyName = 'content';
$def->properties['content']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['subject'] = new ezcPersistentObjectProperty();
$def->properties['subject']->columnName   = 'subject';
$def->properties['subject']->propertyName = 'subject';
$def->properties['subject']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;
?>