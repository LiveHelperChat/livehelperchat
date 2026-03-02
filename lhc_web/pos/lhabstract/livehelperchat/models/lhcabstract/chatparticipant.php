<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_chat_participant";
$def->class = "\LiveHelperChat\Models\LHCAbstract\ChatParticipant";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

foreach (['chat_id', 'user_id', 'duration', 'time', 'dep_id',
    'frt',  // First response time. How long agent took to write first message after chat acceptance.
    'aart', // Average agent response time. Average how long agents took to write response to visitor messages.
    'mart', // Max agent response time. How long agent took to write response to visitor messages.
] as $posAttr) {
    $def->properties[$posAttr] = new ezcPersistentObjectProperty();
    $def->properties[$posAttr]->columnName   = $posAttr;
    $def->properties[$posAttr]->propertyName = $posAttr;
    $def->properties[$posAttr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

return $def;

?>