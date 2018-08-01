<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_proactive_chat_campaign_conv";
$def->class = "erLhAbstractModelProactiveChatCampaignConversion";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['device_type'] = new ezcPersistentObjectProperty();
$def->properties['device_type']->columnName   = 'device_type';
$def->properties['device_type']->propertyName = 'device_type';
$def->properties['device_type']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// 0 - Manual, 1 - Automatic
$def->properties['invitation_type'] = new ezcPersistentObjectProperty();
$def->properties['invitation_type']->columnName   = 'invitation_type';
$def->properties['invitation_type']->propertyName = 'invitation_type';
$def->properties['invitation_type']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// 0 - Invitation Send
// 1 - Invitation Shown
// 2 - Invitation Seen
// 3 - Chat started
$def->properties['invitation_status'] = new ezcPersistentObjectProperty();
$def->properties['invitation_status']->columnName   = 'invitation_status';
$def->properties['invitation_status']->propertyName = 'invitation_status';
$def->properties['invitation_status']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['chat_id'] = new ezcPersistentObjectProperty();
$def->properties['chat_id']->columnName   = 'chat_id';
$def->properties['chat_id']->propertyName = 'chat_id';
$def->properties['chat_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['campaign_id'] = new ezcPersistentObjectProperty();
$def->properties['campaign_id']->columnName   = 'campaign_id';
$def->properties['campaign_id']->propertyName = 'campaign_id';
$def->properties['campaign_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['invitation_id'] = new ezcPersistentObjectProperty();
$def->properties['invitation_id']->columnName   = 'invitation_id';
$def->properties['invitation_id']->propertyName = 'invitation_id';
$def->properties['invitation_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['department_id'] = new ezcPersistentObjectProperty();
$def->properties['department_id']->columnName   = 'department_id';
$def->properties['department_id']->propertyName = 'department_id';
$def->properties['department_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Create time
$def->properties['ctime'] = new ezcPersistentObjectProperty();
$def->properties['ctime']->columnName   = 'ctime';
$def->properties['ctime']->propertyName = 'ctime';
$def->properties['ctime']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Conversion time
$def->properties['con_time'] = new ezcPersistentObjectProperty();
$def->properties['con_time']->columnName   = 'con_time';
$def->properties['con_time']->propertyName = 'con_time';
$def->properties['con_time']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Online Visitor ID
$def->properties['vid_id'] = new ezcPersistentObjectProperty();
$def->properties['vid_id']->columnName   = 'vid_id';
$def->properties['vid_id']->propertyName = 'vid_id';
$def->properties['vid_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>