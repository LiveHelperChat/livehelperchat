<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_mailconv_msg";
$def->class = "erLhcoreClassModelMailconvMessage";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['status'] = new ezcPersistentObjectProperty();
$def->properties['status']->columnName   = 'status';
$def->properties['status']->propertyName = 'status';
$def->properties['status']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['conversation_id'] = new ezcPersistentObjectProperty();
$def->properties['conversation_id']->columnName   = 'conversation_id';
$def->properties['conversation_id']->propertyName = 'conversation_id';
$def->properties['conversation_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['message_id'] = new ezcPersistentObjectProperty();
$def->properties['message_id']->columnName   = 'message_id';
$def->properties['message_id']->propertyName = 'message_id';
$def->properties['message_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['in_reply_to'] = new ezcPersistentObjectProperty();
$def->properties['in_reply_to']->columnName   = 'in_reply_to';
$def->properties['in_reply_to']->propertyName = 'in_reply_to';
$def->properties['in_reply_to']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['subject'] = new ezcPersistentObjectProperty();
$def->properties['subject']->columnName   = 'subject';
$def->properties['subject']->propertyName = 'subject';
$def->properties['subject']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['body'] = new ezcPersistentObjectProperty();
$def->properties['body']->columnName   = 'body';
$def->properties['body']->propertyName = 'body';
$def->properties['body']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['alt_body'] = new ezcPersistentObjectProperty();
$def->properties['alt_body']->columnName   = 'alt_body';
$def->properties['alt_body']->propertyName = 'alt_body';
$def->properties['alt_body']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['references'] = new ezcPersistentObjectProperty();
$def->properties['references']->columnName   = 'references';
$def->properties['references']->propertyName = 'references';
$def->properties['references']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Time record was created
$def->properties['ctime'] = new ezcPersistentObjectProperty();
$def->properties['ctime']->columnName   = 'ctime';
$def->properties['ctime']->propertyName = 'ctime';
$def->properties['ctime']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Time message was received by mail server
$def->properties['udate'] = new ezcPersistentObjectProperty();
$def->properties['udate']->columnName   = 'udate';
$def->properties['udate']->propertyName = 'udate';
$def->properties['udate']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['date'] = new ezcPersistentObjectProperty();
$def->properties['date']->columnName   = 'date';
$def->properties['date']->propertyName = 'date';
$def->properties['date']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['flagged'] = new ezcPersistentObjectProperty();
$def->properties['flagged']->columnName   = 'flagged';
$def->properties['flagged']->propertyName = 'flagged';
$def->properties['flagged']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['recent'] = new ezcPersistentObjectProperty();
$def->properties['recent']->columnName   = 'recent';
$def->properties['recent']->propertyName = 'recent';
$def->properties['recent']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['msgno'] = new ezcPersistentObjectProperty();
$def->properties['msgno']->columnName   = 'msgno';
$def->properties['msgno']->propertyName = 'msgno';
$def->properties['msgno']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['uid'] = new ezcPersistentObjectProperty();
$def->properties['uid']->columnName   = 'uid';
$def->properties['uid']->propertyName = 'uid';
$def->properties['uid']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['size'] = new ezcPersistentObjectProperty();
$def->properties['size']->columnName   = 'size';
$def->properties['size']->propertyName = 'size';
$def->properties['size']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$stringAttributes = array(
    'from_host','from_name','from_address',
    'sender_host','sender_name','sender_address',
    'toaddress','fromaddress','reply_toaddress','senderaddress',
    'to_data','from_data','reply_to_data','sender_data'
);

foreach ($stringAttributes as $stringAttribute) {
    $def->properties[$stringAttribute] = new ezcPersistentObjectProperty();
    $def->properties[$stringAttribute]->columnName   = $stringAttribute;
    $def->properties[$stringAttribute]->propertyName = $stringAttribute;
    $def->properties[$stringAttribute]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
}

return $def;

?>