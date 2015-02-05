<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_email_template";
$def->class = "erLhAbstractModelEmailTemplate";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['from_name'] = new ezcPersistentObjectProperty();
$def->properties['from_name']->columnName   = 'from_name';
$def->properties['from_name']->propertyName = 'from_name';
$def->properties['from_name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['from_name_ac'] = new ezcPersistentObjectProperty();
$def->properties['from_name_ac']->columnName   = 'from_name_ac';
$def->properties['from_name_ac']->propertyName = 'from_name_ac';
$def->properties['from_name_ac']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['from_email'] = new ezcPersistentObjectProperty();
$def->properties['from_email']->columnName   = 'from_email';
$def->properties['from_email']->propertyName = 'from_email';
$def->properties['from_email']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['from_email_ac'] = new ezcPersistentObjectProperty();
$def->properties['from_email_ac']->columnName   = 'from_email_ac';
$def->properties['from_email_ac']->propertyName = 'from_email_ac';
$def->properties['from_email_ac']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['content'] = new ezcPersistentObjectProperty();
$def->properties['content']->columnName   = 'content';
$def->properties['content']->propertyName = 'content';
$def->properties['content']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['subject'] = new ezcPersistentObjectProperty();
$def->properties['subject']->columnName   = 'subject';
$def->properties['subject']->propertyName = 'subject';
$def->properties['subject']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['subject_ac'] = new ezcPersistentObjectProperty();
$def->properties['subject_ac']->columnName   = 'subject_ac';
$def->properties['subject_ac']->propertyName = 'subject_ac';
$def->properties['subject_ac']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['reply_to'] = new ezcPersistentObjectProperty();
$def->properties['reply_to']->columnName   = 'reply_to';
$def->properties['reply_to']->propertyName = 'reply_to';
$def->properties['reply_to']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['reply_to_ac'] = new ezcPersistentObjectProperty();
$def->properties['reply_to_ac']->columnName   = 'reply_to_ac';
$def->properties['reply_to_ac']->propertyName = 'reply_to_ac';
$def->properties['reply_to_ac']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['user_mail_as_sender'] = new ezcPersistentObjectProperty();
$def->properties['user_mail_as_sender']->columnName   = 'user_mail_as_sender';
$def->properties['user_mail_as_sender']->propertyName = 'user_mail_as_sender';
$def->properties['user_mail_as_sender']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['recipient'] = new ezcPersistentObjectProperty();
$def->properties['recipient']->columnName   = 'recipient';
$def->properties['recipient']->propertyName = 'recipient';
$def->properties['recipient']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['bcc_recipients'] = new ezcPersistentObjectProperty();
$def->properties['bcc_recipients']->columnName   = 'bcc_recipients';
$def->properties['bcc_recipients']->propertyName = 'bcc_recipients';
$def->properties['bcc_recipients']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>