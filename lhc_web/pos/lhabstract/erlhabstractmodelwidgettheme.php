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

$def->properties['name_company'] = new ezcPersistentObjectProperty();
$def->properties['name_company']->columnName   = 'name_company';
$def->properties['name_company']->propertyName = 'name_company';
$def->properties['name_company']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['onl_bcolor'] = new ezcPersistentObjectProperty();
$def->properties['onl_bcolor']->columnName   = 'onl_bcolor';
$def->properties['onl_bcolor']->propertyName = 'onl_bcolor';
$def->properties['onl_bcolor']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['bor_bcolor'] = new ezcPersistentObjectProperty();
$def->properties['bor_bcolor']->columnName   = 'bor_bcolor';
$def->properties['bor_bcolor']->propertyName = 'bor_bcolor';
$def->properties['bor_bcolor']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

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

$def->properties['operator_image'] = new ezcPersistentObjectProperty();
$def->properties['operator_image']->columnName   = 'operator_image';
$def->properties['operator_image']->propertyName = 'operator_image';
$def->properties['operator_image']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['minimize_image'] = new ezcPersistentObjectProperty();
$def->properties['minimize_image']->columnName   = 'minimize_image';
$def->properties['minimize_image']->propertyName = 'minimize_image';
$def->properties['minimize_image']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['restore_image'] = new ezcPersistentObjectProperty();
$def->properties['restore_image']->columnName   = 'restore_image';
$def->properties['restore_image']->propertyName = 'restore_image';
$def->properties['restore_image']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['close_image'] = new ezcPersistentObjectProperty();
$def->properties['close_image']->columnName   = 'close_image';
$def->properties['close_image']->propertyName = 'close_image';
$def->properties['close_image']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['popup_image'] = new ezcPersistentObjectProperty();
$def->properties['popup_image']->columnName   = 'popup_image';
$def->properties['popup_image']->propertyName = 'popup_image';
$def->properties['popup_image']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['minimize_image_path'] = new ezcPersistentObjectProperty();
$def->properties['minimize_image_path']->columnName   = 'minimize_image_path';
$def->properties['minimize_image_path']->propertyName = 'minimize_image_path';
$def->properties['minimize_image_path']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['popup_image_path'] = new ezcPersistentObjectProperty();
$def->properties['popup_image_path']->columnName   = 'popup_image_path';
$def->properties['popup_image_path']->propertyName = 'popup_image_path';
$def->properties['popup_image_path']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['hide_close'] = new ezcPersistentObjectProperty();
$def->properties['hide_close']->columnName   = 'hide_close';
$def->properties['hide_close']->propertyName = 'hide_close';
$def->properties['hide_close']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['hide_popup'] = new ezcPersistentObjectProperty();
$def->properties['hide_popup']->columnName   = 'hide_popup';
$def->properties['hide_popup']->propertyName = 'hide_popup';
$def->properties['hide_popup']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['restore_image_path'] = new ezcPersistentObjectProperty();
$def->properties['restore_image_path']->columnName   = 'restore_image_path';
$def->properties['restore_image_path']->propertyName = 'restore_image_path';
$def->properties['restore_image_path']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['close_image_path'] = new ezcPersistentObjectProperty();
$def->properties['close_image_path']->columnName   = 'close_image_path';
$def->properties['close_image_path']->propertyName = 'close_image_path';
$def->properties['close_image_path']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['offline_image'] = new ezcPersistentObjectProperty();
$def->properties['offline_image']->columnName   = 'offline_image';
$def->properties['offline_image']->propertyName = 'offline_image';
$def->properties['offline_image']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['online_image_path'] = new ezcPersistentObjectProperty();
$def->properties['online_image_path']->columnName   = 'online_image_path';
$def->properties['online_image_path']->propertyName = 'online_image_path';
$def->properties['online_image_path']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['operator_image_path'] = new ezcPersistentObjectProperty();
$def->properties['operator_image_path']->columnName   = 'operator_image_path';
$def->properties['operator_image_path']->propertyName = 'operator_image_path';
$def->properties['operator_image_path']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

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

