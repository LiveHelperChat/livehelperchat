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

$def->properties['wait_message'] = new ezcPersistentObjectProperty();
$def->properties['wait_message']->columnName   = 'wait_message';
$def->properties['wait_message']->propertyName = 'wait_message';
$def->properties['wait_message']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Department ID
$def->properties['dep_id'] = new ezcPersistentObjectProperty();
$def->properties['dep_id']->columnName   = 'dep_id';
$def->properties['dep_id']->propertyName = 'dep_id';
$def->properties['dep_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Timeout in seconds.
$def->properties['wait_timeout'] = new ezcPersistentObjectProperty();
$def->properties['wait_timeout']->columnName   = 'wait_timeout';
$def->properties['wait_timeout']->propertyName = 'wait_timeout';
$def->properties['wait_timeout']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['repeat_number'] = new ezcPersistentObjectProperty();
$def->properties['repeat_number']->columnName   = 'repeat_number';
$def->properties['repeat_number']->propertyName = 'repeat_number';
$def->properties['repeat_number']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

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

// Then timeout passes show visitor this message.
$def->properties['timeout_message'] = new ezcPersistentObjectProperty();
$def->properties['timeout_message']->columnName   = 'timeout_message';
$def->properties['timeout_message']->propertyName = 'timeout_message';
$def->properties['timeout_message']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

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


return $def;

?>