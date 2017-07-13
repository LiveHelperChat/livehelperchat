<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_users_online_session";
$def->class = "erLhcoreClassModelUserOnlineSession";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['user_id'] = new ezcPersistentObjectProperty();
$def->properties['user_id']->columnName   = 'user_id';
$def->properties['user_id']->propertyName = 'user_id';
$def->properties['user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['time'] = new ezcPersistentObjectProperty();
$def->properties['time']->columnName   = 'time';
$def->properties['time']->propertyName = 'time';
$def->properties['time']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['lactivity'] = new ezcPersistentObjectProperty();
$def->properties['lactivity']->columnName   = 'lactivity';
$def->properties['lactivity']->propertyName = 'lactivity';
$def->properties['lactivity']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['duration'] = new ezcPersistentObjectProperty();
$def->properties['duration']->columnName   = 'duration';
$def->properties['duration']->propertyName = 'duration';
$def->properties['duration']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def; 

?>