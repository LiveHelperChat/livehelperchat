<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_product_departament";
$def->class = "erLhAbstractModelProductDepartament";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['departament_id'] = new ezcPersistentObjectProperty();
$def->properties['departament_id']->columnName   = 'departament_id';
$def->properties['departament_id']->propertyName = 'departament_id';
$def->properties['departament_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['product_id'] = new ezcPersistentObjectProperty();
$def->properties['product_id']->columnName   = 'product_id';
$def->properties['product_id']->propertyName = 'product_id';
$def->properties['product_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>