<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_mailconv_conversation";
$def->class = "erLhcoreClassModelMailconvConversation";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['dep_id'] = new ezcPersistentObjectProperty();
$def->properties['dep_id']->columnName   = 'dep_id';
$def->properties['dep_id']->propertyName = 'dep_id';
$def->properties['dep_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['ctime'] = new ezcPersistentObjectProperty();
$def->properties['ctime']->columnName   = 'ctime';
$def->properties['ctime']->propertyName = 'ctime';
$def->properties['ctime']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['user_id'] = new ezcPersistentObjectProperty();
$def->properties['user_id']->columnName   = 'user_id';
$def->properties['user_id']->propertyName = 'user_id';
$def->properties['user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['status'] = new ezcPersistentObjectProperty();
$def->properties['status']->columnName   = 'status';
$def->properties['status']->propertyName = 'status';
$def->properties['status']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['subject'] = new ezcPersistentObjectProperty();
$def->properties['subject']->columnName   = 'subject';
$def->properties['subject']->propertyName = 'subject';
$def->properties['subject']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['body'] = new ezcPersistentObjectProperty();
$def->properties['body']->columnName   = 'body';
$def->properties['body']->propertyName = 'body';
$def->properties['body']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['from_name'] = new ezcPersistentObjectProperty();
$def->properties['from_name']->columnName   = 'from_name';
$def->properties['from_name']->propertyName = 'from_name';
$def->properties['from_name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['from_address'] = new ezcPersistentObjectProperty();
$def->properties['from_address']->columnName   = 'from_address';
$def->properties['from_address']->propertyName = 'from_address';
$def->properties['from_address']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Last message ID
$def->properties['last_message_id'] = new ezcPersistentObjectProperty();
$def->properties['last_message_id']->columnName   = 'last_message_id';
$def->properties['last_message_id']->propertyName = 'last_message_id';
$def->properties['last_message_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Mail box ID
$def->properties['mailbox_id'] = new ezcPersistentObjectProperty();
$def->properties['mailbox_id']->columnName   = 'mailbox_id';
$def->properties['mailbox_id']->propertyName = 'mailbox_id';
$def->properties['mailbox_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Initial message ID
$def->properties['message_id'] = new ezcPersistentObjectProperty();
$def->properties['message_id']->columnName   = 'message_id';
$def->properties['message_id']->propertyName = 'message_id';
$def->properties['message_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['priority'] = new ezcPersistentObjectProperty();
$def->properties['priority']->columnName   = 'priority';
$def->properties['priority']->propertyName = 'priority';
$def->properties['priority']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['udate'] = new ezcPersistentObjectProperty();
$def->properties['udate']->columnName   = 'udate';
$def->properties['udate']->propertyName = 'udate';
$def->properties['udate']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['date'] = new ezcPersistentObjectProperty();
$def->properties['date']->columnName   = 'date';
$def->properties['date']->propertyName = 'date';
$def->properties['date']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['total_messages'] = new ezcPersistentObjectProperty();
$def->properties['total_messages']->columnName   = 'total_messages';
$def->properties['total_messages']->propertyName = 'total_messages';
$def->properties['total_messages']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['match_rule_id'] = new ezcPersistentObjectProperty();
$def->properties['match_rule_id']->columnName   = 'match_rule_id';
$def->properties['match_rule_id']->propertyName = 'match_rule_id';
$def->properties['match_rule_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>