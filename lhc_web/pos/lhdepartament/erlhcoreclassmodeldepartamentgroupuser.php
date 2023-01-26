<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_departament_group_user";
$def->class = "erLhcoreClassModelDepartamentGroupUser";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

foreach ([
             'dep_group_id','user_id',
             'read_only','exc_indv_autoasign',
             'assign_priority','chat_min_priority','chat_max_priority'
         ] as $posAttr) {
    $def->properties[$posAttr] = new ezcPersistentObjectProperty();
    $def->properties[$posAttr]->columnName   = $posAttr;
    $def->properties[$posAttr]->propertyName = $posAttr;
    $def->properties[$posAttr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

return $def;

?>