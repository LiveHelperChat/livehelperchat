<?php

$def = include 'pos/lhmailconv/erlhcoreclassmodelmailconvfile.php';
$def->table =  \LiveHelperChat\Models\mailConv\Archive\Range::$archiveConversationFileTable;
$def->class = '\LiveHelperChat\Models\mailConv\Archive\File';

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentManualGenerator' );

return $def;

?>