<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_stats";
$def->class = "erLhAbstractModelStats";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['type'] = new ezcPersistentObjectProperty();
$def->properties['type']->columnName   = 'type';
$def->properties['type']->propertyName = 'type';
$def->properties['type']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['object_id'] = new ezcPersistentObjectProperty();
$def->properties['object_id']->columnName   = 'object_id';
$def->properties['object_id']->propertyName = 'object_id';
$def->properties['object_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['lupdate'] = new ezcPersistentObjectProperty();
$def->properties['lupdate']->columnName   = 'lupdate';
$def->properties['lupdate']->propertyName = 'lupdate';
$def->properties['lupdate']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['stats'] = new ezcPersistentObjectProperty();
$def->properties['stats']->columnName   = 'stats';
$def->properties['stats']->propertyName = 'stats';
$def->properties['stats']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>