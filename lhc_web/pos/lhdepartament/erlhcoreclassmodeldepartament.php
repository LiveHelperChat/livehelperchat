<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_departament";
$def->class = "erLhcoreClassModelDepartament";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

foreach (['name','email','xmpp_recipients','xmpp_group_recipients','identifier','inform_options','inform_close_all_email','product_configuration','bot_configuration','alias'] as $posAttr) {
    $def->properties[$posAttr] = new ezcPersistentObjectProperty();
    $def->properties[$posAttr]->columnName   = $posAttr;
    $def->properties[$posAttr]->propertyName = $posAttr;
    $def->properties[$posAttr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
}

foreach (['priority','archive','department_transfer_id','transfer_timeout','online_hours_active','inform_delay','inform_close','inform_unread','inform_unread_delay','disabled',
             'visible_if_online','hidden','active_chats_counter','pending_chats_counter','max_load','max_load_h','nc_cb_execute','na_cb_execute','delay_lm','active_balancing','max_active_chats','max_ac_dep_chats',
             'exclude_inactive_chats','delay_before_assign','max_timeout_seconds','attr_int_1','attr_int_2','attr_int_3','sort_priority','inform_close_all','pending_max','pending_group_max','bot_chats_counter','inactive_chats_cnt','inop_chats_cnt',
             'acop_chats_cnt','assign_same_language'] as $posAttr) {
    $def->properties[$posAttr] = new ezcPersistentObjectProperty();
    $def->properties[$posAttr]->columnName   = $posAttr;
    $def->properties[$posAttr]->propertyName = $posAttr;
    $def->properties[$posAttr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

foreach (array('mod','tud','wed','thd','frd','sad','sud') as $dayShort) {
    $def->properties[$dayShort.'_start_hour'] = new ezcPersistentObjectProperty();
    $def->properties[$dayShort.'_start_hour']->columnName = $dayShort.'_start_hour';
    $def->properties[$dayShort.'_start_hour']->propertyName = $dayShort.'_start_hour';
    $def->properties[$dayShort.'_start_hour']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

    $def->properties[$dayShort.'_end_hour'] = new ezcPersistentObjectProperty();
    $def->properties[$dayShort.'_end_hour']->columnName = $dayShort.'_end_hour';
    $def->properties[$dayShort.'_end_hour']->propertyName = $dayShort.'_end_hour';
    $def->properties[$dayShort.'_end_hour']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

return $def;

?>