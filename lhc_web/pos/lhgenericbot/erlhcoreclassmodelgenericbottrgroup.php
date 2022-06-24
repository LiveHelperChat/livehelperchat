<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_generic_bot_tr_group";
$def->class = "erLhcoreClassModelGenericBotTrGroup";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

foreach (['name','nick','filepath','filename','configuration','bot_lang'] as $posAttr) {
    $def->properties[$posAttr] = new ezcPersistentObjectProperty();
    $def->properties[$posAttr]->columnName   = $posAttr;
    $def->properties[$posAttr]->propertyName = $posAttr;
    $def->properties[$posAttr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
}

$def->properties['use_translation_service'] = new ezcPersistentObjectProperty();
$def->properties['use_translation_service']->columnName   = 'use_translation_service';
$def->properties['use_translation_service']->propertyName = 'use_translation_service';
$def->properties['use_translation_service']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>