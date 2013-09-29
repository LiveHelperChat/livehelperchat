<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_departament";
$def->class = "erLhcoreClassModelDepartament";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['email'] = new ezcPersistentObjectProperty();
$def->properties['email']->columnName   = 'email';
$def->properties['email']->propertyName = 'email';
$def->properties['email']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['priority'] = new ezcPersistentObjectProperty();
$def->properties['priority']->columnName   = 'priority';
$def->properties['priority']->propertyName = 'priority';
$def->properties['priority']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['department_transfer_id'] = new ezcPersistentObjectProperty();
$def->properties['department_transfer_id']->columnName   = 'department_transfer_id';
$def->properties['department_transfer_id']->propertyName = 'department_transfer_id';
$def->properties['department_transfer_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['transfer_timeout'] = new ezcPersistentObjectProperty();
$def->properties['transfer_timeout']->columnName   = 'transfer_timeout';
$def->properties['transfer_timeout']->propertyName = 'transfer_timeout';
$def->properties['transfer_timeout']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>