<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_rest_api_key";
$def->class = "erLhAbstractModelRestAPIKey";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['user_id'] = new ezcPersistentObjectProperty();
$def->properties['user_id']->columnName   = 'user_id';
$def->properties['user_id']->propertyName = 'user_id';
$def->properties['user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['api_key'] = new ezcPersistentObjectProperty();
$def->properties['api_key']->columnName   = 'api_key';
$def->properties['api_key']->propertyName = 'api_key';
$def->properties['api_key']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['active'] = new ezcPersistentObjectProperty();
$def->properties['active']->columnName   = 'active';
$def->properties['active']->propertyName = 'active';
$def->properties['active']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>