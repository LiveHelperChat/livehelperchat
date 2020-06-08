<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_group_chat_member";
$def->class = "erLhcoreClassModelGroupChatMember";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['user_id'] = new ezcPersistentObjectProperty();
$def->properties['user_id']->columnName   = 'user_id';
$def->properties['user_id']->propertyName = 'user_id';
$def->properties['user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['group_id'] = new ezcPersistentObjectProperty();
$def->properties['group_id']->columnName   = 'group_id';
$def->properties['group_id']->propertyName = 'group_id';
$def->properties['group_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['last_activity'] = new ezcPersistentObjectProperty();
$def->properties['last_activity']->columnName   = 'last_activity';
$def->properties['last_activity']->propertyName = 'last_activity';
$def->properties['last_activity']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Join time
$def->properties['jtime'] = new ezcPersistentObjectProperty();
$def->properties['jtime']->columnName   = 'jtime';
$def->properties['jtime']->propertyName = 'jtime';
$def->properties['jtime']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Last message id operator has fetched
$def->properties['last_msg_id'] = new ezcPersistentObjectProperty();
$def->properties['last_msg_id']->columnName   = 'last_msg_id';
$def->properties['last_msg_id']->propertyName = 'last_msg_id';
$def->properties['last_msg_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>