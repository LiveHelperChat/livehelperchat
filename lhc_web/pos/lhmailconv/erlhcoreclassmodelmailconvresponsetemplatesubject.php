<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_mailconv_response_template_subject";
$def->class = "erLhcoreClassModelMailconvResponseTemplateSubject";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['template_id'] = new ezcPersistentObjectProperty();
$def->properties['template_id']->columnName   = 'template_id';
$def->properties['template_id']->propertyName = 'template_id';
$def->properties['template_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['subject_id'] = new ezcPersistentObjectProperty();
$def->properties['subject_id']->columnName   = 'subject_id';
$def->properties['subject_id']->propertyName = 'subject_id';
$def->properties['subject_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>