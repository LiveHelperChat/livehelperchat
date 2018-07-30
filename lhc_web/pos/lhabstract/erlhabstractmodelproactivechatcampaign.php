<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_proactive_chat_campaign";
$def->class = "erLhAbstractModelProactiveChatCampaign";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['text'] = new ezcPersistentObjectProperty();
$def->properties['text']->columnName   = 'text';
$def->properties['text']->propertyName = 'text';
$def->properties['text']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>