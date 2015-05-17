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

$def->properties['mod'] = new ezcPersistentObjectProperty();
$def->properties['mod']->columnName   = 'mod';
$def->properties['mod']->propertyName = 'mod';
$def->properties['mod']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['tud'] = new ezcPersistentObjectProperty();
$def->properties['tud']->columnName   = 'tud';
$def->properties['tud']->propertyName = 'tud';
$def->properties['tud']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['wed'] = new ezcPersistentObjectProperty();
$def->properties['wed']->columnName   = 'wed';
$def->properties['wed']->propertyName = 'wed';
$def->properties['wed']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['thd'] = new ezcPersistentObjectProperty();
$def->properties['thd']->columnName   = 'thd';
$def->properties['thd']->propertyName = 'thd';
$def->properties['thd']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['frd'] = new ezcPersistentObjectProperty();
$def->properties['frd']->columnName   = 'frd';
$def->properties['frd']->propertyName = 'frd';
$def->properties['frd']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['sad'] = new ezcPersistentObjectProperty();
$def->properties['sad']->columnName   = 'sad';
$def->properties['sad']->propertyName = 'sad';
$def->properties['sad']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['sud'] = new ezcPersistentObjectProperty();
$def->properties['sud']->columnName   = 'sud';
$def->properties['sud']->propertyName = 'sud';
$def->properties['sud']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['start_hour'] = new ezcPersistentObjectProperty();
$def->properties['start_hour']->columnName   = 'start_hour';
$def->properties['start_hour']->propertyName = 'start_hour';
$def->properties['start_hour']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['end_hour'] = new ezcPersistentObjectProperty();
$def->properties['end_hour']->columnName   = 'end_hour';
$def->properties['end_hour']->propertyName = 'end_hour';
$def->properties['end_hour']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

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

return $def;

?>