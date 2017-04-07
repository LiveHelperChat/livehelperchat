<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_canned_msg_tag";
$def->class = "erLhcoreClassModelCannedMsgTag";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['tag'] = new ezcPersistentObjectProperty();
$def->properties['tag']->columnName   = 'tag';
$def->properties['tag']->propertyName = 'tag';
$def->properties['tag']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>