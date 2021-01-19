<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_chat_voice_video";
$def->class = "erLhcoreClassModelChatVoiceVideo";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['token'] = new ezcPersistentObjectProperty();
$def->properties['token']->columnName   = 'token';
$def->properties['token']->propertyName = 'token';
$def->properties['token']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

foreach (array('chat_id','user_id','op_status','vi_status','status','voice','video','screen_share','ctime') as $attr) {
    $def->properties[$attr] = new ezcPersistentObjectProperty();
    $def->properties[$attr]->columnName   = $attr;
    $def->properties[$attr]->propertyName = $attr;
    $def->properties[$attr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

return $def;

?>