<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_group_chat";
$def->class = "erLhcoreClassModelGroupChat";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

/**
 * Main chat status
 * */
$def->properties['status'] = new ezcPersistentObjectProperty();
$def->properties['status']->columnName   = 'status';
$def->properties['status']->propertyName = 'status';
$def->properties['status']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['time'] = new ezcPersistentObjectProperty();
$def->properties['time']->columnName   = 'time';
$def->properties['time']->propertyName = 'time';
$def->properties['time']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

/**
 * Operator who created a group
**/
$def->properties['user_id'] = new ezcPersistentObjectProperty();
$def->properties['user_id']->columnName   = 'user_id';
$def->properties['user_id']->propertyName = 'user_id';
$def->properties['user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['last_msg'] = new ezcPersistentObjectProperty();
$def->properties['last_msg']->columnName   = 'last_msg';
$def->properties['last_msg']->propertyName = 'last_msg';
$def->properties['last_msg']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['last_msg_op_id'] = new ezcPersistentObjectProperty();
$def->properties['last_msg_op_id']->columnName   = 'last_msg_op_id';
$def->properties['last_msg_op_id']->propertyName = 'last_msg_op_id';
$def->properties['last_msg_op_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['last_user_msg_time'] = new ezcPersistentObjectProperty();
$def->properties['last_user_msg_time']->columnName   = 'last_user_msg_time';
$def->properties['last_user_msg_time']->propertyName = 'last_user_msg_time';
$def->properties['last_user_msg_time']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['last_msg_id'] = new ezcPersistentObjectProperty();
$def->properties['last_msg_id']->columnName   = 'last_msg_id';
$def->properties['last_msg_id']->propertyName = 'last_msg_id';
$def->properties['last_msg_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['type'] = new ezcPersistentObjectProperty();
$def->properties['type']->columnName   = 'type';
$def->properties['type']->propertyName = 'type';
$def->properties['type']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Total members
$def->properties['tm'] = new ezcPersistentObjectProperty();
$def->properties['tm']->columnName   = 'tm';
$def->properties['tm']->propertyName = 'tm';
$def->properties['tm']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>