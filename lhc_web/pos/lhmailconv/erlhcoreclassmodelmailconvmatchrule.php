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

$def->properties['mailbox_id'] = new ezcPersistentObjectProperty();
$def->properties['mailbox_id']->columnName   = 'mailbox_id';
$def->properties['mailbox_id']->propertyName = 'mailbox_id';
$def->properties['mailbox_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['from_name'] = new ezcPersistentObjectProperty();
$def->properties['from_name']->columnName   = 'from_name';
$def->properties['from_name']->propertyName = 'from_name';
$def->properties['from_name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['from_mail'] = new ezcPersistentObjectProperty();
$def->properties['from_mail']->columnName   = 'from_mail';
$def->properties['from_mail']->propertyName = 'from_mail';
$def->properties['from_mail']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['subject_contains'] = new ezcPersistentObjectProperty();
$def->properties['subject_contains']->columnName   = 'subject_contains';
$def->properties['subject_contains']->propertyName = 'subject_contains';
$def->properties['subject_contains']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['priority'] = new ezcPersistentObjectProperty();
$def->properties['priority']->columnName   = 'priority';
$def->properties['priority']->propertyName = 'priority';
$def->properties['priority']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['priority_rule'] = new ezcPersistentObjectProperty();
$def->properties['priority_rule']->columnName   = 'priority_rule';
$def->properties['priority_rule']->propertyName = 'priority_rule';
$def->properties['priority_rule']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>