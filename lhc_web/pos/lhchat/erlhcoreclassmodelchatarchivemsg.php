<?php

$def = include 'pos/lhchat/erlhcoreclassmodelmsg.php';
$def->table =  erLhcoreClassModelChatArchiveRange::$archiveMsgTable;
$def->class = 'erLhcoreClassModelChatArchiveMsg';

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentManualGenerator' );

return $def;

?>