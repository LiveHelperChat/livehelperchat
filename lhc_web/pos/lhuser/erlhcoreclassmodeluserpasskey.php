<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_users_passkey";
$def->class = "erLhcoreClassModelUserPasskey";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

foreach (array(
     'user_id' => ezcPersistentObjectProperty::PHP_TYPE_INT,
     'credential_id' => ezcPersistentObjectProperty::PHP_TYPE_STRING,
     'credential' => ezcPersistentObjectProperty::PHP_TYPE_STRING,
     'name' => ezcPersistentObjectProperty::PHP_TYPE_STRING,
     'aaguid' => ezcPersistentObjectProperty::PHP_TYPE_STRING,
     'counter' => ezcPersistentObjectProperty::PHP_TYPE_INT,
     'created_at' => ezcPersistentObjectProperty::PHP_TYPE_INT,
     'last_used_at' => ezcPersistentObjectProperty::PHP_TYPE_INT,
) as $field => $fieldType) {
    $def->properties[$field] = new ezcPersistentObjectProperty();
    $def->properties[$field]->columnName   = $field;
    $def->properties[$field]->propertyName = $field;
    $def->properties[$field]->propertyType = $fieldType;
}

return $def;

?>
