<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_generic_bot_rest_api_cache";
$def->class = "erLhcoreClassModelGenericBotRestAPICache";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'hash';
$def->idProperty->propertyName = 'hash';
$def->idProperty->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentManualGenerator', ['check_persistent' => false]);

$def->properties['rest_api_id'] = new ezcPersistentObjectProperty();
$def->properties['rest_api_id']->columnName   = 'rest_api_id';
$def->properties['rest_api_id']->propertyName = 'rest_api_id';
$def->properties['rest_api_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['ctime'] = new ezcPersistentObjectProperty();
$def->properties['ctime']->columnName   = 'ctime';
$def->properties['ctime']->propertyName = 'ctime';
$def->properties['ctime']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['response'] = new ezcPersistentObjectProperty();
$def->properties['response']->columnName   = 'response';
$def->properties['response']->propertyName = 'response';
$def->properties['response']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>