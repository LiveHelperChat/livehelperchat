<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_mailconv_conversation";
$def->class = "erLhcoreClassModelMailconvConversation";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

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

$def->properties['date'] = new ezcPersistentObjectProperty();
$def->properties['date']->columnName   = 'date';
$def->properties['date']->propertyName = 'date';
$def->properties['date']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['remarks'] = new ezcPersistentObjectProperty();
$def->properties['remarks']->columnName   = 'remarks';
$def->properties['remarks']->propertyName = 'remarks';
$def->properties['remarks']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$intAttributes = array(
    'transfer_uid','start_type','lr_time','dep_id','ctime','user_id','status','last_message_id','mailbox_id','message_id','priority','udate',
    'total_messages','match_rule_id','cls_time','pnd_time','wait_time',
    'accept_time','response_time','interaction_time','tslasign'
);

foreach ($intAttributes as $intAttribute) {
    $def->properties[$intAttribute] = new ezcPersistentObjectProperty();
    $def->properties[$intAttribute]->columnName   = $intAttribute;
    $def->properties[$intAttribute]->propertyName = $intAttribute;
    $def->properties[$intAttribute]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

return $def;

?>