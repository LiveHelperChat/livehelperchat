<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_departament";
$def->class = "erLhcoreClassModelDepartament";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['email'] = new ezcPersistentObjectProperty();
$def->properties['email']->columnName   = 'email';
$def->properties['email']->propertyName = 'email';
$def->properties['email']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['xmpp_recipients'] = new ezcPersistentObjectProperty();
$def->properties['xmpp_recipients']->columnName   = 'xmpp_recipients';
$def->properties['xmpp_recipients']->propertyName = 'xmpp_recipients';
$def->properties['xmpp_recipients']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['xmpp_group_recipients'] = new ezcPersistentObjectProperty();
$def->properties['xmpp_group_recipients']->columnName   = 'xmpp_group_recipients';
$def->properties['xmpp_group_recipients']->propertyName = 'xmpp_group_recipients';
$def->properties['xmpp_group_recipients']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['priority'] = new ezcPersistentObjectProperty();
$def->properties['priority']->columnName   = 'priority';
$def->properties['priority']->propertyName = 'priority';
$def->properties['priority']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['department_transfer_id'] = new ezcPersistentObjectProperty();
$def->properties['department_transfer_id']->columnName   = 'department_transfer_id';
$def->properties['department_transfer_id']->propertyName = 'department_transfer_id';
$def->properties['department_transfer_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['transfer_timeout'] = new ezcPersistentObjectProperty();
$def->properties['transfer_timeout']->columnName   = 'transfer_timeout';
$def->properties['transfer_timeout']->propertyName = 'transfer_timeout';
$def->properties['transfer_timeout']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['identifier'] = new ezcPersistentObjectProperty();
$def->properties['identifier']->columnName   = 'identifier';
$def->properties['identifier']->propertyName = 'identifier';
$def->properties['identifier']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// New chat notification configuration
$def->properties['online_hours_active'] = new ezcPersistentObjectProperty();
$def->properties['online_hours_active']->columnName   = 'online_hours_active';
$def->properties['online_hours_active']->propertyName = 'online_hours_active';
$def->properties['online_hours_active']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

foreach (erLhcoreClassDepartament::getWeekDays() as $dayShort => $dayLong) {
    $def->properties[$dayShort.'_start_hour'] = new ezcPersistentObjectProperty();
    $def->properties[$dayShort.'_start_hour']->columnName = $dayShort.'_start_hour';
    $def->properties[$dayShort.'_start_hour']->propertyName = $dayShort.'_start_hour';
    $def->properties[$dayShort.'_start_hour']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

    $def->properties[$dayShort.'_end_hour'] = new ezcPersistentObjectProperty();
    $def->properties[$dayShort.'_end_hour']->columnName = $dayShort.'_end_hour';
    $def->properties[$dayShort.'_end_hour']->propertyName = $dayShort.'_end_hour';
    $def->properties[$dayShort.'_end_hour']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

$def->properties['inform_options'] = new ezcPersistentObjectProperty();
$def->properties['inform_options']->columnName   = 'inform_options';
$def->properties['inform_options']->propertyName = 'inform_options';
$def->properties['inform_options']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['inform_delay'] = new ezcPersistentObjectProperty();
$def->properties['inform_delay']->columnName   = 'inform_delay';
$def->properties['inform_delay']->propertyName = 'inform_delay';
$def->properties['inform_delay']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['inform_close'] = new ezcPersistentObjectProperty();
$def->properties['inform_close']->columnName   = 'inform_close';
$def->properties['inform_close']->propertyName = 'inform_close';
$def->properties['inform_close']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['inform_unread'] = new ezcPersistentObjectProperty();
$def->properties['inform_unread']->columnName   = 'inform_unread';
$def->properties['inform_unread']->propertyName = 'inform_unread';
$def->properties['inform_unread']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['inform_unread_delay'] = new ezcPersistentObjectProperty();
$def->properties['inform_unread_delay']->columnName   = 'inform_unread_delay';
$def->properties['inform_unread_delay']->propertyName = 'inform_unread_delay';
$def->properties['inform_unread_delay']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['disabled'] = new ezcPersistentObjectProperty();
$def->properties['disabled']->columnName   = 'disabled';
$def->properties['disabled']->propertyName = 'disabled';
$def->properties['disabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['visible_if_online'] = new ezcPersistentObjectProperty();
$def->properties['visible_if_online']->columnName   = 'visible_if_online';
$def->properties['visible_if_online']->propertyName = 'visible_if_online';
$def->properties['visible_if_online']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['hidden'] = new ezcPersistentObjectProperty();
$def->properties['hidden']->columnName   = 'hidden';
$def->properties['hidden']->propertyName = 'hidden';
$def->properties['hidden']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['active_chats_counter'] = new ezcPersistentObjectProperty();
$def->properties['active_chats_counter']->columnName   = 'active_chats_counter';
$def->properties['active_chats_counter']->propertyName = 'active_chats_counter';
$def->properties['active_chats_counter']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['pending_chats_counter'] = new ezcPersistentObjectProperty();
$def->properties['pending_chats_counter']->columnName   = 'pending_chats_counter';
$def->properties['pending_chats_counter']->propertyName = 'pending_chats_counter';
$def->properties['pending_chats_counter']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['closed_chats_counter'] = new ezcPersistentObjectProperty();
$def->properties['closed_chats_counter']->columnName   = 'closed_chats_counter';
$def->properties['closed_chats_counter']->propertyName = 'closed_chats_counter';
$def->properties['closed_chats_counter']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

/**
 * Reset new chat callback execution
 * */
$def->properties['nc_cb_execute'] = new ezcPersistentObjectProperty();
$def->properties['nc_cb_execute']->columnName   = 'nc_cb_execute';
$def->properties['nc_cb_execute']->propertyName = 'nc_cb_execute';
$def->properties['nc_cb_execute']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

/**
 * Reset unanswered chat callback execution
 * */
$def->properties['na_cb_execute'] = new ezcPersistentObjectProperty();
$def->properties['na_cb_execute']->columnName   = 'na_cb_execute';
$def->properties['na_cb_execute']->propertyName = 'na_cb_execute';
$def->properties['na_cb_execute']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

/**
 * Delay before leave a message window is shown
 * */
$def->properties['delay_lm'] = new ezcPersistentObjectProperty();
$def->properties['delay_lm']->columnName   = 'delay_lm';
$def->properties['delay_lm']->propertyName = 'delay_lm';
$def->properties['delay_lm']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['active_balancing'] = new ezcPersistentObjectProperty();
$def->properties['active_balancing']->columnName   = 'active_balancing';
$def->properties['active_balancing']->propertyName = 'active_balancing';
$def->properties['active_balancing']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['max_active_chats'] = new ezcPersistentObjectProperty();
$def->properties['max_active_chats']->columnName   = 'max_active_chats';
$def->properties['max_active_chats']->propertyName = 'max_active_chats';
$def->properties['max_active_chats']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['max_ac_dep_chats'] = new ezcPersistentObjectProperty();
$def->properties['max_ac_dep_chats']->columnName   = 'max_ac_dep_chats';
$def->properties['max_ac_dep_chats']->propertyName = 'max_ac_dep_chats';
$def->properties['max_ac_dep_chats']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['exclude_inactive_chats'] = new ezcPersistentObjectProperty();
$def->properties['exclude_inactive_chats']->columnName   = 'exclude_inactive_chats';
$def->properties['exclude_inactive_chats']->propertyName = 'exclude_inactive_chats';
$def->properties['exclude_inactive_chats']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['delay_before_assign'] = new ezcPersistentObjectProperty();
$def->properties['delay_before_assign']->columnName   = 'delay_before_assign';
$def->properties['delay_before_assign']->propertyName = 'delay_before_assign';
$def->properties['delay_before_assign']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['max_timeout_seconds'] = new ezcPersistentObjectProperty();
$def->properties['max_timeout_seconds']->columnName   = 'max_timeout_seconds';
$def->properties['max_timeout_seconds']->propertyName = 'max_timeout_seconds';
$def->properties['max_timeout_seconds']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['attr_int_1'] = new ezcPersistentObjectProperty();
$def->properties['attr_int_1']->columnName   = 'attr_int_1';
$def->properties['attr_int_1']->propertyName = 'attr_int_1';
$def->properties['attr_int_1']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['attr_int_2'] = new ezcPersistentObjectProperty();
$def->properties['attr_int_2']->columnName   = 'attr_int_2';
$def->properties['attr_int_2']->propertyName = 'attr_int_2';
$def->properties['attr_int_2']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['attr_int_3'] = new ezcPersistentObjectProperty();
$def->properties['attr_int_3']->columnName   = 'attr_int_3';
$def->properties['attr_int_3']->propertyName = 'attr_int_3';
$def->properties['attr_int_3']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['sort_priority'] = new ezcPersistentObjectProperty();
$def->properties['sort_priority']->columnName   = 'sort_priority';
$def->properties['sort_priority']->propertyName = 'sort_priority';
$def->properties['sort_priority']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['inform_close_all'] = new ezcPersistentObjectProperty();
$def->properties['inform_close_all']->columnName   = 'inform_close_all';
$def->properties['inform_close_all']->propertyName = 'inform_close_all';
$def->properties['inform_close_all']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Maximum of pending chats
$def->properties['pending_max'] = new ezcPersistentObjectProperty();
$def->properties['pending_max']->columnName   = 'pending_max';
$def->properties['pending_max']->propertyName = 'pending_max';
$def->properties['pending_max']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['pending_group_max'] = new ezcPersistentObjectProperty();
$def->properties['pending_group_max']->columnName   = 'pending_group_max';
$def->properties['pending_group_max']->propertyName = 'pending_group_max';
$def->properties['pending_group_max']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['inform_close_all_email'] = new ezcPersistentObjectProperty();
$def->properties['inform_close_all_email']->columnName   = 'inform_close_all_email';
$def->properties['inform_close_all_email']->propertyName = 'inform_close_all_email';
$def->properties['inform_close_all_email']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['product_configuration'] = new ezcPersistentObjectProperty();
$def->properties['product_configuration']->columnName   = 'product_configuration';
$def->properties['product_configuration']->propertyName = 'product_configuration';
$def->properties['product_configuration']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>