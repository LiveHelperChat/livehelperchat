<?php

$def = include 'pos/lhchat/erlhcoreclassmodelchataction.php';
$def->table =  erLhcoreClassModelChatArchiveRange::$archiveChatActionsTable;
$def->class = 'erLhcoreClassModelChatArchiveAction';

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentManualGenerator' );

return $def;

?>