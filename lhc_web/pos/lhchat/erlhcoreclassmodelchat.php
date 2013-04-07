<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_chat";
$def->class = "erLhcoreClassModelChat";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['nick'] = new ezcPersistentObjectProperty();
$def->properties['nick']->columnName   = 'nick';
$def->properties['nick']->propertyName = 'nick';
$def->properties['nick']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['status'] = new ezcPersistentObjectProperty();
$def->properties['status']->columnName   = 'status';
$def->properties['status']->propertyName = 'status';
$def->properties['status']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['time'] = new ezcPersistentObjectProperty();
$def->properties['time']->columnName   = 'time';
$def->properties['time']->propertyName = 'time';
$def->properties['time']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['user_id'] = new ezcPersistentObjectProperty();
$def->properties['user_id']->columnName   = 'user_id';
$def->properties['user_id']->propertyName = 'user_id';
$def->properties['user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['dep_id'] = new ezcPersistentObjectProperty();
$def->properties['dep_id']->columnName   = 'dep_id';
$def->properties['dep_id']->propertyName = 'dep_id';
$def->properties['dep_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['user_status'] = new ezcPersistentObjectProperty();
$def->properties['user_status']->columnName   = 'user_status';
$def->properties['user_status']->propertyName = 'user_status';
$def->properties['user_status']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['support_informed'] = new ezcPersistentObjectProperty();
$def->properties['support_informed']->columnName   = 'support_informed';
$def->properties['support_informed']->propertyName = 'support_informed';
$def->properties['support_informed']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['hash'] = new ezcPersistentObjectProperty();
$def->properties['hash']->columnName   = 'hash';
$def->properties['hash']->propertyName = 'hash';
$def->properties['hash']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['ip'] = new ezcPersistentObjectProperty();
$def->properties['ip']->columnName   = 'ip';
$def->properties['ip']->propertyName = 'ip';
$def->properties['ip']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['email'] = new ezcPersistentObjectProperty();
$def->properties['email']->columnName   = 'email';
$def->properties['email']->propertyName = 'email';
$def->properties['email']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['phone'] = new ezcPersistentObjectProperty();
$def->properties['phone']->columnName   = 'phone';
$def->properties['phone']->propertyName = 'phone';
$def->properties['phone']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['referrer'] = new ezcPersistentObjectProperty();
$def->properties['referrer']->columnName   = 'referrer';
$def->properties['referrer']->propertyName = 'referrer';
$def->properties['referrer']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['country_code'] = new ezcPersistentObjectProperty();
$def->properties['country_code']->columnName   = 'country_code';
$def->properties['country_code']->propertyName = 'country_code';
$def->properties['country_code']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['country_name'] = new ezcPersistentObjectProperty();
$def->properties['country_name']->columnName   = 'country_name';
$def->properties['country_name']->propertyName = 'country_name';
$def->properties['country_name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['user_typing'] = new ezcPersistentObjectProperty();
$def->properties['user_typing']->columnName   = 'user_typing';
$def->properties['user_typing']->propertyName = 'user_typing';
$def->properties['user_typing']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['operator_typing'] = new ezcPersistentObjectProperty();
$def->properties['operator_typing']->columnName   = 'operator_typing';
$def->properties['operator_typing']->propertyName = 'operator_typing';
$def->properties['operator_typing']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['has_unread_messages'] = new ezcPersistentObjectProperty();
$def->properties['has_unread_messages']->columnName   = 'has_unread_messages';
$def->properties['has_unread_messages']->propertyName = 'has_unread_messages';
$def->properties['has_unread_messages']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['last_user_msg_time'] = new ezcPersistentObjectProperty();
$def->properties['last_user_msg_time']->columnName   = 'last_user_msg_time';
$def->properties['last_user_msg_time']->propertyName = 'last_user_msg_time';
$def->properties['last_user_msg_time']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['last_msg_id'] = new ezcPersistentObjectProperty();
$def->properties['last_msg_id']->columnName   = 'last_msg_id';
$def->properties['last_msg_id']->propertyName = 'last_msg_id';
$def->properties['last_msg_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>