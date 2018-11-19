<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_rest_api_key_remote";
$def->class = "erLhAbstractModelRestAPIKeyRemote";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['host'] = new ezcPersistentObjectProperty();
$def->properties['host']->columnName   = 'host';
$def->properties['host']->propertyName = 'host';
$def->properties['host']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['api_key'] = new ezcPersistentObjectProperty();
$def->properties['api_key']->columnName   = 'api_key';
$def->properties['api_key']->propertyName = 'api_key';
$def->properties['api_key']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['username'] = new ezcPersistentObjectProperty();
$def->properties['username']->columnName   = 'username';
$def->properties['username']->propertyName = 'username';
$def->properties['username']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['position'] = new ezcPersistentObjectProperty();
$def->properties['position']->columnName   = 'position';
$def->properties['position']->propertyName = 'position';
$def->properties['position']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['active'] = new ezcPersistentObjectProperty();
$def->properties['active']->columnName   = 'active';
$def->properties['active']->propertyName = 'active';
$def->properties['active']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>