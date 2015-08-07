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

$def->properties['max_stars'] = new ezcPersistentObjectProperty();
$def->properties['max_stars']->columnName   = 'max_stars';
$def->properties['max_stars']->propertyName = 'max_stars';
$def->properties['max_stars']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>