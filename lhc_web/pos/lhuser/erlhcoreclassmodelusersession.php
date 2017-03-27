<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_users_session";
$def->class = "erLhcoreClassModelUserSession";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['token'] = new ezcPersistentObjectProperty();
$def->properties['token']->columnName   = 'token';
$def->properties['token']->propertyName = 'token';
$def->properties['token']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['device_type'] = new ezcPersistentObjectProperty();
$def->properties['device_type']->columnName   = 'device_type';
$def->properties['device_type']->propertyName = 'device_type';
$def->properties['device_type']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['device_token'] = new ezcPersistentObjectProperty();
$def->properties['device_token']->columnName   = 'device_token';
$def->properties['device_token']->propertyName = 'device_token';
$def->properties['device_token']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['user_id'] = new ezcPersistentObjectProperty();
$def->properties['user_id']->columnName   = 'user_id';
$def->properties['user_id']->propertyName = 'user_id';
$def->properties['user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['created_on'] = new ezcPersistentObjectProperty();
$def->properties['created_on']->columnName   = 'created_on';
$def->properties['created_on']->propertyName = 'created_on';
$def->properties['created_on']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['updated_on'] = new ezcPersistentObjectProperty();
$def->properties['updated_on']->columnName   = 'updated_on';
$def->properties['updated_on']->propertyName = 'updated_on';
$def->properties['updated_on']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['expires_on'] = new ezcPersistentObjectProperty();
$def->properties['expires_on']->columnName   = 'expires_on';
$def->properties['expires_on']->propertyName = 'expires_on';
$def->properties['expires_on']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>