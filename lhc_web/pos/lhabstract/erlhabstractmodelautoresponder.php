<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_auto_responder";
$def->class = "erLhAbstractModelAutoResponder";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['siteaccess'] = new ezcPersistentObjectProperty();
$def->properties['siteaccess']->columnName   = 'siteaccess';
$def->properties['siteaccess']->propertyName = 'siteaccess';
$def->properties['siteaccess']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['wait_message'] = new ezcPersistentObjectProperty();
$def->properties['wait_message']->columnName   = 'wait_message';
$def->properties['wait_message']->propertyName = 'wait_message';
$def->properties['wait_message']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Timeout in seconds.
$def->properties['wait_timeout'] = new ezcPersistentObjectProperty();
$def->properties['wait_timeout']->columnName   = 'wait_timeout';
$def->properties['wait_timeout']->propertyName = 'wait_timeout';
$def->properties['wait_timeout']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Department ID
$def->properties['dep_id'] = new ezcPersistentObjectProperty();
$def->properties['dep_id']->columnName   = 'dep_id';
$def->properties['dep_id']->propertyName = 'dep_id';
$def->properties['dep_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Position
$def->properties['position'] = new ezcPersistentObjectProperty();
$def->properties['position']->columnName   = 'position';
$def->properties['position']->propertyName = 'position';
$def->properties['position']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Then timeout passes show visitor this message.
$def->properties['timeout_message'] = new ezcPersistentObjectProperty();
$def->properties['timeout_message']->columnName   = 'timeout_message';
$def->properties['timeout_message']->propertyName = 'timeout_message';
$def->properties['timeout_message']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// How many times repeat timeout message
// 0 - infinity times
// 1 - one time
$def->properties['repeat_number'] = new ezcPersistentObjectProperty();
$def->properties['repeat_number']->columnName   = 'repeat_number';
$def->properties['repeat_number']->propertyName = 'repeat_number';
$def->properties['repeat_number']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>