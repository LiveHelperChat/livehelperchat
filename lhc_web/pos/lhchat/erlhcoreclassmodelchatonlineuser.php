<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_chat_online_user";
$def->class = "erLhcoreClassModelChatOnlineUser";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

foreach (['notes','visitor_tz','online_attr_system','operation_chat','online_attr','screenshot_id','operation','total_visits','referrer','identifier','vid','ip','current_page','page_title','user_agent','user_country_code','user_country_name','lat','lon','city','operator_message','operator_user_id','operator_user_proactive','message_seen'] as $posAttr) {
    $def->properties[$posAttr] = new ezcPersistentObjectProperty();
    $def->properties[$posAttr]->columnName   = $posAttr;
    $def->properties[$posAttr]->propertyName = $posAttr;
    $def->properties[$posAttr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
}

foreach (['conversion_id','reopen_chat','invitation_seen_count','user_active','requires_phone','requires_username','requires_email','invitation_id','message_seen_ts','chat_id','last_check_time','last_visit','device_type','dep_id','first_visit','pages_count','tt_pages_count','invitation_count','time_on_site','tt_time_on_site'] as $posAttr) {
    $def->properties[$posAttr] = new ezcPersistentObjectProperty();
    $def->properties[$posAttr]->columnName   = $posAttr;
    $def->properties[$posAttr]->propertyName = $posAttr;
    $def->properties[$posAttr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

return $def;

?>