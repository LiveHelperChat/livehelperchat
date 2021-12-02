<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_mailconv_mailing_list_recipient";
$def->class = "erLhcoreClassModelMailconvMailingListRecipient";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['mailing_list_id'] = new ezcPersistentObjectProperty();
$def->properties['mailing_list_id']->columnName   = 'mailing_list_id';
$def->properties['mailing_list_id']->propertyName = 'mailing_list_id';
$def->properties['mailing_list_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['mailing_recipient_id'] = new ezcPersistentObjectProperty();
$def->properties['mailing_recipient_id']->columnName   = 'mailing_recipient_id';
$def->properties['mailing_recipient_id']->propertyName = 'mailing_recipient_id';
$def->properties['mailing_recipient_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>