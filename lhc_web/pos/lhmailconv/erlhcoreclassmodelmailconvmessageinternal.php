<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_mailconv_msg_internal";
$def->class = "erLhcoreClassModelMailconvMessageInternal";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );


foreach (array(
             'msg','meta_msg','name_support'
         ) as $attr) {
    $def->properties[$attr] = new ezcPersistentObjectProperty();
    $def->properties[$attr]->columnName   = $attr;
    $def->properties[$attr]->propertyName = $attr;
    $def->properties[$attr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
}

foreach (array(
             'time','user_id','chat_id'
         ) as $attr) {
    $def->properties[$attr] = new ezcPersistentObjectProperty();
    $def->properties[$attr]->columnName   = $attr;
    $def->properties[$attr]->propertyName = $attr;
    $def->properties[$attr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

return $def;

?>