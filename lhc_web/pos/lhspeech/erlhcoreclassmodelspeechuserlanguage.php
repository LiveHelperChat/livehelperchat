<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_speech_user_language";
$def->class = "erLhcoreClassModelSpeechUserLanguage";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['user_id'] = new ezcPersistentObjectProperty();
$def->properties['user_id']->columnName   = 'user_id';
$def->properties['user_id']->propertyName = 'user_id';
$def->properties['user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['language'] = new ezcPersistentObjectProperty();
$def->properties['language']->columnName   = 'language';
$def->properties['language']->propertyName = 'language';
$def->properties['language']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>