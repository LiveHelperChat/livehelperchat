<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_generic_bot_bot";
$def->class = "erLhcoreClassModelGenericBotBot";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

foreach (['name','short_name','nick','avatar','configuration','attr_str_1','attr_str_2','attr_str_3','filepath','filename'] as $posAttr) {
    $def->properties[$posAttr] = new ezcPersistentObjectProperty();
    $def->properties[$posAttr]->columnName   = $posAttr;
    $def->properties[$posAttr]->propertyName = $posAttr;
    $def->properties[$posAttr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
}

return $def;

?>