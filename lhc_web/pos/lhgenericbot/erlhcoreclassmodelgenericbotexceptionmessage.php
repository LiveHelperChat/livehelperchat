<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_generic_bot_exception_message";
$def->class = "erLhcoreClassModelGenericBotExceptionMessage";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['code'] = new ezcPersistentObjectProperty();
$def->properties['code']->columnName   = 'code';
$def->properties['code']->propertyName = 'code';
$def->properties['code']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['exception_group_id'] = new ezcPersistentObjectProperty();
$def->properties['exception_group_id']->columnName   = 'exception_group_id';
$def->properties['exception_group_id']->propertyName = 'exception_group_id';
$def->properties['exception_group_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['message'] = new ezcPersistentObjectProperty();
$def->properties['message']->columnName   = 'message';
$def->properties['message']->propertyName = 'message';
$def->properties['message']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['active'] = new ezcPersistentObjectProperty();
$def->properties['active']->columnName   = 'active';
$def->properties['active']->propertyName = 'active';
$def->properties['active']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['priority'] = new ezcPersistentObjectProperty();
$def->properties['priority']->columnName   = 'priority';
$def->properties['priority']->propertyName = 'priority';
$def->properties['priority']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>