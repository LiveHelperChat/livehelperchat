<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_speech_language_dialect";
$def->class = "erLhcoreClassModelSpeechLanguageDialect";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['language_id'] = new ezcPersistentObjectProperty();
$def->properties['language_id']->columnName   = 'language_id';
$def->properties['language_id']->propertyName = 'language_id';
$def->properties['language_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['lang_name'] = new ezcPersistentObjectProperty();
$def->properties['lang_name']->columnName   = 'lang_name';
$def->properties['lang_name']->propertyName = 'lang_name';
$def->properties['lang_name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['lang_code'] = new ezcPersistentObjectProperty();
$def->properties['lang_code']->columnName   = 'lang_code';
$def->properties['lang_code']->propertyName = 'lang_code';
$def->properties['lang_code']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>