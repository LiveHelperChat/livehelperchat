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

$def->properties['stars'] = new ezcPersistentObjectProperty();
$def->properties['stars']->columnName   = 'stars';
$def->properties['stars']->propertyName = 'stars';
$def->properties['stars']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

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

return $def;

?>