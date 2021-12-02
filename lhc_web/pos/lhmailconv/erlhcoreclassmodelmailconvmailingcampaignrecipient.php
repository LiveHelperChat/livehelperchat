<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_mailconv_mailing_campaign_recipient";
$def->class = "erLhcoreClassModelMailconvMailingCampaignRecipient";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['campaign_id'] = new ezcPersistentObjectProperty();
$def->properties['campaign_id']->columnName   = 'campaign_id';
$def->properties['campaign_id']->propertyName = 'campaign_id';
$def->properties['campaign_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['recipient_id'] = new ezcPersistentObjectProperty();
$def->properties['recipient_id']->columnName   = 'recipient_id';
$def->properties['recipient_id']->propertyName = 'recipient_id';
$def->properties['recipient_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['status'] = new ezcPersistentObjectProperty();
$def->properties['status']->columnName   = 'status';
$def->properties['status']->propertyName = 'status';
$def->properties['status']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>