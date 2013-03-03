<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_canned_msg";
$def->class = "erLhcoreClassModelCannedMsg";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['msg'] = new ezcPersistentObjectProperty();
$def->properties['msg']->columnName   = 'msg';
$def->properties['msg']->propertyName = 'msg';
$def->properties['msg']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING; 

return $def; 

?>