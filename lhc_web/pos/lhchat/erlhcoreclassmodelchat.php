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

/**
 * Main chat status
 * */
$def->properties['status'] = new ezcPersistentObjectProperty();
$def->properties['status']->columnName   = 'status';
$def->properties['status']->propertyName = 'status';
$def->properties['status']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

/**
 * Sub status, used for informing about changed owner. Can be used in the future for more actions
 * */
$def->properties['status_sub'] = new ezcPersistentObjectProperty();
$def->properties['status_sub']->columnName   = 'status_sub';
$def->properties['status_sub']->propertyName = 'status_sub';
$def->properties['status_sub']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

/**
 * Stores additional persisten arguments for substatus changes. Application logic is responsible for for clearing these.
 */
$def->properties['status_sub_arg'] = new ezcPersistentObjectProperty();
$def->properties['status_sub_arg']->columnName   = 'status_sub_arg';
$def->properties['status_sub_arg']->propertyName = 'status_sub_arg';
$def->properties['status_sub_arg']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

/**
* Sub status, used for operators only. Should not influce user actions. Just operators.
* */
$def->properties['status_sub_sub'] = new ezcPersistentObjectProperty();
$def->properties['status_sub_sub']->columnName   = 'status_sub_sub';
$def->properties['status_sub_sub']->propertyName = 'status_sub_sub';
$def->properties['status_sub_sub']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

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

