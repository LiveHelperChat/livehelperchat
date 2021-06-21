<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_chat";
$def->class = "erLhcoreClassModelChat";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

foreach (['lat','lon','city','uagent','chat_locale_to','chat_locale','screenshot_id','operation_admin','operation','chat_variables','additional_data','nick','status_sub_arg','hash','ip','email','phone','referrer','session_referrer','country_code','country_name','remarks','user_typing_txt','user_tz_identifier'] as $posAttr) {
    $def->properties[$posAttr] = new ezcPersistentObjectProperty();
    $def->properties[$posAttr]->columnName   = $posAttr;
    $def->properties[$posAttr]->propertyName = $posAttr;
    $def->properties[$posAttr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
}

foreach (['gbot_id','invitation_id','anonymized','cls_time','pnd_time','auto_responder_id','usaccept','sender_user_id','device_type','unread_op_messages_informed','lsync','has_unread_op_messages','last_op_msg_time','unanswered_chat','tslasign','nc_cb_executed','fbst','na_cb_executed','transfer_uid','transfer_if_na','transfer_timeout_ac','transfer_timeout_ts','online_user_id','chat_initiator','priority','chat_duration','wait_time','mail_send','last_msg_id','last_user_msg_time','reinform_timeout','status','status_sub','status_sub_sub','time','user_id','dep_id','product_id','user_status','user_closed_ts','support_informed','user_typing','operator_typing','operator_typing_id','has_unread_messages','unread_messages_informed',
          'cls_us'] as $posAttr) {
    $def->properties[$posAttr] = new ezcPersistentObjectProperty();
    $def->properties[$posAttr]->columnName   = $posAttr;
    $def->properties[$posAttr]->propertyName = $posAttr;
    $def->properties[$posAttr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

return $def;

?>