// Used to style layout of status widget
$def->properties['custom_status_css'] = new ezcPersistentObjectProperty();
$def->properties['custom_status_css']->columnName   = 'custom_status_css';
$def->properties['custom_status_css']->propertyName = 'custom_status_css';
$def->properties['custom_status_css']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Used to style container of iframe
$def->properties['custom_container_css'] = new ezcPersistentObjectProperty();
$def->properties['custom_container_css']->columnName   = 'custom_container_css';
$def->properties['custom_container_css']->propertyName = 'custom_container_css';
$def->properties['custom_container_css']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Used in widget.php for styling
$def->properties['custom_widget_css'] = new ezcPersistentObjectProperty();
$def->properties['custom_widget_css']->columnName   = 'custom_widget_css';
$def->properties['custom_widget_css']->propertyName = 'custom_widget_css';
$def->properties['custom_widget_css']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Used on userchat.php for popup layout styling
$def->properties['custom_popup_css'] = new ezcPersistentObjectProperty();
$def->properties['custom_popup_css']->columnName   = 'custom_popup_css';
$def->properties['custom_popup_css']->propertyName = 'custom_popup_css';
$def->properties['custom_popup_css']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
 
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
 
$def->properties['explain_text'] = new ezcPersistentObjectProperty();
$def->properties['explain_text']->columnName   = 'explain_text';
$def->properties['explain_text']->propertyName = 'explain_text';
$def->properties['explain_text']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
 
$def->properties['intro_operator_text'] = new ezcPersistentObjectProperty();
$def->properties['intro_operator_text']->columnName   = 'intro_operator_text';
$def->properties['intro_operator_text']->propertyName = 'intro_operator_text';
$def->properties['intro_operator_text']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
 
$def->properties['header_height'] = new ezcPersistentObjectProperty();
$def->properties['header_height']->columnName   = 'header_height';
$def->properties['header_height']->propertyName = 'header_height';
$def->properties['header_height']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
 
$def->properties['header_padding'] = new ezcPersistentObjectProperty();
$def->properties['header_padding']->columnName   = 'header_padding';
$def->properties['header_padding']->propertyName = 'header_padding';
$def->properties['header_padding']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
 
$def->properties['widget_border_width'] = new ezcPersistentObjectProperty();
$def->properties['widget_border_width']->columnName   = 'widget_border_width';
$def->properties['widget_border_width']->propertyName = 'widget_border_width';
$def->properties['widget_border_width']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Allow to change default important widget text's

