<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_departament_availability";
$def->class = "erLhcoreClassModelDepartamentAvailability";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['dep_id'] = new ezcPersistentObjectProperty();
$def->properties['dep_id']->columnName   = 'dep_id';
$def->properties['dep_id']->propertyName = 'dep_id';
$def->properties['dep_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['hourminute'] = new ezcPersistentObjectProperty();
$def->properties['hourminute']->columnName   = 'hourminute';
$def->properties['hourminute']->propertyName = 'hourminute';
$def->properties['hourminute']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['hour'] = new ezcPersistentObjectProperty();
$def->properties['hour']->columnName   = 'hour';
$def->properties['hour']->propertyName = 'hour';
$def->properties['hour']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['minute'] = new ezcPersistentObjectProperty();
$def->properties['minute']->columnName   = 'minute';
$def->properties['minute']->propertyName = 'minute';
$def->properties['minute']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['time'] = new ezcPersistentObjectProperty();
$def->properties['time']->columnName   = 'time';
$def->properties['time']->propertyName = 'time';
$def->properties['time']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['status'] = new ezcPersistentObjectProperty();
$def->properties['status']->columnName   = 'status';
$def->properties['status']->propertyName = 'status';
$def->properties['status']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['ymdhi'] = new ezcPersistentObjectProperty();
$def->properties['ymdhi']->columnName   = 'ymdhi';
$def->properties['ymdhi']->propertyName = 'ymdhi';
$def->properties['ymdhi']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['ymd'] = new ezcPersistentObjectProperty();
$def->properties['ymd']->columnName   = 'ymd';
$def->properties['ymd']->propertyName = 'ymd';
$def->properties['ymd']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>