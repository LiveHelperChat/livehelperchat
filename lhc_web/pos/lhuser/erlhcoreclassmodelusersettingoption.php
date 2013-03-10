<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_users_setting_option";
$def->class = "erLhcoreClassModelUserSettingOption";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'identifier';
$def->idProperty->propertyName = 'identifier';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentManualGenerator' );

$def->properties['class'] = new ezcPersistentObjectProperty();
$def->properties['class']->columnName   = 'class';
$def->properties['class']->propertyName = 'class';
$def->properties['class']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
 
$def->properties['attribute'] = new ezcPersistentObjectProperty();
$def->properties['attribute']->columnName   = 'attribute';
$def->properties['attribute']->propertyName = 'attribute';
$def->properties['attribute']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING; 



return $def; 

?>