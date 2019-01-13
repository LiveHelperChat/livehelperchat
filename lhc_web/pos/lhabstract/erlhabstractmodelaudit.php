<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_audits";
$def->class = "erLhAbstractModelAudit";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['category'] = new ezcPersistentObjectProperty();
$def->properties['category']->columnName   = 'category';
$def->properties['category']->propertyName = 'category';
$def->properties['category']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['file'] = new ezcPersistentObjectProperty();
$def->properties['file']->columnName   = 'file';
$def->properties['file']->propertyName = 'file';
$def->properties['file']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['line'] = new ezcPersistentObjectProperty();
$def->properties['line']->columnName   = 'line';
$def->properties['line']->propertyName = 'line';
$def->properties['line']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['message'] = new ezcPersistentObjectProperty();
$def->properties['message']->columnName   = 'message';
$def->properties['message']->propertyName = 'message';
$def->properties['message']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['severity'] = new ezcPersistentObjectProperty();
$def->properties['severity']->columnName   = 'severity';
$def->properties['severity']->propertyName = 'severity';
$def->properties['severity']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['source'] = new ezcPersistentObjectProperty();
$def->properties['source']->columnName   = 'source';
$def->properties['source']->propertyName = 'source';
$def->properties['source']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['time'] = new ezcPersistentObjectProperty();
$def->properties['time']->columnName   = 'time';
$def->properties['time']->propertyName = 'time';
$def->properties['time']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['object_id'] = new ezcPersistentObjectProperty();
$def->properties['object_id']->columnName   = 'object_id';
$def->properties['object_id']->propertyName = 'object_id';
$def->properties['object_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>