<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_userdep";
$def->class = "erLhcoreClassModelUserDep";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['user_id'] = new ezcPersistentObjectProperty();
$def->properties['user_id']->columnName   = 'user_id';
$def->properties['user_id']->propertyName = 'user_id';
$def->properties['user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT; 

$def->properties['dep_id'] = new ezcPersistentObjectProperty();
$def->properties['dep_id']->columnName   = 'dep_id';
$def->properties['dep_id']->propertyName = 'dep_id';
$def->properties['dep_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT; 

$def->properties['last_activity'] = new ezcPersistentObjectProperty();
$def->properties['last_activity']->columnName   = 'last_activity';
$def->properties['last_activity']->propertyName = 'last_activity';
$def->properties['last_activity']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT; 

$def->properties['hide_online_ts'] = new ezcPersistentObjectProperty();
$def->properties['hide_online_ts']->columnName   = 'hide_online_ts';
$def->properties['hide_online_ts']->propertyName = 'hide_online_ts';
$def->properties['hide_online_ts']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT; 

$def->properties['hide_online'] = new ezcPersistentObjectProperty();
$def->properties['hide_online']->columnName   = 'hide_online';
$def->properties['hide_online']->propertyName = 'hide_online';
$def->properties['hide_online']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT; 

$def->properties['last_accepted'] = new ezcPersistentObjectProperty();
$def->properties['last_accepted']->columnName   = 'last_accepted';
$def->properties['last_accepted']->propertyName = 'last_accepted';
$def->properties['last_accepted']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT; 

$def->properties['active_chats'] = new ezcPersistentObjectProperty();
$def->properties['active_chats']->columnName   = 'active_chats';
$def->properties['active_chats']->propertyName = 'active_chats';
$def->properties['active_chats']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT; 

return $def; 

?>