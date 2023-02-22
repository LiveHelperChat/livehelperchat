<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_survey_item";
$def->class = "erLhAbstractModelSurveyItem";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

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

foreach (['online_user_id','status','ftime','dep_id','user_id','chat_id','survey_id'] as $posAttr) {
    $def->properties[$posAttr] = new ezcPersistentObjectProperty();
    $def->properties[$posAttr]->columnName   = $posAttr;
    $def->properties[$posAttr]->propertyName = $posAttr;
    $def->properties[$posAttr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

return $def;

?>