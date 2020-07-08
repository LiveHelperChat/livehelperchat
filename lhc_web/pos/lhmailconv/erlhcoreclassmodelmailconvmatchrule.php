<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_mailconv_match_rule";
$def->class = "erLhcoreClassModelMailconvMatchRule";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['dep_id'] = new ezcPersistentObjectProperty();
$def->properties['dep_id']->columnName   = 'dep_id';
$def->properties['dep_id']->propertyName = 'dep_id';
$def->properties['dep_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['active'] = new ezcPersistentObjectProperty();
$def->properties['active']->columnName   = 'active';
$def->properties['active']->propertyName = 'active';
$def->properties['active']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['conditions'] = new ezcPersistentObjectProperty();
$def->properties['conditions']->columnName   = 'conditions';
$def->properties['conditions']->propertyName = 'conditions';
$def->properties['conditions']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>