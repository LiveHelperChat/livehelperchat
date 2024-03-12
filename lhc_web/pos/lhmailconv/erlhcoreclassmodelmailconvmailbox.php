<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_mailconv_mailbox";
$def->class = "erLhcoreClassModelMailconvMailbox";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

foreach (array(
            'mail','name','username','password','host','imap','last_sync_log','mailbox_sync','signature','uuid_status',
            'mail_smtp','name_smtp','username_smtp','password_smtp','workflow_options'
         ) as $attr) {
    $def->properties[$attr] = new ezcPersistentObjectProperty();
    $def->properties[$attr]->columnName   = $attr;
    $def->properties[$attr]->propertyName = $attr;
    $def->properties[$attr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
}

foreach (array(
             'port','active','create_a_copy','delete_mode','sync_status','sync_started','sync_interval','no_pswd_smtp',
             'last_sync_time','import_since','signature_under','reopen_timeout','failed','import_priority','assign_parent_user',
             'user_id', 'dep_id', 'auth_method','reopen_reset','last_process_time','delete_on_archive','delete_policy'
         ) as $attr) {
    $def->properties[$attr] = new ezcPersistentObjectProperty();
    $def->properties[$attr]->columnName   = $attr;
    $def->properties[$attr]->propertyName = $attr;
    $def->properties[$attr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

return $def;

?>