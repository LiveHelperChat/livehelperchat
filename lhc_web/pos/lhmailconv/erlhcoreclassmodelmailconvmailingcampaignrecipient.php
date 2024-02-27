<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_mailconv_mailing_campaign_recipient";
$def->class = "erLhcoreClassModelMailconvMailingCampaignRecipient";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

foreach (array(
             'mailbox','email','name',
             'attr_str_1','attr_str_2','attr_str_3',
             'attr_str_4','attr_str_5','attr_str_6',
             'log'
         ) as $attr) {
    $def->properties[$attr] = new ezcPersistentObjectProperty();
    $def->properties[$attr]->columnName   = $attr;
    $def->properties[$attr]->propertyName = $attr;
    $def->properties[$attr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
}

foreach (array(
             'campaign_id','recipient_id','status','send_at','type',
             'message_id','conversation_id','opened_at'
         ) as $attr) {
    $def->properties[$attr] = new ezcPersistentObjectProperty();
    $def->properties[$attr]->columnName   = $attr;
    $def->properties[$attr]->propertyName = $attr;
    $def->properties[$attr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

return $def;

?>