<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_departament_group";
$def->class = "erLhcoreClassModelDepartamentGroup";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['achats_cnt'] = new ezcPersistentObjectProperty();
$def->properties['achats_cnt']->columnName   = 'achats_cnt';
$def->properties['achats_cnt']->propertyName = 'achats_cnt';
$def->properties['achats_cnt']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['pchats_cnt'] = new ezcPersistentObjectProperty();
$def->properties['pchats_cnt']->columnName   = 'pchats_cnt';
$def->properties['pchats_cnt']->propertyName = 'pchats_cnt';
$def->properties['pchats_cnt']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['bchats_cnt'] = new ezcPersistentObjectProperty();
$def->properties['bchats_cnt']->columnName   = 'bchats_cnt';
$def->properties['bchats_cnt']->propertyName = 'bchats_cnt';
$def->properties['bchats_cnt']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['max_load'] = new ezcPersistentObjectProperty();
$def->properties['max_load']->columnName   = 'max_load';
$def->properties['max_load']->propertyName = 'max_load';
$def->properties['max_load']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>