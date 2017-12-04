<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_users";
$def->class = "erLhcoreClassModelUser";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['username'] = new ezcPersistentObjectProperty();
$def->properties['username']->columnName   = 'username';
$def->properties['username']->propertyName = 'username';
$def->properties['username']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['password'] = new ezcPersistentObjectProperty();
$def->properties['password']->columnName   = 'password';
$def->properties['password']->propertyName = 'password';
$def->properties['password']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['email'] = new ezcPersistentObjectProperty();
$def->properties['email']->columnName   = 'email';
$def->properties['email']->propertyName = 'email';
$def->properties['email']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['chat_nickname'] = new ezcPersistentObjectProperty();
$def->properties['chat_nickname']->columnName   = 'chat_nickname';
$def->properties['chat_nickname']->propertyName = 'chat_nickname';
$def->properties['chat_nickname']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['skype'] = new ezcPersistentObjectProperty();
$def->properties['skype']->columnName   = 'skype';
$def->properties['skype']->propertyName = 'skype';
$def->properties['skype']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['xmpp_username'] = new ezcPersistentObjectProperty();
$def->properties['xmpp_username']->columnName   = 'xmpp_username';
$def->properties['xmpp_username']->propertyName = 'xmpp_username';
$def->properties['xmpp_username']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['surname'] = new ezcPersistentObjectProperty();
$def->properties['surname']->columnName   = 'surname';
$def->properties['surname']->propertyName = 'surname';
$def->properties['surname']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['job_title'] = new ezcPersistentObjectProperty();
$def->properties['job_title']->columnName   = 'job_title';
$def->properties['job_title']->propertyName = 'job_title';
$def->properties['job_title']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['disabled'] = new ezcPersistentObjectProperty();
$def->properties['disabled']->columnName   = 'disabled';
$def->properties['disabled']->propertyName = 'disabled';
$def->properties['disabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['hide_online'] = new ezcPersistentObjectProperty();
$def->properties['hide_online']->columnName   = 'hide_online';
$def->properties['hide_online']->propertyName = 'hide_online';
$def->properties['hide_online']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['all_departments'] = new ezcPersistentObjectProperty();
$def->properties['all_departments']->columnName   = 'all_departments';
$def->properties['all_departments']->propertyName = 'all_departments';
$def->properties['all_departments']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['invisible_mode'] = new ezcPersistentObjectProperty();
$def->properties['invisible_mode']->columnName   = 'invisible_mode';
$def->properties['invisible_mode']->propertyName = 'invisible_mode';
$def->properties['invisible_mode']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['inactive_mode'] = new ezcPersistentObjectProperty();
$def->properties['inactive_mode']->columnName   = 'inactive_mode';
$def->properties['inactive_mode']->propertyName = 'inactive_mode';
$def->properties['inactive_mode']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['active_chats_counter'] = new ezcPersistentObjectProperty();
$def->properties['active_chats_counter']->columnName   = 'active_chats_counter';
$def->properties['active_chats_counter']->propertyName = 'active_chats_counter';
$def->properties['active_chats_counter']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['closed_chats_counter'] = new ezcPersistentObjectProperty();
$def->properties['closed_chats_counter']->columnName   = 'closed_chats_counter';
$def->properties['closed_chats_counter']->propertyName = 'closed_chats_counter';
$def->properties['closed_chats_counter']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['pending_chats_counter'] = new ezcPersistentObjectProperty();
$def->properties['pending_chats_counter']->columnName   = 'pending_chats_counter';
$def->properties['pending_chats_counter']->propertyName = 'pending_chats_counter';
$def->properties['pending_chats_counter']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

/**
 * We store to user assigned department, for easier fetching. Just for repreesntation purposes.
 * */
$def->properties['departments_ids'] = new ezcPersistentObjectProperty();
$def->properties['departments_ids']->columnName   = 'departments_ids';
$def->properties['departments_ids']->propertyName = 'departments_ids';
$def->properties['departments_ids']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

/**
 * Receive permission request from other users/operators
 * */
$def->properties['rec_per_req'] = new ezcPersistentObjectProperty();
$def->properties['rec_per_req']->columnName   = 'rec_per_req';
$def->properties['rec_per_req']->propertyName = 'rec_per_req';
$def->properties['rec_per_req']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['filepath'] = new ezcPersistentObjectProperty();
$def->properties['filepath']->columnName   = 'filepath';
$def->properties['filepath']->propertyName = 'filepath';
$def->properties['filepath']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['filename'] = new ezcPersistentObjectProperty();
$def->properties['filename']->columnName   = 'filename';
$def->properties['filename']->propertyName = 'filename';
$def->properties['filename']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['time_zone'] = new ezcPersistentObjectProperty();
$def->properties['time_zone']->columnName   = 'time_zone';
$def->properties['time_zone']->propertyName = 'time_zone';
$def->properties['time_zone']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['session_id'] = new ezcPersistentObjectProperty();
$def->properties['session_id']->columnName   = 'session_id';
$def->properties['session_id']->propertyName = 'session_id';
$def->properties['session_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['operation_admin'] = new ezcPersistentObjectProperty();
$def->properties['operation_admin']->columnName   = 'operation_admin';
$def->properties['operation_admin']->propertyName = 'operation_admin';
$def->properties['operation_admin']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['max_active_chats'] = new ezcPersistentObjectProperty();
$def->properties['max_active_chats']->columnName   = 'max_active_chats';
$def->properties['max_active_chats']->propertyName = 'max_active_chats';
$def->properties['max_active_chats']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['auto_accept'] = new ezcPersistentObjectProperty();
$def->properties['auto_accept']->columnName   = 'auto_accept';
$def->properties['auto_accept']->propertyName = 'auto_accept';
$def->properties['auto_accept']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['exclude_autoasign'] = new ezcPersistentObjectProperty();
$def->properties['exclude_autoasign']->columnName   = 'exclude_autoasign';
$def->properties['exclude_autoasign']->propertyName = 'exclude_autoasign';
$def->properties['exclude_autoasign']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

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