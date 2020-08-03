<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_mailconv_mailbox";
$def->class = "erLhcoreClassModelMailconvMailbox";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['mail'] = new ezcPersistentObjectProperty();
$def->properties['mail']->columnName   = 'mail';
$def->properties['mail']->propertyName = 'mail';
$def->properties['mail']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['username'] = new ezcPersistentObjectProperty();
$def->properties['username']->columnName   = 'username';
$def->properties['username']->propertyName = 'username';
$def->properties['username']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['password'] = new ezcPersistentObjectProperty();
$def->properties['password']->columnName   = 'password';
$def->properties['password']->propertyName = 'password';
$def->properties['password']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['host'] = new ezcPersistentObjectProperty();
$def->properties['host']->columnName   = 'host';
$def->properties['host']->propertyName = 'host';
$def->properties['host']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['imap'] = new ezcPersistentObjectProperty();
$def->properties['imap']->columnName   = 'imap';
$def->properties['imap']->propertyName = 'imap';
$def->properties['imap']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['port'] = new ezcPersistentObjectProperty();
$def->properties['port']->columnName   = 'port';
$def->properties['port']->propertyName = 'port';
$def->properties['port']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['active'] = new ezcPersistentObjectProperty();
$def->properties['active']->columnName   = 'active';
$def->properties['active']->propertyName = 'active';
$def->properties['active']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['sync_status'] = new ezcPersistentObjectProperty();
$def->properties['sync_status']->columnName   = 'sync_status';
$def->properties['sync_status']->propertyName = 'sync_status';
$def->properties['sync_status']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['sync_interval'] = new ezcPersistentObjectProperty();
$def->properties['sync_interval']->columnName   = 'sync_interval';
$def->properties['sync_interval']->propertyName = 'sync_interval';
$def->properties['sync_interval']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['last_sync_time'] = new ezcPersistentObjectProperty();
$def->properties['last_sync_time']->columnName   = 'last_sync_time';
$def->properties['last_sync_time']->propertyName = 'last_sync_time';
$def->properties['last_sync_time']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['last_sync_log'] = new ezcPersistentObjectProperty();
$def->properties['last_sync_log']->columnName   = 'last_sync_log';
$def->properties['last_sync_log']->propertyName = 'last_sync_log';
$def->properties['last_sync_log']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['mailbox_sync'] = new ezcPersistentObjectProperty();
$def->properties['mailbox_sync']->columnName   = 'mailbox_sync';
$def->properties['mailbox_sync']->propertyName = 'mailbox_sync';
$def->properties['mailbox_sync']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['signature'] = new ezcPersistentObjectProperty();
$def->properties['signature']->columnName   = 'signature';
$def->properties['signature']->propertyName = 'signature';
$def->properties['signature']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>