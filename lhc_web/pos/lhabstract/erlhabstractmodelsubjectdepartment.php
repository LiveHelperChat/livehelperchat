<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_subject_dep";
$def->class = "erLhAbstractModelSubjectDepartment";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['subject_id'] = new ezcPersistentObjectProperty();
$def->properties['subject_id']->columnName   = 'subject_id';
$def->properties['subject_id']->propertyName = 'subject_id';
$def->properties['subject_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['dep_id'] = new ezcPersistentObjectProperty();
$def->properties['dep_id']->columnName   = 'dep_id';
$def->properties['dep_id']->propertyName = 'dep_id';
$def->properties['dep_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>