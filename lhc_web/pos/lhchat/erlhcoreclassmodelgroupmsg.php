<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_group_msg";
$def->class = "erLhcoreClassModelGroupMsg";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['msg'] = new ezcPersistentObjectProperty();
$def->properties['msg']->columnName   = 'msg';
$def->properties['msg']->propertyName = 'msg';
$def->properties['msg']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Meta data for message
$def->properties['meta_msg'] = new ezcPersistentObjectProperty();
$def->properties['meta_msg']->columnName   = 'meta_msg';
$def->properties['meta_msg']->propertyName = 'meta_msg';
$def->properties['meta_msg']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['name_support'] = new ezcPersistentObjectProperty();
$def->properties['name_support']->columnName   = 'name_support';
$def->properties['name_support']->propertyName = 'name_support';
$def->properties['name_support']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['time'] = new ezcPersistentObjectProperty();
$def->properties['time']->columnName   = 'time';
$def->properties['time']->propertyName = 'time';
$def->properties['time']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['user_id'] = new ezcPersistentObjectProperty();
$def->properties['user_id']->columnName   = 'user_id';
$def->properties['user_id']->propertyName = 'user_id';
$def->properties['user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['chat_id'] = new ezcPersistentObjectProperty();
$def->properties['chat_id']->columnName   = 'chat_id';
$def->properties['chat_id']->propertyName = 'chat_id';
$def->properties['chat_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>