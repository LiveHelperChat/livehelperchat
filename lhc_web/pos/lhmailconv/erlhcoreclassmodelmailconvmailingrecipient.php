<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_mailconv_recipient";
$def->class = "erLhcoreClassModelMailconvMailingRecipient";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['email'] = new ezcPersistentObjectProperty();
$def->properties['email']->columnName   = 'email';
$def->properties['email']->propertyName = 'email';
$def->properties['email']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['data'] = new ezcPersistentObjectProperty();
$def->properties['data']->columnName   = 'data';
$def->properties['data']->propertyName = 'data';
$def->properties['data']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['disabled'] = new ezcPersistentObjectProperty();
$def->properties['disabled']->columnName   = 'disabled';
$def->properties['disabled']->propertyName = 'disabled';
$def->properties['disabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>