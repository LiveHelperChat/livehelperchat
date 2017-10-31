<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_rolefunction";
$def->class = "erLhcoreClassModelRoleFunction";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['role_id'] = new ezcPersistentObjectProperty();
$def->properties['role_id']->columnName   = 'role_id';
$def->properties['role_id']->propertyName = 'role_id';
$def->properties['role_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT; 

$def->properties['module'] = new ezcPersistentObjectProperty();
$def->properties['module']->columnName   = 'module';
$def->properties['module']->propertyName = 'module';
$def->properties['module']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING; 

$def->properties['function'] = new ezcPersistentObjectProperty();
$def->properties['function']->columnName   = 'function';
$def->properties['function']->propertyName = 'function';
$def->properties['function']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['limitation'] = new ezcPersistentObjectProperty();
$def->properties['limitation']->columnName   = 'limitation';
$def->properties['limitation']->propertyName = 'limitation';
$def->properties['limitation']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def; 

?>