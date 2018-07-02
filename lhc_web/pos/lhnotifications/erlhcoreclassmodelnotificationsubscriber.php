<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_notification_subscriber";
$def->class = "erLhcoreClassModelNotificationSubscriber";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['chat_id'] = new ezcPersistentObjectProperty();
$def->properties['chat_id']->columnName   = 'chat_id';
$def->properties['chat_id']->propertyName = 'chat_id';
$def->properties['chat_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['online_user_id'] = new ezcPersistentObjectProperty();
$def->properties['online_user_id']->columnName   = 'online_user_id';
$def->properties['online_user_id']->propertyName = 'online_user_id';
$def->properties['online_user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['dep_id'] = new ezcPersistentObjectProperty();
$def->properties['dep_id']->columnName   = 'dep_id';
$def->properties['dep_id']->propertyName = 'dep_id';
$def->properties['dep_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['ctime'] = new ezcPersistentObjectProperty();
$def->properties['ctime']->columnName   = 'ctime';
$def->properties['ctime']->propertyName = 'ctime';
$def->properties['ctime']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['utime'] = new ezcPersistentObjectProperty();
$def->properties['utime']->columnName   = 'utime';
$def->properties['utime']->propertyName = 'utime';
$def->properties['utime']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Is subscriber valid/new/failing etc...
$def->properties['status'] = new ezcPersistentObjectProperty();
$def->properties['status']->columnName   = 'status';
$def->properties['status']->propertyName = 'status';
$def->properties['status']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Subscriber endpoint parameters
$def->properties['params'] = new ezcPersistentObjectProperty();
$def->properties['params']->columnName   = 'params';
$def->properties['params']->propertyName = 'params';
$def->properties['params']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// MD5 hash for easier finding existing recipient
$def->properties['subscriber_hash'] = new ezcPersistentObjectProperty();
$def->properties['subscriber_hash']->columnName   = 'subscriber_hash';
$def->properties['subscriber_hash']->propertyName = 'subscriber_hash';
$def->properties['subscriber_hash']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// MD5 hash for easier finding existing recipient
$def->properties['last_error'] = new ezcPersistentObjectProperty();
$def->properties['last_error']->columnName   = 'last_error';
$def->properties['last_error']->propertyName = 'last_error';
$def->properties['last_error']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['uagent'] = new ezcPersistentObjectProperty();
$def->properties['uagent']->columnName   = 'uagent';
$def->properties['uagent']->propertyName = 'uagent';
$def->properties['uagent']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['ip'] = new ezcPersistentObjectProperty();
$def->properties['ip']->columnName   = 'ip';
$def->properties['ip']->propertyName = 'ip';
$def->properties['ip']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['device_type'] = new ezcPersistentObjectProperty();
$def->properties['device_type']->columnName   = 'device_type';
$def->properties['device_type']->propertyName = 'device_type';
$def->properties['device_type']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['theme_id'] = new ezcPersistentObjectProperty();
$def->properties['theme_id']->columnName   = 'theme_id';
$def->properties['theme_id']->propertyName = 'theme_id';
$def->properties['theme_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>