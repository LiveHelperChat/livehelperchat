<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_group";
$def->class = "erLhcoreClassModelGroup";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING; 

foreach (['created_at','updated_at','muser_id','required','disabled'] as $posAttr) {
	$def->properties[$posAttr] = new ezcPersistentObjectProperty();
	$def->properties[$posAttr]->columnName = $posAttr;
	$def->properties[$posAttr]->propertyName = $posAttr;
	$def->properties[$posAttr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

return $def; 

?>