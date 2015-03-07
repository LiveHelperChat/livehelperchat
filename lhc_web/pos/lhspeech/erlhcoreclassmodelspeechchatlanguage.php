<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_speech_chat_language";
$def->class = "erLhcoreClassModelSpeechChatLanguage";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['chat_id'] = new ezcPersistentObjectProperty();
$def->properties['chat_id']->columnName   = 'chat_id';
$def->properties['chat_id']->propertyName = 'chat_id';
$def->properties['chat_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['language_id'] = new ezcPersistentObjectProperty();
$def->properties['language_id']->columnName   = 'language_id';
$def->properties['language_id']->propertyName = 'language_id';
$def->properties['language_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['dialect'] = new ezcPersistentObjectProperty();
$def->properties['dialect']->columnName   = 'dialect';
$def->properties['dialect']->propertyName = 'dialect';
$def->properties['dialect']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>