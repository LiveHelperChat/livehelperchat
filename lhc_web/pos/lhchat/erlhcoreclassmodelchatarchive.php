<?php

$def = include 'pos/lhchat/erlhcoreclassmodelchat.php';
$def->table =  erLhcoreClassModelChatArchiveRange::$archiveTable;
$def->class = 'erLhcoreClassModelChatArchive';

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentManualGenerator' );

return $def;

?>