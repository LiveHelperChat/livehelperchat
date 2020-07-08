<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_mailconv_mailbox";
$def->class = "erLhcoreClassModelMailconvMailbox";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['mail'] = new ezcPersistentObjectProperty();
$def->properties['mail']->columnName   = 'mail';
$def->properties['mail']->propertyName = 'mail';
$def->properties['mail']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['username'] = new ezcPersistentObjectProperty();
$def->properties['username']->columnName   = 'username';
$def->properties['username']->propertyName = 'username';
$def->properties['username']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['password'] = new ezcPersistentObjectProperty();
$def->properties['password']->columnName   = 'password';
$def->properties['password']->propertyName = 'password';
$def->properties['password']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['host'] = new ezcPersistentObjectProperty();
$def->properties['host']->columnName   = 'host';
$def->properties['host']->propertyName = 'host';
$def->properties['host']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['port'] = new ezcPersistentObjectProperty();
$def->properties['port']->columnName   = 'port';
$def->properties['port']->propertyName = 'port';
$def->properties['port']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['active'] = new ezcPersistentObjectProperty();
$def->properties['active']->columnName   = 'active';
$def->properties['active']->propertyName = 'active';
$def->properties['active']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>