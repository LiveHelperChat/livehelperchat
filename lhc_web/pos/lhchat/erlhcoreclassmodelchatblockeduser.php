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

return $def; 

?>