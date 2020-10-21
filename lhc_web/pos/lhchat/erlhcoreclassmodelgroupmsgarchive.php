<?php

$def = include 'pos/lhchat/erlhcoreclassmodelgroupmsg.php';
$def->table =  erLhcoreClassModelChatArchiveRange::$archiveSupportMsgTable;
$def->class = 'erLhcoreClassModelGroupMsgArchive';

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentManualGenerator' );

return $def;

?>