<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_userdep";
$def->class = "erLhcoreClassModelUserDep";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

foreach (['user_id','dep_id','last_activity','lastd_activity','hide_online_ts','hide_online','last_accepted',
             'active_chats','pending_chats','inactive_chats','always_on','ro','max_chats','type','dep_group_id',
             'exclude_autoasign','exc_indv_autoasign','assign_priority','chat_min_priority','chat_max_priority'] as $posAttr) {
    $def->properties[$posAttr] = new ezcPersistentObjectProperty();
    $def->properties[$posAttr]->columnName   = $posAttr;
    $def->properties[$posAttr]->propertyName = $posAttr;
    $def->properties[$posAttr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

return $def; 

?>