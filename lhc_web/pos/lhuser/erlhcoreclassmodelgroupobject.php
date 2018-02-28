<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_group_object";
$def->class = "erLhcoreClassModelGroupObject";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['group_id'] = new ezcPersistentObjectProperty();
$def->properties['group_id']->columnName   = 'group_id';
$def->properties['group_id']->propertyName = 'group_id';
$def->properties['group_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['object_id'] = new ezcPersistentObjectProperty();
$def->properties['object_id']->columnName   = 'object_id';
$def->properties['object_id']->propertyName = 'object_id';
$def->properties['object_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['type'] = new ezcPersistentObjectProperty();
$def->properties['type']->columnName   = 'type';
$def->properties['type']->propertyName = 'type';
$def->properties['type']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>