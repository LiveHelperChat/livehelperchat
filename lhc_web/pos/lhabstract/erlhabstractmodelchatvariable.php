<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_chat_variable";
$def->class = "erLhAbstractModelChatVariable";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['dep_id'] = new ezcPersistentObjectProperty();
$def->properties['dep_id']->columnName   = 'dep_id';
$def->properties['dep_id']->propertyName = 'dep_id';
$def->properties['dep_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['js_variable'] = new ezcPersistentObjectProperty();
$def->properties['js_variable']->columnName   = 'js_variable';
$def->properties['js_variable']->propertyName = 'js_variable';
$def->properties['js_variable']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['var_name'] = new ezcPersistentObjectProperty();
$def->properties['var_name']->columnName   = 'var_name';
$def->properties['var_name']->propertyName = 'var_name';
$def->properties['var_name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['var_identifier'] = new ezcPersistentObjectProperty();
$def->properties['var_identifier']->columnName   = 'var_identifier';
$def->properties['var_identifier']->propertyName = 'var_identifier';
$def->properties['var_identifier']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['type'] = new ezcPersistentObjectProperty();
$def->properties['type']->columnName   = 'type';
$def->properties['type']->propertyName = 'type';
$def->properties['type']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['persistent'] = new ezcPersistentObjectProperty();
$def->properties['persistent']->columnName   = 'persistent';
$def->properties['persistent']->propertyName = 'persistent';
$def->properties['persistent']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>