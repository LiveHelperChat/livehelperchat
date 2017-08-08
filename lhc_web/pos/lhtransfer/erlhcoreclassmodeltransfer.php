<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_transfer";
$def->class = "erLhcoreClassModelTransfer";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['chat_id'] = new ezcPersistentObjectProperty();
$def->properties['chat_id']->columnName   = 'chat_id';
$def->properties['chat_id']->propertyName = 'chat_id';
$def->properties['chat_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Destination department
$def->properties['dep_id'] = new ezcPersistentObjectProperty();
$def->properties['dep_id']->columnName   = 'dep_id';
$def->properties['dep_id']->propertyName = 'dep_id';
$def->properties['dep_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// What user transfered chat
$def->properties['transfer_user_id'] = new ezcPersistentObjectProperty();
$def->properties['transfer_user_id']->columnName   = 'transfer_user_id';
$def->properties['transfer_user_id']->propertyName = 'transfer_user_id';
$def->properties['transfer_user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// From what department chat was transfered
$def->properties['from_dep_id'] = new ezcPersistentObjectProperty();
$def->properties['from_dep_id']->columnName   = 'from_dep_id';
$def->properties['from_dep_id']->propertyName = 'from_dep_id';
$def->properties['from_dep_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// From what department chat was transfered
$def->properties['transfer_to_user_id'] = new ezcPersistentObjectProperty();
$def->properties['transfer_to_user_id']->columnName   = 'transfer_to_user_id';
$def->properties['transfer_to_user_id']->propertyName = 'transfer_to_user_id';
$def->properties['transfer_to_user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// From what department chat was transfered
$def->properties['ctime'] = new ezcPersistentObjectProperty();
$def->properties['ctime']->columnName   = 'ctime';
$def->properties['ctime']->propertyName = 'ctime';
$def->properties['ctime']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>