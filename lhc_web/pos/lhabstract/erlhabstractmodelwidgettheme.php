<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_widget_theme";
$def->class = "erLhAbstractModelWidgetTheme";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['onl_bcolor'] = new ezcPersistentObjectProperty();
$def->properties['onl_bcolor']->columnName   = 'onl_bcolor';
$def->properties['onl_bcolor']->propertyName = 'onl_bcolor';
$def->properties['onl_bcolor']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['offl_bcolor'] = new ezcPersistentObjectProperty();
$def->properties['offl_bcolor']->columnName   = 'offl_bcolor';
$def->properties['offl_bcolor']->propertyName = 'offl_bcolor';
$def->properties['offl_bcolor']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['text_color'] = new ezcPersistentObjectProperty();
$def->properties['text_color']->columnName   = 'text_color';
$def->properties['text_color']->propertyName = 'text_color';
$def->properties['text_color']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['online_image'] = new ezcPersistentObjectProperty();
$def->properties['online_image']->columnName   = 'online_image';
$def->properties['online_image']->propertyName = 'online_image';
$def->properties['online_image']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['offline_image'] = new ezcPersistentObjectProperty();
$def->properties['offline_image']->columnName   = 'offline_image';
$def->properties['offline_image']->propertyName = 'offline_image';
$def->properties['offline_image']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['need_help_bck'] = new ezcPersistentObjectProperty();
$def->properties['need_help_bck']->columnName   = 'need_help_bck';
$def->properties['need_help_bck']->propertyName = 'need_help_bck';
$def->properties['need_help_bck']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
 
$def->properties['need_help_image'] = new ezcPersistentObjectProperty();
$def->properties['need_help_image']->columnName   = 'need_help_image';
$def->properties['need_help_image']->propertyName = 'need_help_image';
$def->properties['need_help_image']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
 
$def->properties['header_background'] = new ezcPersistentObjectProperty();
$def->properties['header_background']->columnName   = 'header_background';
$def->properties['header_background']->propertyName = 'header_background';
$def->properties['header_background']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>