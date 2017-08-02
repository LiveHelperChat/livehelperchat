<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_chat_start_settings";
$def->class = "erLhcoreClassModelChatStartSettings";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['data'] = new ezcPersistentObjectProperty();
$def->properties['data']->columnName   = 'data';
$def->properties['data']->propertyName = 'data';
$def->properties['data']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['department_id'] = new ezcPersistentObjectProperty();
$def->properties['department_id']->columnName   = 'department_id';
$def->properties['department_id']->propertyName = 'department_id';
$def->properties['department_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>