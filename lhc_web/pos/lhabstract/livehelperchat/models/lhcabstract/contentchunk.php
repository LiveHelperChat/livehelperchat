<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_content_chunk";
$def->class = "\LiveHelperChat\Models\LHCAbstract\ContentChunk";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition('ezcPersistentNativeGenerator');

foreach (['name', 'identifier', 'content', 'in_active'] as $posAttr) {
    $def->properties[$posAttr] = new ezcPersistentObjectProperty();
    $def->properties[$posAttr]->columnName   = $posAttr;
    $def->properties[$posAttr]->propertyName = $posAttr;
    $def->properties[$posAttr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
}

$def->properties['in_active']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;
