<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_form_collected";
$def->class = "erLhAbstractModelFormCollected";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition('ezcPersistentNativeGenerator');

$propertiesMap = array(
    'form_id'       => ezcPersistentObjectProperty::PHP_TYPE_STRING,
    'ctime'         => ezcPersistentObjectProperty::PHP_TYPE_INT,
    'ip'            => ezcPersistentObjectProperty::PHP_TYPE_STRING,
    'content'       => ezcPersistentObjectProperty::PHP_TYPE_STRING,
    'identifier'    => ezcPersistentObjectProperty::PHP_TYPE_STRING,
    'custom_fields' => ezcPersistentObjectProperty::PHP_TYPE_STRING,
    'chat_id'       => ezcPersistentObjectProperty::PHP_TYPE_INT,
    'user_id'       => ezcPersistentObjectProperty::PHP_TYPE_INT,
    'attr_int_1'    => ezcPersistentObjectProperty::PHP_TYPE_INT,
    'attr_int_2'    => ezcPersistentObjectProperty::PHP_TYPE_INT,
    'attr_int_3'    => ezcPersistentObjectProperty::PHP_TYPE_INT,
);

foreach ($propertiesMap as $propertyName => $propertyType) {
    $def->properties[$propertyName] = new ezcPersistentObjectProperty();
    $def->properties[$propertyName]->columnName   = $propertyName;
    $def->properties[$propertyName]->propertyName = $propertyName;
    $def->properties[$propertyName]->propertyType = $propertyType;
}

return $def;

?>