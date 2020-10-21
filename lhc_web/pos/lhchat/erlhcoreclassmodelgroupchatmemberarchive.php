<?php

$def = include 'pos/lhchat/erlhcoreclassmodelgroupchatmember.php';
$def->table =  erLhcoreClassModelChatArchiveRange::$archiveSupportMemberTable;
$def->class = 'erLhcoreClassModelGroupChatMemberArchive';

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentManualGenerator' );

return $def;

?>