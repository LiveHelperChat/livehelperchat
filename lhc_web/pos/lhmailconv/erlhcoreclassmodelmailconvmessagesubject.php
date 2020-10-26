<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_mailconv_msg_subject";
$def->class = "erLhcoreClassModelMailconvMessageSubject";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['subject_id'] = new ezcPersistentObjectProperty();
$def->properties['subject_id']->columnName   = 'subject_id';
$def->properties['subject_id']->propertyName = 'subject_id';
$def->properties['subject_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['message_id'] = new ezcPersistentObjectProperty();
$def->properties['message_id']->columnName   = 'message_id';
$def->properties['message_id']->propertyName = 'message_id';
$def->properties['message_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['conversation_id'] = new ezcPersistentObjectProperty();
$def->properties['conversation_id']->columnName   = 'conversation_id';
$def->properties['conversation_id']->propertyName = 'conversation_id';
$def->properties['conversation_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>