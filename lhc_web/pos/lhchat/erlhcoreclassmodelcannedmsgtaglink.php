<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_canned_msg_tag_link";
$def->class = "erLhcoreClassModelCannedMsgTagLink";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['tag_id'] = new ezcPersistentObjectProperty();
$def->properties['tag_id']->columnName   = 'tag_id';
$def->properties['tag_id']->propertyName = 'tag_id';
$def->properties['tag_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['canned_id'] = new ezcPersistentObjectProperty();
$def->properties['canned_id']->columnName   = 'canned_id';
$def->properties['canned_id']->propertyName = 'canned_id';
$def->properties['canned_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>