<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_generic_bot_tr_item";
$def->class = "erLhcoreClassModelGenericBotTrItem";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['group_id'] = new ezcPersistentObjectProperty();
$def->properties['group_id']->columnName   = 'group_id';
$def->properties['group_id']->propertyName = 'group_id';
$def->properties['group_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['identifier'] = new ezcPersistentObjectProperty();
$def->properties['identifier']->columnName   = 'identifier';
$def->properties['identifier']->propertyName = 'identifier';
$def->properties['identifier']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['translation'] = new ezcPersistentObjectProperty();
$def->properties['translation']->columnName   = 'translation';
$def->properties['translation']->propertyName = 'translation';
$def->properties['translation']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>