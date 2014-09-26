<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_canned_msg";
$def->class = "erLhcoreClassModelCannedMsg";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['msg'] = new ezcPersistentObjectProperty();
$def->properties['msg']->columnName   = 'msg';
$def->properties['msg']->propertyName = 'msg';
$def->properties['msg']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['position'] = new ezcPersistentObjectProperty();
$def->properties['position']->columnName   = 'position';
$def->properties['position']->propertyName = 'position';
$def->properties['position']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['delay'] = new ezcPersistentObjectProperty();
$def->properties['delay']->columnName   = 'delay';
$def->properties['delay']->propertyName = 'delay';
$def->properties['delay']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['department_id'] = new ezcPersistentObjectProperty();
$def->properties['department_id']->columnName   = 'department_id';
$def->properties['department_id']->propertyName = 'department_id';
$def->properties['department_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['user_id'] = new ezcPersistentObjectProperty();
$def->properties['user_id']->columnName   = 'user_id';
$def->properties['user_id']->propertyName = 'user_id';
$def->properties['user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['auto_send'] = new ezcPersistentObjectProperty();
$def->properties['auto_send']->columnName   = 'auto_send';
$def->properties['auto_send']->propertyName = 'auto_send';
$def->properties['auto_send']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>