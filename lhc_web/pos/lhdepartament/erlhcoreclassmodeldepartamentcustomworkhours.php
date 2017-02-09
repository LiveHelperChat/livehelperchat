<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_departament_custom_work_hours";
$def->class = "erLhcoreClassModelDepartamentCustomWorkHours";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['dep_id'] = new ezcPersistentObjectProperty();
$def->properties['dep_id']->columnName   = 'dep_id';
$def->properties['dep_id']->propertyName = 'dep_id';
$def->properties['dep_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['date_from'] = new ezcPersistentObjectProperty();
$def->properties['date_from']->columnName   = 'date_from';
$def->properties['date_from']->propertyName = 'date_from';
$def->properties['date_from']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['date_to'] = new ezcPersistentObjectProperty();
$def->properties['date_to']->columnName   = 'date_to';
$def->properties['date_to']->propertyName = 'date_to';
$def->properties['date_to']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['start_hour'] = new ezcPersistentObjectProperty();
$def->properties['start_hour']->columnName   = 'start_hour';
$def->properties['start_hour']->propertyName = 'start_hour';
$def->properties['start_hour']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['end_hour'] = new ezcPersistentObjectProperty();
$def->properties['end_hour']->columnName   = 'end_hour';
$def->properties['end_hour']->propertyName = 'end_hour';
$def->properties['end_hour']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>