$def->properties['support_joined'] = new ezcPersistentObjectProperty();
$def->properties['support_joined']->columnName   = 'support_joined';
$def->properties['support_joined']->propertyName = 'support_joined';
$def->properties['support_joined']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['support_closed'] = new ezcPersistentObjectProperty();
$def->properties['support_closed']->columnName   = 'support_closed';
$def->properties['support_closed']->propertyName = 'support_closed';
$def->properties['support_closed']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['pending_join'] = new ezcPersistentObjectProperty();
$def->properties['pending_join']->columnName   = 'pending_join';
$def->properties['pending_join']->propertyName = 'pending_join';
$def->properties['pending_join']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['noonline_operators'] = new ezcPersistentObjectProperty();
$def->properties['noonline_operators']->columnName   = 'noonline_operators';
$def->properties['noonline_operators']->propertyName = 'noonline_operators';
$def->properties['noonline_operators']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['noonline_operators_offline'] = new ezcPersistentObjectProperty();
$def->properties['noonline_operators_offline']->columnName   = 'noonline_operators_offline';
$def->properties['noonline_operators_offline']->propertyName = 'noonline_operators_offline';
$def->properties['noonline_operators_offline']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Show need help tooltip?
$def->properties['show_need_help'] = new ezcPersistentObjectProperty();
$def->properties['show_need_help']->columnName   = 'show_need_help';
$def->properties['show_need_help']->propertyName = 'show_need_help';
$def->properties['show_need_help']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Need help tooltip timeout, after how many hours show again tooltip?
$def->properties['show_need_help_timeout'] = new ezcPersistentObjectProperty();
$def->properties['show_need_help_timeout']->columnName   = 'show_need_help_timeout';
$def->properties['show_need_help_timeout']->propertyName = 'show_need_help_timeout';
$def->properties['show_need_help_timeout']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// New version
// Show voting thumbs
$def->properties['show_voting'] = new ezcPersistentObjectProperty();
$def->properties['show_voting']->columnName   = 'show_voting';
$def->properties['show_voting']->propertyName = 'show_voting';
$def->properties['show_voting']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// What title show instaed of department, let say location
$def->properties['department_title'] = new ezcPersistentObjectProperty();
$def->properties['department_title']->columnName   = 'department_title';
$def->properties['department_title']->propertyName = 'department_title';
$def->properties['department_title']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Select department text will be shown if filled
$def->properties['department_select'] = new ezcPersistentObjectProperty();
$def->properties['department_select']->columnName   = 'department_select';
$def->properties['department_select']->propertyName = 'department_select';
$def->properties['department_select']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Select department text will be shown if filled
$def->properties['buble_visitor_background'] = new ezcPersistentObjectProperty();
$def->properties['buble_visitor_background']->columnName   = 'buble_visitor_background';
$def->properties['buble_visitor_background']->propertyName = 'buble_visitor_background';
$def->properties['buble_visitor_background']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Buble visitor title color
$def->properties['buble_visitor_title_color'] = new ezcPersistentObjectProperty();
$def->properties['buble_visitor_title_color']->columnName   = 'buble_visitor_title_color';
$def->properties['buble_visitor_title_color']->propertyName = 'buble_visitor_title_color';
$def->properties['buble_visitor_title_color']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Buble visitor text color
$def->properties['buble_visitor_text_color'] = new ezcPersistentObjectProperty();
$def->properties['buble_visitor_text_color']->columnName   = 'buble_visitor_text_color';
$def->properties['buble_visitor_text_color']->propertyName = 'buble_visitor_text_color';
$def->properties['buble_visitor_text_color']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Buble operator background
$def->properties['buble_operator_background'] = new ezcPersistentObjectProperty();
$def->properties['buble_operator_background']->columnName   = 'buble_operator_background';
$def->properties['buble_operator_background']->propertyName = 'buble_operator_background';
$def->properties['buble_operator_background']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Buble operator title color
$def->properties['buble_operator_title_color'] = new ezcPersistentObjectProperty();
$def->properties['buble_operator_title_color']->columnName   = 'buble_operator_title_color';
$def->properties['buble_operator_title_color']->propertyName = 'buble_operator_title_color';
$def->properties['buble_operator_title_color']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Buble operator text color
$def->properties['buble_operator_text_color'] = new ezcPersistentObjectProperty();
$def->properties['buble_operator_text_color']->columnName   = 'buble_operator_text_color';
$def->properties['buble_operator_text_color']->propertyName = 'buble_operator_text_color';
$def->properties['buble_operator_text_color']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Show Timestamp For visitors
$def->properties['hide_ts'] = new ezcPersistentObjectProperty();
$def->properties['hide_ts']->columnName   = 'hide_ts';
$def->properties['hide_ts']->propertyName = 'hide_ts';
$def->properties['hide_ts']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// From what width response widget layout should trigger
// Overrides default 640px
$def->properties['widget_response_width'] = new ezcPersistentObjectProperty();
$def->properties['widget_response_width']->columnName   = 'widget_response_width';
$def->properties['widget_response_width']->propertyName = 'widget_response_width';
$def->properties['widget_response_width']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('lhabstract.erlhabstractmodelwidgettheme.posdefinition',array('def' => & $def));

return $def;

?>