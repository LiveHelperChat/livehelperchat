<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_chat_blocked_user";
$def->class = "erLhcoreClassModelChatBlockedUser";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['ip'] = new ezcPersistentObjectProperty();
$def->properties['ip']->columnName   = 'ip';
$def->properties['ip']->propertyName = 'ip';
$def->properties['ip']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING; 

$def->properties['user_id'] = new ezcPersistentObjectProperty();
$def->properties['user_id']->columnName   = 'user_id';
$def->properties['user_id']->propertyName = 'user_id';
$def->properties['user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['datets'] = new ezcPersistentObjectProperty();
$def->properties['datets']->columnName   = 'datets';
$def->properties['datets']->propertyName = 'datets';
$def->properties['datets']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['chat_id'] = new ezcPersistentObjectProperty();
$def->properties['chat_id']->columnName   = 'chat_id';
$def->properties['chat_id']->propertyName = 'chat_id';
$def->properties['chat_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['dep_id'] = new ezcPersistentObjectProperty();
$def->properties['dep_id']->columnName   = 'dep_id';
$def->properties['dep_id']->propertyName = 'dep_id';
$def->properties['dep_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['nick'] = new ezcPersistentObjectProperty();
$def->properties['nick']->columnName   = 'nick';
$def->properties['nick']->propertyName = 'nick';
$def->properties['nick']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['btype'] = new ezcPersistentObjectProperty();
$def->properties['btype']->columnName   = 'btype';
$def->properties['btype']->propertyName = 'btype';
$def->properties['btype']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['expires'] = new ezcPersistentObjectProperty();
$def->properties['expires']->columnName   = 'expires';
$def->properties['expires']->propertyName = 'expires';
$def->properties['expires']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def; 

?>