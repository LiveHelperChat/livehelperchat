<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_chat_online_user";
$def->class = "erLhcoreClassModelChatOnlineUser";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentSequenceGenerator' );

$def->properties['vid'] = new ezcPersistentObjectProperty();
$def->properties['vid']->columnName   = 'vid';
$def->properties['vid']->propertyName = 'vid';
$def->properties['vid']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING; 

$def->properties['ip'] = new ezcPersistentObjectProperty();
$def->properties['ip']->columnName   = 'ip';
$def->properties['ip']->propertyName = 'ip';
$def->properties['ip']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['current_page'] = new ezcPersistentObjectProperty();
$def->properties['current_page']->columnName   = 'current_page';
$def->properties['current_page']->propertyName = 'current_page';
$def->properties['current_page']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING; 

$def->properties['chat_id'] = new ezcPersistentObjectProperty();
$def->properties['chat_id']->columnName   = 'chat_id';
$def->properties['chat_id']->propertyName = 'chat_id';
$def->properties['chat_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['last_visit'] = new ezcPersistentObjectProperty();
$def->properties['last_visit']->columnName   = 'last_visit';
$def->properties['last_visit']->propertyName = 'last_visit';
$def->properties['last_visit']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['user_agent'] = new ezcPersistentObjectProperty();
$def->properties['user_agent']->columnName   = 'user_agent';
$def->properties['user_agent']->propertyName = 'user_agent';
$def->properties['user_agent']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['user_country_code'] = new ezcPersistentObjectProperty();
$def->properties['user_country_code']->columnName   = 'user_country_code';
$def->properties['user_country_code']->propertyName = 'user_country_code';
$def->properties['user_country_code']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
 
$def->properties['user_country_name'] = new ezcPersistentObjectProperty();
$def->properties['user_country_name']->columnName   = 'user_country_name';
$def->properties['user_country_name']->propertyName = 'user_country_name';
$def->properties['user_country_name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
 
// Operator message
$def->properties['operator_message'] = new ezcPersistentObjectProperty();
$def->properties['operator_message']->columnName   = 'operator_message';
$def->properties['operator_message']->propertyName = 'operator_message';
$def->properties['operator_message']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Which user has send the message
$def->properties['operator_user_id'] = new ezcPersistentObjectProperty();
$def->properties['operator_user_id']->columnName   = 'operator_user_id';
$def->properties['operator_user_id']->propertyName = 'operator_user_id';
$def->properties['operator_user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Have the user seen the message and closed the window
$def->properties['message_seen'] = new ezcPersistentObjectProperty();
$def->properties['message_seen']->columnName   = 'message_seen';
$def->properties['message_seen']->propertyName = 'message_seen';
$def->properties['message_seen']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING; 

return $def; 

?>