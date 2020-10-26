<?php

$def = include 'pos/lhchat/erlhcoreclassmodelgroupchat.php';
$def->table =  erLhcoreClassModelChatArchiveRange::$archiveSupportTable;
$def->class = 'erLhcoreClassModelGroupChatArchive';

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentManualGenerator' );

return $def;

?>