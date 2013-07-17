<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_chat_online_user_footprint";
$def->class = "erLhcoreClassModelChatOnlineUserFootprint";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['chat_id'] = new ezcPersistentObjectProperty();
$def->properties['chat_id']->columnName   = 'chat_id';
$def->properties['chat_id']->propertyName = 'chat_id';
$def->properties['chat_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['online_user_id'] = new ezcPersistentObjectProperty();
$def->properties['online_user_id']->columnName   = 'online_user_id';
$def->properties['online_user_id']->propertyName = 'online_user_id';
$def->properties['online_user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['page'] = new ezcPersistentObjectProperty();
$def->properties['page']->columnName   = 'page';
$def->properties['page']->propertyName = 'page';
$def->properties['page']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['vtime'] = new ezcPersistentObjectProperty();
$def->properties['vtime']->columnName   = 'vtime';
$def->properties['vtime']->propertyName = 'vtime';
$def->properties['vtime']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>