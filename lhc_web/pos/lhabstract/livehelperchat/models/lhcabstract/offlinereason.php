<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_offline_reason";
$def->class = "\LiveHelperChat\Models\LHCAbstract\OfflineReason";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition('ezcPersistentNativeGenerator');

foreach (['name', 'description', 'icon', 'pos'] as $posAttr) {
    $def->properties[$posAttr] = new ezcPersistentObjectProperty();
    $def->properties[$posAttr]->columnName   = $posAttr;
    $def->properties[$posAttr]->propertyName = $posAttr;
    $def->properties[$posAttr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
}

$def->properties['pos']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;
