<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_mailconv_mailing_campaign";
$def->class = "erLhcoreClassModelMailconvMailingCampaign";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['status'] = new ezcPersistentObjectProperty();
$def->properties['status']->columnName   = 'status';
$def->properties['status']->propertyName = 'status';
$def->properties['status']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['starts_at'] = new ezcPersistentObjectProperty();
$def->properties['starts_at']->columnName   = 'starts_at';
$def->properties['starts_at']->propertyName = 'starts_at';
$def->properties['starts_at']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['user_id'] = new ezcPersistentObjectProperty();
$def->properties['user_id']->columnName   = 'user_id';
$def->properties['user_id']->propertyName = 'user_id';
$def->properties['user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['enabled'] = new ezcPersistentObjectProperty();
$def->properties['enabled']->columnName   = 'enabled';
$def->properties['enabled']->propertyName = 'enabled';
$def->properties['enabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>