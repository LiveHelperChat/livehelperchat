<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_chat_online_user_footprint";
$def->class = "erLhcoreClassModelChatOnlineUserFootprint";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['page'] = new ezcPersistentObjectProperty();
$def->properties['page']->columnName   = 'page';
$def->properties['page']->propertyName = 'page';
$def->properties['page']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

foreach (['chat_id','online_user_id','vtime'] as $posAttr) {
    $def->properties[$posAttr] = new ezcPersistentObjectProperty();
    $def->properties[$posAttr]->columnName   = $posAttr;
    $def->properties[$posAttr]->propertyName = $posAttr;
    $def->properties[$posAttr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

return $def;

?>