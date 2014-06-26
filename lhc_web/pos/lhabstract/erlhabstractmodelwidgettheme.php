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

$def->properties['text_color'] = new ezcPersistentObjectProperty();
$def->properties['text_color']->columnName   = 'text_color';
$def->properties['text_color']->propertyName = 'text_color';
$def->properties['text_color']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['header_background'] = new ezcPersistentObjectProperty();
$def->properties['header_background']->columnName   = 'header_background';
$def->properties['header_background']->propertyName = 'header_background';
$def->properties['header_background']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['widget_border_color'] = new ezcPersistentObjectProperty();
$def->properties['widget_border_color']->columnName   = 'widget_border_color';
$def->properties['widget_border_color']->propertyName = 'widget_border_color';
$def->properties['widget_border_color']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['online_image'] = new ezcPersistentObjectProperty();
$def->properties['online_image']->columnName   = 'online_image';
$def->properties['online_image']->propertyName = 'online_image';
$def->properties['online_image']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['offline_image'] = new ezcPersistentObjectProperty();
$def->properties['offline_image']->columnName   = 'offline_image';
$def->properties['offline_image']->propertyName = 'offline_image';
$def->properties['offline_image']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['online_image_path'] = new ezcPersistentObjectProperty();
$def->properties['online_image_path']->columnName   = 'online_image_path';
$def->properties['online_image_path']->propertyName = 'online_image_path';
$def->properties['online_image_path']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['need_help_close_hover_bg'] = new ezcPersistentObjectProperty();
$def->properties['need_help_close_hover_bg']->columnName   = 'need_help_close_hover_bg';
$def->properties['need_help_close_hover_bg']->propertyName = 'need_help_close_hover_bg';
$def->properties['need_help_close_hover_bg']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['offline_image_path'] = new ezcPersistentObjectProperty();
$def->properties['offline_image_path']->columnName   = 'offline_image_path';
$def->properties['offline_image_path']->propertyName = 'offline_image_path';
$def->properties['offline_image_path']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['logo_image'] = new ezcPersistentObjectProperty();
$def->properties['logo_image']->columnName   = 'logo_image';
$def->properties['logo_image']->propertyName = 'logo_image';
$def->properties['logo_image']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['logo_image_path'] = new ezcPersistentObjectProperty();
$def->properties['logo_image_path']->columnName   = 'logo_image_path';
$def->properties['logo_image_path']->propertyName = 'logo_image_path';
$def->properties['logo_image_path']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['need_help_bcolor'] = new ezcPersistentObjectProperty();
$def->properties['need_help_bcolor']->columnName   = 'need_help_bcolor';
$def->properties['need_help_bcolor']->propertyName = 'need_help_bcolor';
$def->properties['need_help_bcolor']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['need_help_tcolor'] = new ezcPersistentObjectProperty();
$def->properties['need_help_tcolor']->columnName   = 'need_help_tcolor';
$def->properties['need_help_tcolor']->propertyName = 'need_help_tcolor';
$def->properties['need_help_tcolor']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['need_help_border'] = new ezcPersistentObjectProperty();
$def->properties['need_help_border']->columnName   = 'need_help_border';
$def->properties['need_help_border']->propertyName = 'need_help_border';
$def->properties['need_help_border']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['need_help_close_bg'] = new ezcPersistentObjectProperty();
$def->properties['need_help_close_bg']->columnName   = 'need_help_close_bg';
$def->properties['need_help_close_bg']->propertyName = 'need_help_close_bg';
$def->properties['need_help_close_bg']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['need_help_hover_bg'] = new ezcPersistentObjectProperty();
$def->properties['need_help_hover_bg']->columnName   = 'need_help_hover_bg';
$def->properties['need_help_hover_bg']->propertyName = 'need_help_hover_bg';
$def->properties['need_help_hover_bg']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
 
$def->properties['need_help_image'] = new ezcPersistentObjectProperty();
$def->properties['need_help_image']->columnName   = 'need_help_image';
$def->properties['need_help_image']->propertyName = 'need_help_image';
$def->properties['need_help_image']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
 
$def->properties['need_help_image_path'] = new ezcPersistentObjectProperty();
$def->properties['need_help_image_path']->columnName   = 'need_help_image_path';
$def->properties['need_help_image_path']->propertyName = 'need_help_image_path';
$def->properties['need_help_image_path']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['copyright_image'] = new ezcPersistentObjectProperty();
$def->properties['copyright_image']->columnName   = 'copyright_image';
$def->properties['copyright_image']->propertyName = 'copyright_image';
$def->properties['copyright_image']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
 
$def->properties['copyright_image_path'] = new ezcPersistentObjectProperty();
$def->properties['copyright_image_path']->columnName   = 'copyright_image_path';
$def->properties['copyright_image_path']->propertyName = 'copyright_image_path';
$def->properties['copyright_image_path']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
 
$def->properties['widget_copyright_url'] = new ezcPersistentObjectProperty();
$def->properties['widget_copyright_url']->columnName   = 'widget_copyright_url';
$def->properties['widget_copyright_url']->propertyName = 'widget_copyright_url';
$def->properties['widget_copyright_url']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
 
$def->properties['show_copyright'] = new ezcPersistentObjectProperty();
$def->properties['show_copyright']->columnName   = 'show_copyright';
$def->properties['show_copyright']->propertyName = 'show_copyright';
$def->properties['show_copyright']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['custom_status_css'] = new ezcPersistentObjectProperty();
$def->properties['custom_status_css']->columnName   = 'custom_status_css';
$def->properties['custom_status_css']->propertyName = 'custom_status_css';
$def->properties['custom_status_css']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
 
$def->properties['custom_container_css'] = new ezcPersistentObjectProperty();
$def->properties['custom_container_css']->columnName   = 'custom_container_css';
$def->properties['custom_container_css']->propertyName = 'custom_container_css';
$def->properties['custom_container_css']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
 
$def->properties['custom_widget_css'] = new ezcPersistentObjectProperty();
$def->properties['custom_widget_css']->columnName   = 'custom_widget_css';
$def->properties['custom_widget_css']->propertyName = 'custom_widget_css';
$def->properties['custom_widget_css']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
 
$def->properties['need_help_header'] = new ezcPersistentObjectProperty();
$def->properties['need_help_header']->columnName   = 'need_help_header';
$def->properties['need_help_header']->propertyName = 'need_help_header';
$def->properties['need_help_header']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
 
$def->properties['need_help_text'] = new ezcPersistentObjectProperty();
$def->properties['need_help_text']->columnName   = 'need_help_text';
$def->properties['need_help_text']->propertyName = 'need_help_text';
$def->properties['need_help_text']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
 
$def->properties['online_text'] = new ezcPersistentObjectProperty();
$def->properties['online_text']->columnName   = 'online_text';
$def->properties['online_text']->propertyName = 'online_text';
$def->properties['online_text']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
 
$def->properties['offline_text'] = new ezcPersistentObjectProperty();
$def->properties['offline_text']->columnName   = 'offline_text';
$def->properties['offline_text']->propertyName = 'offline_text';
$def->properties['offline_text']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>