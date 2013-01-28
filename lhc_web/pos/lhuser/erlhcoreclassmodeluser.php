<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_users";
$def->class = "erLhcoreClassModelUser";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentSequenceGenerator' );

$def->properties['username'] = new ezcPersistentObjectProperty();
$def->properties['username']->columnName   = 'username';
$def->properties['username']->propertyName = 'username';
$def->properties['username']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING; 

$def->properties['password'] = new ezcPersistentObjectProperty();
$def->properties['password']->columnName   = 'password';
$def->properties['password']->propertyName = 'password';
$def->properties['password']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING; 

$def->properties['email'] = new ezcPersistentObjectProperty();
$def->properties['email']->columnName   = 'email';
$def->properties['email']->propertyName = 'email';
$def->properties['email']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
 
$def->properties['lastactivity'] = new ezcPersistentObjectProperty();
$def->properties['lastactivity']->columnName   = 'lastactivity';
$def->properties['lastactivity']->propertyName = 'lastactivity';
$def->properties['lastactivity']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT; 

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING; 

$def->properties['surname'] = new ezcPersistentObjectProperty();
$def->properties['surname']->columnName   = 'surname';
$def->properties['surname']->propertyName = 'surname';
$def->properties['surname']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING; 

$def->properties['disabled'] = new ezcPersistentObjectProperty();
$def->properties['disabled']->columnName   = 'disabled';
$def->properties['disabled']->propertyName = 'disabled';
$def->properties['disabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING; 

return $def; 

?>