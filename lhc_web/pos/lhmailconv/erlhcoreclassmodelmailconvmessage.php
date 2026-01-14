<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_mailconv_msg";
$def->class = "erLhcoreClassModelMailconvMessage";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

foreach (array(
    'conv_duration','dep_id','status','conversation_id','mailbox_id','response_type',
     'lr_time','user_id','size','uid','msgno','recent','flagged','udate','ctime','undelivered','priority','opened_at',
     'conversation_id_old','is_external','conv_user_id','auto_submitted'
    ) as $attr) {
    $def->properties[$attr] = new ezcPersistentObjectProperty();
    $def->properties[$attr]->columnName   = $attr;
    $def->properties[$attr]->propertyName = $attr;
    $def->properties[$attr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

foreach (array(
             'mb_folder','cc_data','bcc_data','from_host','from_name','from_address',
             'sender_host','sender_name','sender_address','date','in_reply_to',
             'to_data','reply_to_data','references','alt_body','body','subject','message_id',
             'response_time','cls_time','wait_time','accept_time','interaction_time','has_attachment',
              'rfc822_body','delivery_status','lang','message_hash'
         ) as $attr) {
    $def->properties[$attr] = new ezcPersistentObjectProperty();
    $def->properties[$attr]->columnName   = $attr;
    $def->properties[$attr]->propertyName = $attr;
    $def->properties[$attr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
}

return $def;

?>