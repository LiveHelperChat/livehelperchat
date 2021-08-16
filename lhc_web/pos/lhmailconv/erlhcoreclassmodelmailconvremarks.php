<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_mailconv_remarks";
$def->class = "erLhcoreClassModelMailconvRemarks";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['email'] = new ezcPersistentObjectProperty();
$def->properties['email']->columnName   = 'email';
$def->properties['email']->propertyName = 'email';
$def->properties['email']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['remarks'] = new ezcPersistentObjectProperty();
$def->properties['remarks']->columnName   = 'remarks';
$def->properties['remarks']->propertyName = 'remarks';
$def->properties['remarks']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>