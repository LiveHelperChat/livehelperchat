<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_question_option_answer";
$def->class = "erLhcoreClassModelQuestionOptionAnswer";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['question_id'] = new ezcPersistentObjectProperty();
$def->properties['question_id']->columnName   = 'question_id';
$def->properties['question_id']->propertyName = 'question_id';
$def->properties['question_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['option_id'] = new ezcPersistentObjectProperty();
$def->properties['option_id']->columnName   = 'option_id';
$def->properties['option_id']->propertyName = 'option_id';
$def->properties['option_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['ctime'] = new ezcPersistentObjectProperty();
$def->properties['ctime']->columnName   = 'ctime';
$def->properties['ctime']->propertyName = 'ctime';
$def->properties['ctime']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['ip'] = new ezcPersistentObjectProperty();
$def->properties['ip']->columnName   = 'ip';
$def->properties['ip']->propertyName = 'ip';
$def->properties['ip']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>