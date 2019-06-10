<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_proactive_chat_invitation";
$def->class = "erLhAbstractModelProactiveChatInvitation";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['siteaccess'] = new ezcPersistentObjectProperty();
$def->properties['siteaccess']->columnName   = 'siteaccess';
$def->properties['siteaccess']->propertyName = 'siteaccess';
$def->properties['siteaccess']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['time_on_site'] = new ezcPersistentObjectProperty();
$def->properties['time_on_site']->columnName   = 'time_on_site';
$def->properties['time_on_site']->propertyName = 'time_on_site';
$def->properties['time_on_site']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['inject_only_html'] = new ezcPersistentObjectProperty();
$def->properties['inject_only_html']->columnName   = 'inject_only_html';
$def->properties['inject_only_html']->propertyName = 'inject_only_html';
$def->properties['inject_only_html']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['delay'] = new ezcPersistentObjectProperty();
$def->properties['delay']->columnName   = 'delay';
$def->properties['delay']->propertyName = 'delay';
$def->properties['delay']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['delay_init'] = new ezcPersistentObjectProperty();
$def->properties['delay_init']->columnName   = 'delay_init';
$def->properties['delay_init']->propertyName = 'delay_init';
$def->properties['delay_init']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['show_instant'] = new ezcPersistentObjectProperty();
$def->properties['show_instant']->columnName   = 'show_instant';
$def->properties['show_instant']->propertyName = 'show_instant';
$def->properties['show_instant']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['pageviews'] = new ezcPersistentObjectProperty();
$def->properties['pageviews']->columnName   = 'pageviews';
$def->properties['pageviews']->propertyName = 'pageviews';
$def->properties['pageviews']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['message'] = new ezcPersistentObjectProperty();
$def->properties['message']->columnName   = 'message';
$def->properties['message']->propertyName = 'message';
$def->properties['message']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['identifier'] = new ezcPersistentObjectProperty();
$def->properties['identifier']->columnName   = 'identifier';
$def->properties['identifier']->propertyName = 'identifier';
$def->properties['identifier']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['executed_times'] = new ezcPersistentObjectProperty();
$def->properties['executed_times']->columnName   = 'executed_times';
$def->properties['executed_times']->propertyName = 'executed_times';
$def->properties['executed_times']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['position'] = new ezcPersistentObjectProperty();
$def->properties['position']->columnName   = 'position';
$def->properties['position']->propertyName = 'position';
$def->properties['position']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['operator_name'] = new ezcPersistentObjectProperty();
$def->properties['operator_name']->columnName   = 'operator_name';
$def->properties['operator_name']->propertyName = 'operator_name';
$def->properties['operator_name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['operator_ids'] = new ezcPersistentObjectProperty();
$def->properties['operator_ids']->columnName   = 'operator_ids';
$def->properties['operator_ids']->propertyName = 'operator_ids';
$def->properties['operator_ids']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Department ID
$def->properties['dep_id'] = new ezcPersistentObjectProperty();
$def->properties['dep_id']->columnName   = 'dep_id';
$def->properties['dep_id']->propertyName = 'dep_id';
$def->properties['dep_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['autoresponder_id'] = new ezcPersistentObjectProperty();
$def->properties['autoresponder_id']->columnName   = 'autoresponder_id';
$def->properties['autoresponder_id']->propertyName = 'autoresponder_id';
$def->properties['autoresponder_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['requires_email'] = new ezcPersistentObjectProperty();
$def->properties['requires_email']->columnName   = 'requires_email';
$def->properties['requires_email']->propertyName = 'requires_email';
$def->properties['requires_email']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['requires_phone'] = new ezcPersistentObjectProperty();
$def->properties['requires_phone']->columnName   = 'requires_phone';
$def->properties['requires_phone']->propertyName = 'requires_phone';
$def->properties['requires_phone']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['requires_username'] = new ezcPersistentObjectProperty();
$def->properties['requires_username']->columnName   = 'requires_username';
$def->properties['requires_username']->propertyName = 'requires_username';
$def->properties['requires_username']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['show_random_operator'] = new ezcPersistentObjectProperty();
$def->properties['show_random_operator']->columnName   = 'show_random_operator';
$def->properties['show_random_operator']->propertyName = 'show_random_operator';
$def->properties['show_random_operator']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['show_on_mobile'] = new ezcPersistentObjectProperty();
$def->properties['show_on_mobile']->columnName   = 'show_on_mobile';
$def->properties['show_on_mobile']->propertyName = 'show_on_mobile';
$def->properties['show_on_mobile']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// How many times show invitation popup before it gets hidden automatically
$def->properties['hide_after_ntimes'] = new ezcPersistentObjectProperty();
$def->properties['hide_after_ntimes']->columnName   = 'hide_after_ntimes';
$def->properties['hide_after_ntimes']->propertyName = 'hide_after_ntimes';
$def->properties['hide_after_ntimes']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Referrer
$def->properties['referrer'] = new ezcPersistentObjectProperty();
$def->properties['referrer']->columnName   = 'referrer';
$def->properties['referrer']->propertyName = 'referrer';
$def->properties['referrer']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Message returning
$def->properties['message_returning'] = new ezcPersistentObjectProperty();
$def->properties['message_returning']->columnName   = 'message_returning';
$def->properties['message_returning']->propertyName = 'message_returning';
$def->properties['message_returning']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// If we won't find returning visitor chat or hist previous chat nick, this variable will be replaced for nick
$def->properties['message_returning_nick'] = new ezcPersistentObjectProperty();
$def->properties['message_returning_nick']->columnName   = 'message_returning_nick';
$def->properties['message_returning_nick']->propertyName = 'message_returning_nick';
$def->properties['message_returning_nick']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Tag for proactive
$def->properties['tag'] = new ezcPersistentObjectProperty();
$def->properties['tag']->columnName   = 'tag';
$def->properties['tag']->propertyName = 'tag';
$def->properties['tag']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Is it dynamic invitation
$def->properties['dynamic_invitation'] = new ezcPersistentObjectProperty();
$def->properties['dynamic_invitation']->columnName   = 'dynamic_invitation';
$def->properties['dynamic_invitation']->propertyName = 'dynamic_invitation';
$def->properties['dynamic_invitation']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Mouse iddle timeout
$def->properties['iddle_for'] = new ezcPersistentObjectProperty();
$def->properties['iddle_for']->columnName   = 'iddle_for';
$def->properties['iddle_for']->propertyName = 'iddle_for';
$def->properties['iddle_for']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Dynamic event type
$def->properties['event_type'] = new ezcPersistentObjectProperty();
$def->properties['event_type']->columnName   = 'event_type';
$def->properties['event_type']->propertyName = 'event_type';
$def->properties['event_type']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Event invitation
$def->properties['event_invitation'] = new ezcPersistentObjectProperty();
$def->properties['event_invitation']->columnName   = 'event_invitation';
$def->properties['event_invitation']->propertyName = 'event_invitation';
$def->properties['event_invitation']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Bot ID
$def->properties['bot_id'] = new ezcPersistentObjectProperty();
$def->properties['bot_id']->columnName   = 'bot_id';
$def->properties['bot_id']->propertyName = 'bot_id';
$def->properties['bot_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Bot trigger id to send
$def->properties['trigger_id'] = new ezcPersistentObjectProperty();
$def->properties['trigger_id']->columnName   = 'trigger_id';
$def->properties['trigger_id']->propertyName = 'trigger_id';
$def->properties['trigger_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Execute bot only if there is no online operators
$def->properties['bot_offline'] = new ezcPersistentObjectProperty();
$def->properties['bot_offline']->columnName   = 'bot_offline';
$def->properties['bot_offline']->propertyName = 'bot_offline';
$def->properties['bot_offline']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Execute bot only if there is no online operators
$def->properties['disabled'] = new ezcPersistentObjectProperty();
$def->properties['disabled']->columnName   = 'disabled';
$def->properties['disabled']->propertyName = 'disabled';
$def->properties['disabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Campaign tracking
$def->properties['campaign_id'] = new ezcPersistentObjectProperty();
$def->properties['campaign_id']->columnName   = 'campaign_id';
$def->properties['campaign_id']->propertyName = 'campaign_id';
$def->properties['campaign_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['design_data'] = new ezcPersistentObjectProperty();
$def->properties['design_data']->columnName   = 'design_data';
$def->properties['design_data']->propertyName = 'design_data';
$def->properties['design_data']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>