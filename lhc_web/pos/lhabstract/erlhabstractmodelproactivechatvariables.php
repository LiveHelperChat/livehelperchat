<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_proactive_chat_variables";
$def->class = "erLhAbstractModelProactiveChatVariables";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['identifier'] = new ezcPersistentObjectProperty();
$def->properties['identifier']->columnName   = 'identifier';
$def->properties['identifier']->propertyName = 'identifier';
$def->properties['identifier']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['store_timeout'] = new ezcPersistentObjectProperty();
$def->properties['store_timeout']->columnName   = 'store_timeout';
$def->properties['store_timeout']->propertyName = 'store_timeout';
$def->properties['store_timeout']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['filter_val'] = new ezcPersistentObjectProperty();
$def->properties['filter_val']->columnName   = 'filter_val';
$def->properties['filter_val']->propertyName = 'filter_val';
$def->properties['filter_val']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>