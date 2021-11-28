<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_widget_theme";
$def->class = "erLhAbstractModelWidgetTheme";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

foreach (['alias','widget_pbottom','widget_position','notification_configuration','bot_configuration','buble_operator_text_color','buble_operator_title_color','buble_operator_background','buble_visitor_text_color','buble_visitor_title_color','buble_visitor_background','department_select','department_title','noonline_operators_offline','noonline_operators','pending_join_queue','pending_join','support_closed','support_joined','intro_operator_text','explain_text','offline_text','online_text','need_help_text','need_help_header','custom_popup_css','custom_widget_css','custom_container_css','custom_status_css','show_copyright','widget_copyright_url','copyright_image_path','copyright_image','need_help_image_path','need_help_image','need_help_hover_bg','need_help_close_bg','need_help_border','need_help_tcolor','need_help_bcolor','logo_image_path','logo_image','offline_image_path','need_help_close_hover_bg','operator_image_path','online_image_path','offline_image','close_image_path','restore_image_path','hide_popup','hide_close','popup_image_path','minimize_image_path','popup_image','close_image','restore_image','minimize_image','operator_image','online_image','name','name_company','onl_bcolor','bor_bcolor','text_color','header_background','bot_status_text','widget_border_color'] as $posAttr) {
    $def->properties[$posAttr] = new ezcPersistentObjectProperty();
    $def->properties[$posAttr]->columnName   = $posAttr;
    $def->properties[$posAttr]->propertyName = $posAttr;
    $def->properties[$posAttr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
}

foreach (['widget_pright','widget_popheight','widget_popwidth','widget_survey','widget_show_leave_form','enable_widget_embed_override','modified','widget_response_width','hide_ts','show_voting','show_status_delay','show_need_help_delay','show_need_help_timeout','show_need_help','header_height','header_padding','modern_look','widget_border_width'] as $posAttr) {
    $def->properties[$posAttr] = new ezcPersistentObjectProperty();
    $def->properties[$posAttr]->columnName   = $posAttr;
    $def->properties[$posAttr]->propertyName = $posAttr;
    $def->properties[$posAttr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('lhabstract.erlhabstractmodelwidgettheme.posdefinition',array('def' => & $def));

return $def;

?>