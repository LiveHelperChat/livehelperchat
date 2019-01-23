<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_chat_column";
$def->class = "erLhAbstractModelChatColumn";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['column_name'] = new ezcPersistentObjectProperty();
$def->properties['column_name']->columnName   = 'column_name';
$def->properties['column_name']->propertyName = 'column_name';
$def->properties['column_name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['column_icon'] = new ezcPersistentObjectProperty();
$def->properties['column_icon']->columnName   = 'column_icon';
$def->properties['column_icon']->propertyName = 'column_icon';
$def->properties['column_icon']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['column_identifier'] = new ezcPersistentObjectProperty();
$def->properties['column_identifier']->columnName   = 'column_identifier';
$def->properties['column_identifier']->propertyName = 'column_identifier';
$def->properties['column_identifier']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['variable'] = new ezcPersistentObjectProperty();
$def->properties['variable']->columnName   = 'variable';
$def->properties['variable']->propertyName = 'variable';
$def->properties['variable']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['position'] = new ezcPersistentObjectProperty();
$def->properties['position']->columnName   = 'position';
$def->properties['position']->propertyName = 'position';
$def->properties['position']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['enabled'] = new ezcPersistentObjectProperty();
$def->properties['enabled']->columnName   = 'enabled';
$def->properties['enabled']->propertyName = 'enabled';
$def->properties['enabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['conditions'] = new ezcPersistentObjectProperty();
$def->properties['conditions']->columnName   = 'conditions';
$def->properties['conditions']->propertyName = 'conditions';
$def->properties['conditions']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['chat_enabled'] = new ezcPersistentObjectProperty();
$def->properties['chat_enabled']->columnName   = 'chat_enabled';
$def->properties['chat_enabled']->propertyName = 'chat_enabled';
$def->properties['chat_enabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['online_enabled'] = new ezcPersistentObjectProperty();
$def->properties['online_enabled']->columnName   = 'online_enabled';
$def->properties['online_enabled']->propertyName = 'online_enabled';
$def->properties['online_enabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>