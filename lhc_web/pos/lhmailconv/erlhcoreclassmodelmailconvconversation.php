<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_mailconv_conversation";
$def->class = "erLhcoreClassModelMailconvConversation";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

foreach (array(
             'remarks','date','from_address','from_name','body','subject','mail_variables','lang','phone',
             'from_address_clean'
         ) as $attr) {
    $def->properties[$attr] = new ezcPersistentObjectProperty();
    $def->properties[$attr]->columnName   = $attr;
    $def->properties[$attr]->propertyName = $attr;
    $def->properties[$attr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
}

foreach (array(
             'transfer_uid','start_type','lr_time','dep_id','ctime','user_id','status','last_message_id','mailbox_id','message_id',
             'priority','priority_asc','udate','total_messages','match_rule_id','cls_time','pnd_time','wait_time',
             'accept_time','response_time','interaction_time','tslasign','conv_duration','follow_up_id',
             'has_attachment','undelivered','pending_sync','opened_at'
         ) as $attr) {
    $def->properties[$attr] = new ezcPersistentObjectProperty();
    $def->properties[$attr]->columnName   = $attr;
    $def->properties[$attr]->propertyName = $attr;
    $def->properties[$attr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

return $def;

?>