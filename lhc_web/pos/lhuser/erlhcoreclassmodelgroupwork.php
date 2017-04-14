<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_group_work";
$def->class = "erLhcoreClassModelGroupWork";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['group_id'] = new ezcPersistentObjectProperty();
$def->properties['group_id']->columnName   = 'group_id';
$def->properties['group_id']->propertyName = 'group_id';
$def->properties['group_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING; 

$def->properties['group_work_id'] = new ezcPersistentObjectProperty();
$def->properties['group_work_id']->columnName   = 'group_work_id';
$def->properties['group_work_id']->propertyName = 'group_work_id';
$def->properties['group_work_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT; 


return $def; 

?>