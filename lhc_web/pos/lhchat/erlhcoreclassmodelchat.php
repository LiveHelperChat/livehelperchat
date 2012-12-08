<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_chat";
$def->class = "erLhcoreClassModelChat";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentSequenceGenerator' );

$def->properties['nick'] = new ezcPersistentObjectProperty();
$def->properties['nick']->columnName   = 'nick';
$def->properties['nick']->propertyName = 'nick';
$def->properties['nick']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING; 

$def->properties['status'] = new ezcPersistentObjectProperty();
$def->properties['status']->columnName   = 'status';
$def->properties['status']->propertyName = 'status';
$def->properties['status']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT; 

$def->properties['time'] = new ezcPersistentObjectProperty();
$def->properties['time']->columnName   = 'time';
$def->properties['time']->propertyName = 'time';
$def->properties['time']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT; 

$def->properties['user_id'] = new ezcPersistentObjectProperty();
$def->properties['user_id']->columnName   = 'user_id';
$def->properties['user_id']->propertyName = 'user_id';
$def->properties['user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT; 

$def->properties['dep_id'] = new ezcPersistentObjectProperty();
$def->properties['dep_id']->columnName   = 'dep_id';
$def->properties['dep_id']->propertyName = 'dep_id';
$def->properties['dep_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT; 

$def->properties['user_status'] = new ezcPersistentObjectProperty();
$def->properties['user_status']->columnName   = 'user_status';
$def->properties['user_status']->propertyName = 'user_status';
$def->properties['user_status']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT; 

$def->properties['support_informed'] = new ezcPersistentObjectProperty();
$def->properties['support_informed']->columnName   = 'support_informed';
$def->properties['support_informed']->propertyName = 'support_informed';
$def->properties['support_informed']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT; 

$def->properties['hash'] = new ezcPersistentObjectProperty();
$def->properties['hash']->columnName   = 'hash';
$def->properties['hash']->propertyName = 'hash';
$def->properties['hash']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING; 

$def->properties['ip'] = new ezcPersistentObjectProperty();
$def->properties['ip']->columnName   = 'ip';
$def->properties['ip']->propertyName = 'ip';
$def->properties['ip']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING; 

$def->properties['email'] = new ezcPersistentObjectProperty();
$def->properties['email']->columnName   = 'email';
$def->properties['email']->propertyName = 'email';
$def->properties['email']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING; 

$def->properties['referrer'] = new ezcPersistentObjectProperty();
$def->properties['referrer']->columnName   = 'referrer';
$def->properties['referrer']->propertyName = 'referrer';
$def->properties['referrer']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING; 


return $def; 

?>