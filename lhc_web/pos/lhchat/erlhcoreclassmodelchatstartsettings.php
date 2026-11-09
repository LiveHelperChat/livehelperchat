<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_chat_start_settings";
$def->class = "erLhcoreClassModelChatStartSettings";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['data'] = new ezcPersistentObjectProperty();
$def->properties['data']->columnName   = 'data';
$def->properties['data']->propertyName = 'data';
$def->properties['data']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['dep_ids'] = new ezcPersistentObjectProperty();
$def->properties['dep_ids']->columnName   = 'dep_ids';
$def->properties['dep_ids']->propertyName = 'dep_ids';
$def->properties['dep_ids']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

foreach (['created_at','updated_at','muser_id','department_id'] as $posAttr) {
	$def->properties[$posAttr] = new ezcPersistentObjectProperty();
	$def->properties[$posAttr]->columnName = $posAttr;
	$def->properties[$posAttr]->propertyName = $posAttr;
	$def->properties[$posAttr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

return $def;

?>