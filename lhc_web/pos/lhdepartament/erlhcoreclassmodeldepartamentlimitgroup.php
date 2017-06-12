<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_departament_limit_group";
$def->class = "erLhcoreClassModelDepartamentLimitGroup";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['pending_max'] = new ezcPersistentObjectProperty();
$def->properties['pending_max']->columnName   = 'pending_max';
$def->properties['pending_max']->propertyName = 'pending_max';
$def->properties['pending_max']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>