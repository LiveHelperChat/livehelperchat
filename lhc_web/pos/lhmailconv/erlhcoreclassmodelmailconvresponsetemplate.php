<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_mailconv_response_template";
$def->class = "erLhcoreClassModelMailconvResponseTemplate";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['dep_id'] = new ezcPersistentObjectProperty();
$def->properties['dep_id']->columnName   = 'dep_id';
$def->properties['dep_id']->propertyName = 'dep_id';
$def->properties['dep_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['template'] = new ezcPersistentObjectProperty();
$def->properties['template']->columnName   = 'template';
$def->properties['template']->propertyName = 'template';
$def->properties['template']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>