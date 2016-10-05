<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_survey";
$def->class = "erLhAbstractModelSurvey";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['max_stars_1_title'] = new ezcPersistentObjectProperty();
$def->properties['max_stars_1_title']->columnName   = 'max_stars_1_title';
$def->properties['max_stars_1_title']->propertyName = 'max_stars_1_title';
$def->properties['max_stars_1_title']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['max_stars_1'] = new ezcPersistentObjectProperty();
$def->properties['max_stars_1']->columnName   = 'max_stars_1';
$def->properties['max_stars_1']->propertyName = 'max_stars_1';
$def->properties['max_stars_1']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['max_stars_1_pos'] = new ezcPersistentObjectProperty();
$def->properties['max_stars_1_pos']->columnName   = 'max_stars_1_pos';
$def->properties['max_stars_1_pos']->propertyName = 'max_stars_1_pos';
$def->properties['max_stars_1_pos']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['max_stars_1_enabled'] = new ezcPersistentObjectProperty();
$def->properties['max_stars_1_enabled']->columnName   = 'max_stars_1_enabled';
$def->properties['max_stars_1_enabled']->propertyName = 'max_stars_1_enabled';
$def->properties['max_stars_1_enabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Max stars 2
$def->properties['max_stars_2_title'] = new ezcPersistentObjectProperty();
$def->properties['max_stars_2_title']->columnName   = 'max_stars_2_title';
$def->properties['max_stars_2_title']->propertyName = 'max_stars_2_title';
$def->properties['max_stars_2_title']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['max_stars_2'] = new ezcPersistentObjectProperty();
$def->properties['max_stars_2']->columnName   = 'max_stars_2';
$def->properties['max_stars_2']->propertyName = 'max_stars_2';
$def->properties['max_stars_2']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['max_stars_2_pos'] = new ezcPersistentObjectProperty();
$def->properties['max_stars_2_pos']->columnName   = 'max_stars_2_pos';
$def->properties['max_stars_2_pos']->propertyName = 'max_stars_2_pos';
$def->properties['max_stars_2_pos']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['max_stars_2_enabled'] = new ezcPersistentObjectProperty();
$def->properties['max_stars_2_enabled']->columnName   = 'max_stars_2_enabled';
$def->properties['max_stars_2_enabled']->propertyName = 'max_stars_2_enabled';
$def->properties['max_stars_2_enabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Max stars 3
$def->properties['max_stars_3_title'] = new ezcPersistentObjectProperty();
$def->properties['max_stars_3_title']->columnName   = 'max_stars_3_title';
$def->properties['max_stars_3_title']->propertyName = 'max_stars_3_title';
$def->properties['max_stars_3_title']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['max_stars_3'] = new ezcPersistentObjectProperty();
$def->properties['max_stars_3']->columnName   = 'max_stars_3';
$def->properties['max_stars_3']->propertyName = 'max_stars_3';
$def->properties['max_stars_3']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['max_stars_3_pos'] = new ezcPersistentObjectProperty();
$def->properties['max_stars_3_pos']->columnName   = 'max_stars_3_pos';
$def->properties['max_stars_3_pos']->propertyName = 'max_stars_3_pos';
$def->properties['max_stars_3_pos']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['max_stars_3_enabled'] = new ezcPersistentObjectProperty();
$def->properties['max_stars_3_enabled']->columnName   = 'max_stars_3_enabled';
$def->properties['max_stars_3_enabled']->propertyName = 'max_stars_3_enabled';
$def->properties['max_stars_3_enabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Max stars 4
$def->properties['max_stars_4_title'] = new ezcPersistentObjectProperty();
$def->properties['max_stars_4_title']->columnName   = 'max_stars_4_title';
$def->properties['max_stars_4_title']->propertyName = 'max_stars_4_title';
$def->properties['max_stars_4_title']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['max_stars_4'] = new ezcPersistentObjectProperty();
$def->properties['max_stars_4']->columnName   = 'max_stars_4';
$def->properties['max_stars_4']->propertyName = 'max_stars_4';
$def->properties['max_stars_4']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['max_stars_4_pos'] = new ezcPersistentObjectProperty();
$def->properties['max_stars_4_pos']->columnName   = 'max_stars_4_pos';
$def->properties['max_stars_4_pos']->propertyName = 'max_stars_4_pos';
$def->properties['max_stars_4_pos']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['max_stars_4_enabled'] = new ezcPersistentObjectProperty();
$def->properties['max_stars_4_enabled']->columnName   = 'max_stars_4_enabled';
$def->properties['max_stars_4_enabled']->propertyName = 'max_stars_4_enabled';
$def->properties['max_stars_4_enabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Max stars 5
$def->properties['max_stars_5_title'] = new ezcPersistentObjectProperty();
$def->properties['max_stars_5_title']->columnName   = 'max_stars_5_title';
$def->properties['max_stars_5_title']->propertyName = 'max_stars_5_title';
$def->properties['max_stars_5_title']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['max_stars_5'] = new ezcPersistentObjectProperty();
$def->properties['max_stars_5']->columnName   = 'max_stars_5';
$def->properties['max_stars_5']->propertyName = 'max_stars_5';
$def->properties['max_stars_5']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['max_stars_5_pos'] = new ezcPersistentObjectProperty();
$def->properties['max_stars_5_pos']->columnName   = 'max_stars_5_pos';
$def->properties['max_stars_5_pos']->propertyName = 'max_stars_5_pos';
$def->properties['max_stars_5_pos']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['max_stars_5_enabled'] = new ezcPersistentObjectProperty();
$def->properties['max_stars_5_enabled']->columnName   = 'max_stars_5_enabled';
$def->properties['max_stars_5_enabled']->propertyName = 'max_stars_5_enabled';
$def->properties['max_stars_5_enabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Question 1
$def->properties['question_options_1'] = new ezcPersistentObjectProperty();
$def->properties['question_options_1']->columnName   = 'question_options_1';
$def->properties['question_options_1']->propertyName = 'question_options_1';
$def->properties['question_options_1']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['question_options_1_items'] = new ezcPersistentObjectProperty();
$def->properties['question_options_1_items']->columnName   = 'question_options_1_items';
$def->properties['question_options_1_items']->propertyName = 'question_options_1_items';
$def->properties['question_options_1_items']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['question_options_1_pos'] = new ezcPersistentObjectProperty();
$def->properties['question_options_1_pos']->columnName   = 'question_options_1_pos';
$def->properties['question_options_1_pos']->propertyName = 'question_options_1_pos';
$def->properties['question_options_1_pos']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['question_options_1_enabled'] = new ezcPersistentObjectProperty();
$def->properties['question_options_1_enabled']->columnName   = 'question_options_1_enabled';
$def->properties['question_options_1_enabled']->propertyName = 'question_options_1_enabled';
$def->properties['question_options_1_enabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Question 2
$def->properties['question_options_2'] = new ezcPersistentObjectProperty();
$def->properties['question_options_2']->columnName   = 'question_options_2';
$def->properties['question_options_2']->propertyName = 'question_options_2';
$def->properties['question_options_2']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['question_options_2_items'] = new ezcPersistentObjectProperty();
$def->properties['question_options_2_items']->columnName   = 'question_options_2_items';
$def->properties['question_options_2_items']->propertyName = 'question_options_2_items';
$def->properties['question_options_2_items']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['question_options_2_pos'] = new ezcPersistentObjectProperty();
$def->properties['question_options_2_pos']->columnName   = 'question_options_2_pos';
$def->properties['question_options_2_pos']->propertyName = 'question_options_2_pos';
$def->properties['question_options_2_pos']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['question_options_2_enabled'] = new ezcPersistentObjectProperty();
$def->properties['question_options_2_enabled']->columnName   = 'question_options_2_enabled';
$def->properties['question_options_2_enabled']->propertyName = 'question_options_2_enabled';
$def->properties['question_options_2_enabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Question 3
$def->properties['question_options_3'] = new ezcPersistentObjectProperty();
$def->properties['question_options_3']->columnName   = 'question_options_3';
$def->properties['question_options_3']->propertyName = 'question_options_3';
$def->properties['question_options_3']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['question_options_3_items'] = new ezcPersistentObjectProperty();
$def->properties['question_options_3_items']->columnName   = 'question_options_3_items';
$def->properties['question_options_3_items']->propertyName = 'question_options_3_items';
$def->properties['question_options_3_items']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['question_options_3_pos'] = new ezcPersistentObjectProperty();
$def->properties['question_options_3_pos']->columnName   = 'question_options_3_pos';
$def->properties['question_options_3_pos']->propertyName = 'question_options_3_pos';
$def->properties['question_options_3_pos']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['question_options_3_enabled'] = new ezcPersistentObjectProperty();
$def->properties['question_options_3_enabled']->columnName   = 'question_options_3_enabled';
$def->properties['question_options_3_enabled']->propertyName = 'question_options_3_enabled';
$def->properties['question_options_3_enabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Question 4
$def->properties['question_options_4'] = new ezcPersistentObjectProperty();
$def->properties['question_options_4']->columnName   = 'question_options_4';
$def->properties['question_options_4']->propertyName = 'question_options_4';
$def->properties['question_options_4']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['question_options_4_items'] = new ezcPersistentObjectProperty();
$def->properties['question_options_4_items']->columnName   = 'question_options_4_items';
$def->properties['question_options_4_items']->propertyName = 'question_options_4_items';
$def->properties['question_options_4_items']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['question_options_4_pos'] = new ezcPersistentObjectProperty();
$def->properties['question_options_4_pos']->columnName   = 'question_options_4_pos';
$def->properties['question_options_4_pos']->propertyName = 'question_options_4_pos';
$def->properties['question_options_4_pos']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['question_options_4_enabled'] = new ezcPersistentObjectProperty();
$def->properties['question_options_4_enabled']->columnName   = 'question_options_4_enabled';
$def->properties['question_options_4_enabled']->propertyName = 'question_options_4_enabled';
$def->properties['question_options_4_enabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Question 5
$def->properties['question_options_5'] = new ezcPersistentObjectProperty();
$def->properties['question_options_5']->columnName   = 'question_options_5';
$def->properties['question_options_5']->propertyName = 'question_options_5';
$def->properties['question_options_5']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['question_options_5_items'] = new ezcPersistentObjectProperty();
$def->properties['question_options_5_items']->columnName   = 'question_options_5_items';
$def->properties['question_options_5_items']->propertyName = 'question_options_5_items';
$def->properties['question_options_5_items']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['question_options_5_pos'] = new ezcPersistentObjectProperty();
$def->properties['question_options_5_pos']->columnName   = 'question_options_5_pos';
$def->properties['question_options_5_pos']->propertyName = 'question_options_5_pos';
$def->properties['question_options_5_pos']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['question_options_5_enabled'] = new ezcPersistentObjectProperty();
$def->properties['question_options_5_enabled']->columnName   = 'question_options_5_enabled';
$def->properties['question_options_5_enabled']->propertyName = 'question_options_5_enabled';
$def->properties['question_options_5_enabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['question_plain_1'] = new ezcPersistentObjectProperty();
$def->properties['question_plain_1']->columnName   = 'question_plain_1';
$def->properties['question_plain_1']->propertyName = 'question_plain_1';
$def->properties['question_plain_1']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['question_plain_1_pos'] = new ezcPersistentObjectProperty();
$def->properties['question_plain_1_pos']->columnName   = 'question_plain_1_pos';
$def->properties['question_plain_1_pos']->propertyName = 'question_plain_1_pos';
$def->properties['question_plain_1_pos']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['question_plain_1_enabled'] = new ezcPersistentObjectProperty();
$def->properties['question_plain_1_enabled']->columnName   = 'question_plain_1_enabled';
$def->properties['question_plain_1_enabled']->propertyName = 'question_plain_1_enabled';
$def->properties['question_plain_1_enabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['question_plain_2'] = new ezcPersistentObjectProperty();
$def->properties['question_plain_2']->columnName   = 'question_plain_2';
$def->properties['question_plain_2']->propertyName = 'question_plain_2';
$def->properties['question_plain_2']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['question_plain_2_pos'] = new ezcPersistentObjectProperty();
$def->properties['question_plain_2_pos']->columnName   = 'question_plain_2_pos';
$def->properties['question_plain_2_pos']->propertyName = 'question_plain_2_pos';
$def->properties['question_plain_2_pos']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['question_plain_2_enabled'] = new ezcPersistentObjectProperty();
$def->properties['question_plain_2_enabled']->columnName   = 'question_plain_2_enabled';
$def->properties['question_plain_2_enabled']->propertyName = 'question_plain_2_enabled';
$def->properties['question_plain_2_enabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['question_plain_3'] = new ezcPersistentObjectProperty();
$def->properties['question_plain_3']->columnName   = 'question_plain_3';
$def->properties['question_plain_3']->propertyName = 'question_plain_3';
$def->properties['question_plain_3']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['question_plain_3_pos'] = new ezcPersistentObjectProperty();
$def->properties['question_plain_3_pos']->columnName   = 'question_plain_3_pos';
$def->properties['question_plain_3_pos']->propertyName = 'question_plain_3_pos';
$def->properties['question_plain_3_pos']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['question_plain_3_enabled'] = new ezcPersistentObjectProperty();
$def->properties['question_plain_3_enabled']->columnName   = 'question_plain_3_enabled';
$def->properties['question_plain_3_enabled']->propertyName = 'question_plain_3_enabled';
$def->properties['question_plain_3_enabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['question_plain_4'] = new ezcPersistentObjectProperty();
$def->properties['question_plain_4']->columnName   = 'question_plain_4';
$def->properties['question_plain_4']->propertyName = 'question_plain_4';
$def->properties['question_plain_4']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['question_plain_4_pos'] = new ezcPersistentObjectProperty();
$def->properties['question_plain_4_pos']->columnName   = 'question_plain_4_pos';
$def->properties['question_plain_4_pos']->propertyName = 'question_plain_4_pos';
$def->properties['question_plain_4_pos']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['question_plain_4_enabled'] = new ezcPersistentObjectProperty();
$def->properties['question_plain_4_enabled']->columnName   = 'question_plain_4_enabled';
$def->properties['question_plain_4_enabled']->propertyName = 'question_plain_4_enabled';
$def->properties['question_plain_4_enabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['question_plain_5'] = new ezcPersistentObjectProperty();
$def->properties['question_plain_5']->columnName   = 'question_plain_5';
$def->properties['question_plain_5']->propertyName = 'question_plain_5';
$def->properties['question_plain_5']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['question_plain_5_pos'] = new ezcPersistentObjectProperty();
$def->properties['question_plain_5_pos']->columnName   = 'question_plain_5_pos';
$def->properties['question_plain_5_pos']->propertyName = 'question_plain_5_pos';
$def->properties['question_plain_5_pos']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['question_plain_5_enabled'] = new ezcPersistentObjectProperty();
$def->properties['question_plain_5_enabled']->columnName   = 'question_plain_5_enabled';
$def->properties['question_plain_5_enabled']->propertyName = 'question_plain_5_enabled';
$def->properties['question_plain_5_enabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>