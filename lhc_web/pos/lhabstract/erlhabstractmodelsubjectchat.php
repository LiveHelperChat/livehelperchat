<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_subject_chat";
$def->class = "erLhAbstractModelSubjectChat";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['subject_id'] = new ezcPersistentObjectProperty();
$def->properties['subject_id']->columnName   = 'subject_id';
$def->properties['subject_id']->propertyName = 'subject_id';
$def->properties['subject_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['chat_id'] = new ezcPersistentObjectProperty();
$def->properties['chat_id']->columnName   = 'chat_id';
$def->properties['chat_id']->propertyName = 'chat_id';
$def->properties['chat_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>