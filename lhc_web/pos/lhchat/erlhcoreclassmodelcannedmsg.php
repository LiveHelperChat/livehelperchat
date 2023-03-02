<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_canned_msg";
$def->class = "erLhcoreClassModelCannedMsg";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

foreach (['title','unique_id','explain','msg','html_snippet','fallback_msg','languages','additional_data','days_activity'] as $posAttr) {
    $def->properties[$posAttr] = new ezcPersistentObjectProperty();
    $def->properties[$posAttr]->columnName   = $posAttr;
    $def->properties[$posAttr]->propertyName = $posAttr;
    $def->properties[$posAttr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
}

// repetitiveness - Day -- Hour, Minute, Seconds

// Custom range
// repetitiveness - Yearly -- [Enters full date range] Year, Month, Day, Hour, Minute, Seconds

foreach ([
    'position','delay','department_id','user_id','auto_send','attr_int_1','attr_int_2','attr_int_3',
    'updated_at', 'created_at','disabled',
    'active_from','active_to','repetitiveness','delete_on_exp' // Stores period and acitivy type
         ] as $posAttr) {
    $def->properties[$posAttr] = new ezcPersistentObjectProperty();
    $def->properties[$posAttr]->columnName   = $posAttr;
    $def->properties[$posAttr]->propertyName = $posAttr;
    $def->properties[$posAttr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

return $def;

?>