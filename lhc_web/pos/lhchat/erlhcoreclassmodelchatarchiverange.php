<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_chat_archive_range";
$def->class = "erLhcoreClassModelChatArchiveRange";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['range_from'] = new ezcPersistentObjectProperty();
$def->properties['range_from']->columnName   = 'range_from';
$def->properties['range_from']->propertyName = 'range_from';
$def->properties['range_from']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['range_to'] = new ezcPersistentObjectProperty();
$def->properties['range_to']->columnName   = 'range_to';
$def->properties['range_to']->propertyName = 'range_to';
$def->properties['range_to']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Year month then archive was created. For archiving every month.
$def->properties['year_month'] = new ezcPersistentObjectProperty();
$def->properties['year_month']->columnName   = 'year_month';
$def->properties['year_month']->propertyName = 'year_month';
$def->properties['year_month']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// We store days limit instantly
$def->properties['older_than'] = new ezcPersistentObjectProperty();
$def->properties['older_than']->columnName   = 'older_than';
$def->properties['older_than']->propertyName = 'older_than';
$def->properties['older_than']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Last archived chat id
$def->properties['last_id'] = new ezcPersistentObjectProperty();
$def->properties['last_id']->columnName   = 'last_id';
$def->properties['last_id']->propertyName = 'last_id';
$def->properties['last_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Last archived chat id
$def->properties['first_id'] = new ezcPersistentObjectProperty();
$def->properties['first_id']->columnName   = 'first_id';
$def->properties['first_id']->propertyName = 'first_id';
$def->properties['first_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>