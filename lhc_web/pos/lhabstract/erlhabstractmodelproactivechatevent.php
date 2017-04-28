<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_proactive_chat_event";
$def->class = "erLhAbstractModelProactiveChatEvent";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['vid_id'] = new ezcPersistentObjectProperty();
$def->properties['vid_id']->columnName   = 'vid_id';
$def->properties['vid_id']->propertyName = 'vid_id';
$def->properties['vid_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['ev_id'] = new ezcPersistentObjectProperty();
$def->properties['ev_id']->columnName   = 'ev_id';
$def->properties['ev_id']->propertyName = 'ev_id';
$def->properties['ev_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['ts'] = new ezcPersistentObjectProperty();
$def->properties['ts']->columnName   = 'ts';
$def->properties['ts']->propertyName = 'ts';
$def->properties['ts']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['val'] = new ezcPersistentObjectProperty();
$def->properties['val']->columnName   = 'val';
$def->properties['val']->propertyName = 'val';
$def->properties['val']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>