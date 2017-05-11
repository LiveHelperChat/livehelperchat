<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_proactive_chat_invitation_event";
$def->class = "erLhAbstractModelProactiveChatInvitationEvent";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['invitation_id'] = new ezcPersistentObjectProperty();
$def->properties['invitation_id']->columnName   = 'invitation_id';
$def->properties['invitation_id']->propertyName = 'invitation_id';
$def->properties['invitation_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['event_id'] = new ezcPersistentObjectProperty();
$def->properties['event_id']->columnName   = 'event_id';
$def->properties['event_id']->propertyName = 'event_id';
$def->properties['event_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['min_number'] = new ezcPersistentObjectProperty();
$def->properties['min_number']->columnName   = 'min_number';
$def->properties['min_number']->propertyName = 'min_number';
$def->properties['min_number']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['during_seconds'] = new ezcPersistentObjectProperty();
$def->properties['during_seconds']->columnName   = 'during_seconds';
$def->properties['during_seconds']->propertyName = 'during_seconds';
$def->properties['during_seconds']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>