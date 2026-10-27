<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_chat_alert_icon";
$def->class = "erLhAbstractModelChatAlertIcon";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

foreach (['name','identifier'] as $posAttr) {
    $def->properties[$posAttr] = new ezcPersistentObjectProperty();
    $def->properties[$posAttr]->columnName   = $posAttr;
    $def->properties[$posAttr]->propertyName = $posAttr;
    $def->properties[$posAttr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
}

foreach (['created_at','updated_at','muser_id'] as $auditAttr) {
    $def->properties[$auditAttr] = new ezcPersistentObjectProperty();
    $def->properties[$auditAttr]->columnName   = $auditAttr;
    $def->properties[$auditAttr]->propertyName = $auditAttr;
    $def->properties[$auditAttr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

return $def;

?>