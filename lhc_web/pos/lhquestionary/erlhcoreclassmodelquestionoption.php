<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_question_option";
$def->class = "erLhcoreClassModelQuestionOption";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['question_id'] = new ezcPersistentObjectProperty();
$def->properties['question_id']->columnName   = 'question_id';
$def->properties['question_id']->propertyName = 'question_id';
$def->properties['question_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['option_name'] = new ezcPersistentObjectProperty();
$def->properties['option_name']->columnName   = 'option_name';
$def->properties['option_name']->propertyName = 'option_name';
$def->properties['option_name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['priority'] = new ezcPersistentObjectProperty();
$def->properties['priority']->columnName   = 'priority';
$def->properties['priority']->propertyName = 'priority';
$def->properties['priority']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>