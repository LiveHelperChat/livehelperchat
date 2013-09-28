<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_chat";
$def->class = "erLhcoreClassModelChat";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['nick'] = new ezcPersistentObjectProperty();
$def->properties['nick']->columnName   = 'nick';
$def->properties['nick']->propertyName = 'nick';
$def->properties['nick']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['status'] = new ezcPersistentObjectProperty();
$def->properties['status']->columnName   = 'status';
$def->properties['status']->propertyName = 'status';
$def->properties['status']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['time'] = new ezcPersistentObjectProperty();
$def->properties['time']->columnName   = 'time';
$def->properties['time']->propertyName = 'time';
$def->properties['time']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['user_id'] = new ezcPersistentObjectProperty();
$def->properties['user_id']->columnName   = 'user_id';
$def->properties['user_id']->propertyName = 'user_id';
$def->properties['user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['dep_id'] = new ezcPersistentObjectProperty();
$def->properties['dep_id']->columnName   = 'dep_id';
$def->properties['dep_id']->propertyName = 'dep_id';
$def->properties['dep_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['user_status'] = new ezcPersistentObjectProperty();
$def->properties['user_status']->columnName   = 'user_status';
$def->properties['user_status']->propertyName = 'user_status';
$def->properties['user_status']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['support_informed'] = new ezcPersistentObjectProperty();
$def->properties['support_informed']->columnName   = 'support_informed';
$def->properties['support_informed']->propertyName = 'support_informed';
$def->properties['support_informed']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['hash'] = new ezcPersistentObjectProperty();
$def->properties['hash']->columnName   = 'hash';
$def->properties['hash']->propertyName = 'hash';
$def->properties['hash']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['ip'] = new ezcPersistentObjectProperty();
$def->properties['ip']->columnName   = 'ip';
$def->properties['ip']->propertyName = 'ip';
$def->properties['ip']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['email'] = new ezcPersistentObjectProperty();
$def->properties['email']->columnName   = 'email';
$def->properties['email']->propertyName = 'email';
$def->properties['email']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['phone'] = new ezcPersistentObjectProperty();
$def->properties['phone']->columnName   = 'phone';
$def->properties['phone']->propertyName = 'phone';
$def->properties['phone']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Page from which user starts chat
$def->properties['referrer'] = new ezcPersistentObjectProperty();
$def->properties['referrer']->columnName   = 'referrer';
$def->properties['referrer']->propertyName = 'referrer';
$def->properties['referrer']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Initial referrer from where user has come to site
$def->properties['session_referrer'] = new ezcPersistentObjectProperty();
$def->properties['session_referrer']->columnName   = 'session_referrer';
$def->properties['session_referrer']->propertyName = 'session_referrer';
$def->properties['session_referrer']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['country_code'] = new ezcPersistentObjectProperty();
$def->properties['country_code']->columnName   = 'country_code';
$def->properties['country_code']->propertyName = 'country_code';
$def->properties['country_code']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['country_name'] = new ezcPersistentObjectProperty();
$def->properties['country_name']->columnName   = 'country_name';
$def->properties['country_name']->propertyName = 'country_name';
$def->properties['country_name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['lat'] = new ezcPersistentObjectProperty();
$def->properties['lat']->columnName   = 'lat';
$def->properties['lat']->propertyName = 'lat';
$def->properties['lat']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['lon'] = new ezcPersistentObjectProperty();
$def->properties['lon']->columnName   = 'lon';
$def->properties['lon']->propertyName = 'lon';
$def->properties['lon']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['city'] = new ezcPersistentObjectProperty();
$def->properties['city']->columnName   = 'city';
$def->properties['city']->propertyName = 'city';
$def->properties['city']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['user_typing'] = new ezcPersistentObjectProperty();
$def->properties['user_typing']->columnName   = 'user_typing';
$def->properties['user_typing']->propertyName = 'user_typing';
$def->properties['user_typing']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['user_typing_txt'] = new ezcPersistentObjectProperty();
$def->properties['user_typing_txt']->columnName   = 'user_typing_txt';
$def->properties['user_typing_txt']->propertyName = 'user_typing_txt';
$def->properties['user_typing_txt']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['operator_typing'] = new ezcPersistentObjectProperty();
$def->properties['operator_typing']->columnName   = 'operator_typing';
$def->properties['operator_typing']->propertyName = 'operator_typing';
$def->properties['operator_typing']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['has_unread_messages'] = new ezcPersistentObjectProperty();
$def->properties['has_unread_messages']->columnName   = 'has_unread_messages';
$def->properties['has_unread_messages']->propertyName = 'has_unread_messages';
$def->properties['has_unread_messages']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['last_user_msg_time'] = new ezcPersistentObjectProperty();
$def->properties['last_user_msg_time']->columnName   = 'last_user_msg_time';
$def->properties['last_user_msg_time']->propertyName = 'last_user_msg_time';
$def->properties['last_user_msg_time']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['last_msg_id'] = new ezcPersistentObjectProperty();
$def->properties['last_msg_id']->columnName   = 'last_msg_id';
$def->properties['last_msg_id']->propertyName = 'last_msg_id';
$def->properties['last_msg_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['mail_send'] = new ezcPersistentObjectProperty();
$def->properties['mail_send']->columnName   = 'mail_send';
$def->properties['mail_send']->propertyName = 'mail_send';
$def->properties['mail_send']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['additional_data'] = new ezcPersistentObjectProperty();
$def->properties['additional_data']->columnName   = 'additional_data';
$def->properties['additional_data']->propertyName = 'additional_data';
$def->properties['additional_data']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['wait_time'] = new ezcPersistentObjectProperty();
$def->properties['wait_time']->columnName   = 'wait_time';
$def->properties['wait_time']->propertyName = 'wait_time';
$def->properties['wait_time']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['chat_duration'] = new ezcPersistentObjectProperty();
$def->properties['chat_duration']->columnName   = 'chat_duration';
$def->properties['chat_duration']->propertyName = 'chat_duration';
$def->properties['chat_duration']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['chat_variables'] = new ezcPersistentObjectProperty();
$def->properties['chat_variables']->columnName   = 'chat_variables';
$def->properties['chat_variables']->propertyName = 'chat_variables';
$def->properties['chat_variables']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['priority'] = new ezcPersistentObjectProperty();
$def->properties['priority']->columnName   = 'priority';
$def->properties['priority']->propertyName = 'priority';
$def->properties['priority']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['chat_initiator'] = new ezcPersistentObjectProperty();
$def->properties['chat_initiator']->columnName   = 'chat_initiator';
$def->properties['chat_initiator']->propertyName = 'chat_initiator';
$def->properties['chat_initiator']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['wait_timeout'] = new ezcPersistentObjectProperty();
$def->properties['wait_timeout']->columnName   = 'wait_timeout';
$def->properties['wait_timeout']->propertyName = 'wait_timeout';
$def->properties['wait_timeout']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['wait_timeout_send'] = new ezcPersistentObjectProperty();
$def->properties['wait_timeout_send']->columnName   = 'wait_timeout_send';
$def->properties['wait_timeout_send']->propertyName = 'wait_timeout_send';
$def->properties['wait_timeout_send']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['online_user_id'] = new ezcPersistentObjectProperty();
$def->properties['online_user_id']->columnName   = 'online_user_id';
$def->properties['online_user_id']->propertyName = 'online_user_id';
$def->properties['online_user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['timeout_message'] = new ezcPersistentObjectProperty();
$def->properties['timeout_message']->columnName   = 'timeout_message';
$def->properties['timeout_message']->propertyName = 'timeout_message';
$def->properties['timeout_message']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>