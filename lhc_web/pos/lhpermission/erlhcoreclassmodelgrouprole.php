<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_grouprole";
$def->class = "erLhcoreClassModelGroupRole";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['group_id'] = new ezcPersistentObjectProperty();
$def->properties['group_id']->columnName   = 'group_id';
$def->properties['group_id']->propertyName = 'group_id';
$def->properties['group_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT; 

$def->properties['role_id'] = new ezcPersistentObjectProperty();
$def->properties['role_id']->columnName   = 'role_id';
$def->properties['role_id']->propertyName = 'role_id';
$def->properties['role_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT; 

return $def; 

?>