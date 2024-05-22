<?php


$def = include 'pos/lhabstract/erlhabstractmodelsubjectchat.php';
$def->table =  erLhcoreClassModelChatArchiveRange::$archiveChatSubjectTable;
$def->class = 'erLhAbstractModelChatArchiveSubject';

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentManualGenerator' );

return $def;

?>