$def->properties['product_id'] = new ezcPersistentObjectProperty();
$def->properties['product_id']->columnName   = 'product_id';
$def->properties['product_id']->propertyName = 'product_id';
$def->properties['product_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['user_status'] = new ezcPersistentObjectProperty();
$def->properties['user_status']->columnName   = 'user_status';
$def->properties['user_status']->propertyName = 'user_status';
$def->properties['user_status']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['user_closed_ts'] = new ezcPersistentObjectProperty();
$def->properties['user_closed_ts']->columnName   = 'user_closed_ts';
$def->properties['user_closed_ts']->propertyName = 'user_closed_ts';
$def->properties['user_closed_ts']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

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

$def->properties['remarks'] = new ezcPersistentObjectProperty();
$def->properties['remarks']->columnName   = 'remarks';
$def->properties['remarks']->propertyName = 'remarks';
$def->properties['remarks']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

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

$def->properties['operator_typing_id'] = new ezcPersistentObjectProperty();
$def->properties['operator_typing_id']->columnName   = 'operator_typing_id';
$def->properties['operator_typing_id']->propertyName = 'operator_typing_id';
$def->properties['operator_typing_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['has_unread_messages'] = new ezcPersistentObjectProperty();
$def->properties['has_unread_messages']->columnName   = 'has_unread_messages';
$def->properties['has_unread_messages']->propertyName = 'has_unread_messages';
$def->properties['has_unread_messages']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['user_tz_identifier'] = new ezcPersistentObjectProperty();
$def->properties['user_tz_identifier']->columnName   = 'user_tz_identifier';
$def->properties['user_tz_identifier']->propertyName = 'user_tz_identifier';
$def->properties['user_tz_identifier']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

/**
 * Repeatable chat inform
 * */
$def->properties['unread_messages_informed'] = new ezcPersistentObjectProperty();
$def->properties['unread_messages_informed']->columnName   = 'unread_messages_informed';
$def->properties['unread_messages_informed']->propertyName = 'unread_messages_informed';
$def->properties['unread_messages_informed']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

/**
 * After how many seconds inform from last user message
 * */
$def->properties['reinform_timeout'] = new ezcPersistentObjectProperty();
$def->properties['reinform_timeout']->columnName   = 'reinform_timeout';
$def->properties['reinform_timeout']->propertyName = 'reinform_timeout';
$def->properties['reinform_timeout']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

/**
 * END Repetable chat inform
 * */


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

$def->properties['online_user_id'] = new ezcPersistentObjectProperty();
$def->properties['online_user_id']->columnName   = 'online_user_id';
$def->properties['online_user_id']->propertyName = 'online_user_id';
$def->properties['online_user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['operation'] = new ezcPersistentObjectProperty();
$def->properties['operation']->columnName   = 'operation';
$def->properties['operation']->propertyName = 'operation';
$def->properties['operation']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['operation_admin'] = new ezcPersistentObjectProperty();
$def->properties['operation_admin']->columnName   = 'operation_admin';
$def->properties['operation_admin']->propertyName = 'operation_admin';
$def->properties['operation_admin']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['screenshot_id'] = new ezcPersistentObjectProperty();
$def->properties['screenshot_id']->columnName   = 'screenshot_id';
$def->properties['screenshot_id']->propertyName = 'screenshot_id';
$def->properties['screenshot_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// TS then transfer accoured, get's updated then chat is transfered, stores current TS
// We cannot user just chat time attribute because, there can be multiple transfer for the same chat.
$def->properties['transfer_timeout_ts'] = new ezcPersistentObjectProperty();
$def->properties['transfer_timeout_ts']->columnName   = 'transfer_timeout_ts';
$def->properties['transfer_timeout_ts']->propertyName = 'transfer_timeout_ts';
$def->properties['transfer_timeout_ts']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Number of seconds after transfer action should be executed
$def->properties['transfer_timeout_ac'] = new ezcPersistentObjectProperty();
$def->properties['transfer_timeout_ac']->columnName   = 'transfer_timeout_ac';
$def->properties['transfer_timeout_ac']->propertyName = 'transfer_timeout_ac';
$def->properties['transfer_timeout_ac']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// This chat requires transfer action if not accepted
$def->properties['transfer_if_na'] = new ezcPersistentObjectProperty();
$def->properties['transfer_if_na']->columnName   = 'transfer_if_na';
$def->properties['transfer_if_na']->propertyName = 'transfer_if_na';
$def->properties['transfer_if_na']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Transfer user ID, user id who transfered chat
$def->properties['transfer_uid'] = new ezcPersistentObjectProperty();
$def->properties['transfer_uid']->columnName   = 'transfer_uid';
$def->properties['transfer_uid']->propertyName = 'transfer_uid';
$def->properties['transfer_uid']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Was callback for unaccepted chat executed? 0 - no, 1 - yes
$def->properties['na_cb_executed'] = new ezcPersistentObjectProperty();
$def->properties['na_cb_executed']->columnName   = 'na_cb_executed';
$def->properties['na_cb_executed']->propertyName = 'na_cb_executed';
$def->properties['na_cb_executed']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Chat locale, if empty that means no automatic translations are needed
$def->properties['chat_locale'] = new ezcPersistentObjectProperty();
$def->properties['chat_locale']->columnName   = 'chat_locale';
$def->properties['chat_locale']->propertyName = 'chat_locale';
$def->properties['chat_locale']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['chat_locale_to'] = new ezcPersistentObjectProperty();
$def->properties['chat_locale_to']->columnName   = 'chat_locale_to';
$def->properties['chat_locale_to']->propertyName = 'chat_locale_to';
$def->properties['chat_locale_to']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Feedback status
// 0 - not votes, 1 - upvote, 2 - novote
$def->properties['fbst'] = new ezcPersistentObjectProperty();
$def->properties['fbst']->columnName   = 'fbst';
$def->properties['fbst']->propertyName = 'fbst';
$def->properties['fbst']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Was callback for new chat executed? 0 - no, 1 - yes
$def->properties['nc_cb_executed'] = new ezcPersistentObjectProperty();
$def->properties['nc_cb_executed']->columnName   = 'nc_cb_executed';
$def->properties['nc_cb_executed']->propertyName = 'nc_cb_executed';
$def->properties['nc_cb_executed']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Time since last assignment
$def->properties['tslasign'] = new ezcPersistentObjectProperty();
$def->properties['tslasign']->columnName   = 'tslasign';
$def->properties['tslasign']->propertyName = 'tslasign';
$def->properties['tslasign']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['unanswered_chat'] = new ezcPersistentObjectProperty();
$def->properties['unanswered_chat']->columnName   = 'unanswered_chat';
$def->properties['unanswered_chat']->propertyName = 'unanswered_chat';
$def->properties['unanswered_chat']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['last_op_msg_time'] = new ezcPersistentObjectProperty();
$def->properties['last_op_msg_time']->columnName   = 'last_op_msg_time';
$def->properties['last_op_msg_time']->propertyName = 'last_op_msg_time';
$def->properties['last_op_msg_time']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['has_unread_op_messages'] = new ezcPersistentObjectProperty();
$def->properties['has_unread_op_messages']->columnName   = 'has_unread_op_messages';
$def->properties['has_unread_op_messages']->propertyName = 'has_unread_op_messages';
$def->properties['has_unread_op_messages']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Last user sync time, updated only lastsync < 30 sec
$def->properties['lsync'] = new ezcPersistentObjectProperty();
$def->properties['lsync']->columnName   = 'lsync';
$def->properties['lsync']->propertyName = 'lsync';
$def->properties['lsync']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['unread_op_messages_informed'] = new ezcPersistentObjectProperty();
$def->properties['unread_op_messages_informed']->columnName   = 'unread_op_messages_informed';
$def->properties['unread_op_messages_informed']->propertyName = 'unread_op_messages_informed';
$def->properties['unread_op_messages_informed']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['device_type'] = new ezcPersistentObjectProperty();
$def->properties['device_type']->columnName   = 'device_type';
$def->properties['device_type']->propertyName = 'device_type';
$def->properties['device_type']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['sender_user_id'] = new ezcPersistentObjectProperty();
$def->properties['sender_user_id']->columnName   = 'sender_user_id';
$def->properties['sender_user_id']->propertyName = 'sender_user_id';
$def->properties['sender_user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['usaccept'] = new ezcPersistentObjectProperty();
$def->properties['usaccept']->columnName   = 'usaccept';
$def->properties['usaccept']->propertyName = 'usaccept';
$def->properties['usaccept']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['uagent'] = new ezcPersistentObjectProperty();
$def->properties['uagent']->columnName   = 'uagent';
$def->properties['uagent']->propertyName = 'uagent';
$def->properties['uagent']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['auto_responder_id'] = new ezcPersistentObjectProperty();
$def->properties['auto_responder_id']->columnName   = 'auto_responder_id';
$def->properties['auto_responder_id']->propertyName = 'auto_responder_id';
$def->properties['auto_responder_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>