<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_survey_item";
$def->class = "erLhAbstractModelSurveyItem";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['survey_id'] = new ezcPersistentObjectProperty();
$def->properties['survey_id']->columnName   = 'survey_id';
$def->properties['survey_id']->propertyName = 'survey_id';
$def->properties['survey_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['chat_id'] = new ezcPersistentObjectProperty();
$def->properties['chat_id']->columnName   = 'chat_id';
$def->properties['chat_id']->propertyName = 'chat_id';
$def->properties['chat_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

for($i = 1; $i <= 5; $i++) {
	$def->properties['max_stars_' . $i] = new ezcPersistentObjectProperty();
	$def->properties['max_stars_' . $i]->columnName   = 'max_stars_' . $i;
	$def->properties['max_stars_' . $i]->propertyName = 'max_stars_' . $i;
	$def->properties['max_stars_' . $i]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
	
	$def->properties['question_options_' . $i] = new ezcPersistentObjectProperty();
	$def->properties['question_options_' . $i]->columnName   = 'question_options_' . $i;
	$def->properties['question_options_' . $i]->propertyName = 'question_options_' . $i;
	$def->properties['question_options_' . $i]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
	
	$def->properties['question_plain_' . $i] = new ezcPersistentObjectProperty();
	$def->properties['question_plain_' . $i]->columnName   = 'question_plain_' . $i;
	$def->properties['question_plain_' . $i]->propertyName = 'question_plain_' . $i;
	$def->properties['question_plain_' . $i]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
}

$def->properties['user_id'] = new ezcPersistentObjectProperty();
$def->properties['user_id']->columnName   = 'user_id';
$def->properties['user_id']->propertyName = 'user_id';
$def->properties['user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['dep_id'] = new ezcPersistentObjectProperty();
$def->properties['dep_id']->columnName   = 'dep_id';
$def->properties['dep_id']->propertyName = 'dep_id';
$def->properties['dep_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['ftime'] = new ezcPersistentObjectProperty();
$def->properties['ftime']->columnName   = 'ftime';
$def->properties['ftime']->propertyName = 'ftime';
$def->properties['ftime']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['status'] = new ezcPersistentObjectProperty();
$def->properties['status']->columnName   = 'status';
$def->properties['status']->propertyName = 'status';
$def->properties['status']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>