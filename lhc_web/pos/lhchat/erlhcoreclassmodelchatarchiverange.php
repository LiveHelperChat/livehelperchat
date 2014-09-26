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

return $def;

?>