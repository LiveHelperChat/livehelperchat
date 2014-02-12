<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_question";
$def->class = "erLhcoreClassModelQuestion";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['question'] = new ezcPersistentObjectProperty();
$def->properties['question']->columnName   = 'question';
$def->properties['question']->propertyName = 'question';
$def->properties['question']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['question_intro'] = new ezcPersistentObjectProperty();
$def->properties['question_intro']->columnName   = 'question_intro';
$def->properties['question_intro']->propertyName = 'question_intro';
$def->properties['question_intro']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['location'] = new ezcPersistentObjectProperty();
$def->properties['location']->columnName   = 'location';
$def->properties['location']->propertyName = 'location';
$def->properties['location']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['active'] = new ezcPersistentObjectProperty();
$def->properties['active']->columnName   = 'active';
$def->properties['active']->propertyName = 'active';
$def->properties['active']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['priority'] = new ezcPersistentObjectProperty();
$def->properties['priority']->columnName   = 'priority';
$def->properties['priority']->propertyName = 'priority';
$def->properties['priority']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['is_voting'] = new ezcPersistentObjectProperty();
$def->properties['is_voting']->columnName   = 'is_voting';
$def->properties['is_voting']->propertyName = 'is_voting';
$def->properties['is_voting']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['revote'] = new ezcPersistentObjectProperty();
$def->properties['revote']->columnName   = 'revote';
$def->properties['revote']->propertyName = 'revote';
$def->properties['revote']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>