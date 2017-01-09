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

$def->properties['feedback_text'] = new ezcPersistentObjectProperty();
$def->properties['feedback_text']->columnName   = 'feedback_text';
$def->properties['feedback_text']->propertyName = 'feedback_text';
$def->properties['feedback_text']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

for ($i = 1; $i <= 5; $i++) {
    // Stars
    $def->properties['max_stars_'.$i] = new ezcPersistentObjectProperty();
    $def->properties['max_stars_'.$i]->columnName   = 'max_stars_'.$i;
    $def->properties['max_stars_'.$i]->propertyName = 'max_stars_'.$i;
    $def->properties['max_stars_'.$i]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
    
    $def->properties['max_stars_'.$i.'_title'] = new ezcPersistentObjectProperty();
    $def->properties['max_stars_'.$i.'_title']->columnName   = 'max_stars_'.$i.'_title';
    $def->properties['max_stars_'.$i.'_title']->propertyName = 'max_stars_'.$i.'_title';
    $def->properties['max_stars_'.$i.'_title']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
    
    $def->properties['max_stars_'.$i.'_pos'] = new ezcPersistentObjectProperty();
    $def->properties['max_stars_'.$i.'_pos']->columnName   = 'max_stars_'.$i.'_pos';
    $def->properties['max_stars_'.$i.'_pos']->propertyName = 'max_stars_'.$i.'_pos';
    $def->properties['max_stars_'.$i.'_pos']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
    
    $def->properties['max_stars_'.$i.'_enabled'] = new ezcPersistentObjectProperty();
    $def->properties['max_stars_'.$i.'_enabled']->columnName   = 'max_stars_'.$i.'_enabled';
    $def->properties['max_stars_'.$i.'_enabled']->propertyName = 'max_stars_'.$i.'_enabled';
    $def->properties['max_stars_'.$i.'_enabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
    
    $def->properties['max_stars_'.$i.'_req'] = new ezcPersistentObjectProperty();
    $def->properties['max_stars_'.$i.'_req']->columnName   = 'max_stars_'.$i.'_req';
    $def->properties['max_stars_'.$i.'_req']->propertyName = 'max_stars_'.$i.'_req';
    $def->properties['max_stars_'.$i.'_req']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
    
    // Questions options
    $def->properties['question_options_'.$i] = new ezcPersistentObjectProperty();
    $def->properties['question_options_'.$i]->columnName   = 'question_options_'.$i;
    $def->properties['question_options_'.$i]->propertyName = 'question_options_'.$i;
    $def->properties['question_options_'.$i]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
    
    $def->properties['question_options_'.$i.'_items'] = new ezcPersistentObjectProperty();
    $def->properties['question_options_'.$i.'_items']->columnName   = 'question_options_'.$i.'_items';
    $def->properties['question_options_'.$i.'_items']->propertyName = 'question_options_'.$i.'_items';
    $def->properties['question_options_'.$i.'_items']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
    
    $def->properties['question_options_'.$i.'_pos'] = new ezcPersistentObjectProperty();
    $def->properties['question_options_'.$i.'_pos']->columnName   = 'question_options_'.$i.'_pos';
    $def->properties['question_options_'.$i.'_pos']->propertyName = 'question_options_'.$i.'_pos';
    $def->properties['question_options_'.$i.'_pos']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
    
    $def->properties['question_options_'.$i.'_enabled'] = new ezcPersistentObjectProperty();
    $def->properties['question_options_'.$i.'_enabled']->columnName   = 'question_options_'.$i.'_enabled';
    $def->properties['question_options_'.$i.'_enabled']->propertyName = 'question_options_'.$i.'_enabled';
    $def->properties['question_options_'.$i.'_enabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
    
    $def->properties['question_options_'.$i.'_req'] = new ezcPersistentObjectProperty();
    $def->properties['question_options_'.$i.'_req']->columnName   = 'question_options_'.$i.'_req';
    $def->properties['question_options_'.$i.'_req']->propertyName = 'question_options_'.$i.'_req';
    $def->properties['question_options_'.$i.'_req']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
    
    // Questions plains
    $def->properties['question_plain_'.$i] = new ezcPersistentObjectProperty();
    $def->properties['question_plain_'.$i]->columnName   = 'question_plain_'.$i;
    $def->properties['question_plain_'.$i]->propertyName = 'question_plain_'.$i;
    $def->properties['question_plain_'.$i]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

    $def->properties['question_plain_'.$i.'_pos'] = new ezcPersistentObjectProperty();
    $def->properties['question_plain_'.$i.'_pos']->columnName   = 'question_plain_'.$i.'_pos';
    $def->properties['question_plain_'.$i.'_pos']->propertyName = 'question_plain_'.$i.'_pos';
    $def->properties['question_plain_'.$i.'_pos']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

    $def->properties['question_plain_'.$i.'_req'] = new ezcPersistentObjectProperty();
    $def->properties['question_plain_'.$i.'_req']->columnName   = 'question_plain_'.$i.'_req';
    $def->properties['question_plain_'.$i.'_req']->propertyName = 'question_plain_'.$i.'_req';
    $def->properties['question_plain_'.$i.'_req']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

    $def->properties['question_plain_'.$i.'_enabled'] = new ezcPersistentObjectProperty();
    $def->properties['question_plain_'.$i.'_enabled']->columnName   = 'question_plain_'.$i.'_enabled';
    $def->properties['question_plain_'.$i.'_enabled']->propertyName = 'question_plain_'.$i.'_enabled';
    $def->properties['question_plain_'.$i.'_enabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

return $def;

?>