<?php

$def = include 'pos/lhabstract/livehelperchat/models/lhcabstract/chatparticipant.php';
$def->table =  erLhcoreClassModelChatArchiveRange::$archiveChatParticipantTable;
$def->class = 'erLhcoreClassModelChatArchiveParticipant';

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentManualGenerator' );

return $def;

?>