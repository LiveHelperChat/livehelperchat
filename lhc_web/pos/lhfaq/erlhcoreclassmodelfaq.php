<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_faq";
$def->class = "erLhcoreClassModelFaq";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['question'] = new ezcPersistentObjectProperty();
$def->properties['question']->columnName   = 'question';
$def->properties['question']->propertyName = 'question';
$def->properties['question']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['answer'] = new ezcPersistentObjectProperty();
$def->properties['answer']->columnName   = 'answer';
$def->properties['answer']->propertyName = 'answer';
$def->properties['answer']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['url'] = new ezcPersistentObjectProperty();
$def->properties['url']->columnName   = 'url';
$def->properties['url']->propertyName = 'url';
$def->properties['url']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['active'] = new ezcPersistentObjectProperty();
$def->properties['active']->columnName   = 'active';
$def->properties['active']->propertyName = 'active';
$def->properties['active']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['has_url'] = new ezcPersistentObjectProperty();
$def->properties['has_url']->columnName   = 'has_url';
$def->properties['has_url']->propertyName = 'has_url';
$def->properties['has_url']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>