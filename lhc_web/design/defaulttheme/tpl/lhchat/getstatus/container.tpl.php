<?php 
$iconsStatuses['restore_image_url'] = ($theme !== false && $theme->restore_image_url !== false && strpos($theme->restore_image_url, 'http') !== false);
$iconsStatuses['minimize_image_url'] = ($theme !== false && $theme->minimize_image_url !== false && strpos($theme->minimize_image_url, 'http') !== false);
$iconsStatuses['close_image_url'] = ($theme !== false && $theme->close_image_url !== false && strpos($theme->close_image_url, 'http') !== false);
$iconsStatuses['popup_image_url'] = ($theme !== false && $theme->popup_image_url !== false && strpos($theme->popup_image_url, 'http') !== false);
$iconsStatuses['online_image_url'] = ($theme !== false && $theme->online_image_url !== false && strpos($theme->online_image_url, 'http') !== false);
?>
<?php if ($theme !== false && $theme->modern_look == 0) : ?>
this.iframe_html = '<div id="<?php echo $chatCSSLayoutOptions['container_id']?>" <?= isset($currentPosition['full_height']) && $currentPosition['full_height'] ? 'style="height:100%"' : '' ?>>' +
    '<?php if ($theme !== false && isset($theme->bot_configuration_array['custom_html_header'])) : ?><?php echo str_replace(array("\n","\r"), '',$theme->bot_configuration_array['custom_html_header'])?><?php endif?><div id="<?php echo $chatCSSPrefix?>_header"><?php if ($theme !== false && isset($theme->bot_configuration_array['custom_html_header_body'])) : ?><?php echo str_replace(array("\n","\r"), '',$theme->bot_configuration_array['custom_html_header_body'])?><?php endif?><?php if ($theme === false || $theme->hide_close == 0) : ?><a href="#" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>" id="<?php echo $chatCSSPrefix?>_close"><img src="<?php if ($iconsStatuses['close_image_url'] == false) : ?><?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php endif?><?php if ($theme !== false && $theme->close_image_url != '') : ?><?php echo $theme->close_image_url;?><?php else : ?><?php echo erLhcoreClassDesign::design('images/icons/cancel.png');?><?php endif;?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>" /></a><?php endif;?><?php if (erLhcoreClassModelChatConfig::fetch('disable_popup_restore')->current_value == 0 && ($theme === false || $theme->hide_popup == 0)) : ?><a href="#" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Open in a new window')?>" id="<?php echo $chatCSSPrefix?>_remote_window"><img src="<?php if ($iconsStatuses['popup_image_url'] == false) : ?><?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php endif; if ($theme !== false && $theme->popup_image_url != '') : ?><?php echo $theme->popup_image_url;?><?php else : ?><?php echo erLhcoreClassDesign::design('images/icons/application_double.png');?><?php endif;?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Open in a new window')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Open in a new window')?>" /></a><?php endif; ?><a href="#" id="<?php echo $chatCSSPrefix?>_min" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Minimize/Restore')?>"></a><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','New messages')?>" id="<?php echo $chatCSSPrefix?>-msg-number"></i></div>' +
    this.iframe_html + '</div>';
raw_css = "#<?php echo $chatCSSLayoutOptions['container_id']?>.<?php echo $chatCSSPrefix?>-min {overflow:hidden}#<?php echo $chatCSSPrefix?>_remote_window{padding-left:5px;}.<?php echo $chatCSSPrefix?>-min #<?php echo $chatCSSPrefix?>_header{min-width:107px} .<?php echo $chatCSSPrefix?>-min #<?php echo $chatCSSPrefix?>_iframe_container{display:none} .<?php echo $chatCSSPrefix?>-no-transition{ -webkit-transition: none !important; -moz-transition: none !important;-o-transition: none !important;-ms-transition: none !important;transition: none !important;}\n#<?php echo $chatCSSLayoutOptions['container_id']?>.<?php echo $chatCSSPrefix?>-delayed{visibility:hidden;position: fixed;left: -90000px!important;right: auto!important;}#<?php echo $chatCSSLayoutOptions['container_id']?>.<?php echo $chatCSSPrefix?>-delayed .<?php echo $chatCSSPrefix?>-cf{display:none}\n#<?php echo $chatCSSLayoutOptions['container_id']?> * {line-height:100%;direction:<?php (erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language') == 'ltr' || erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language') == '') ? print 'ltr;text-align:left;' : print 'rtl;text-align:right;'; ?>;font-family:arial;font-size:12px;line-height:100%;box-sizing: content-box;-moz-box-sizing:content-box;padding:0;margin:0;}\n#<?php echo $chatCSSLayoutOptions['container_id']?> img {border:0;}\n#<?php echo $chatCSSPrefix?>_header{position:relative;z-index:2147483640;height:<?php ($theme !== false && $theme->header_height > 0) ? print $theme->header_height : print '17' ?>px;overflow:hidden;text-align:right;clear:both;background-color:#<?php $theme !== false ? print $theme->header_background : print '525252' ?>;padding:<?php ($theme !== false && $theme->header_padding > 0) ? print $theme->header_padding : print '5' ?>px;}#<?php echo $chatCSSPrefix?>-msg-number{float: left;color: #FFF;font-size: 12px;font-weight: bold;padding-left: 5px;line-height: 20px;} \n#<?php echo $chatCSSPrefix?>_min{float:left;padding:2px;}#<?php echo $chatCSSPrefix?>_remote_window,#<?php echo $chatCSSPrefix?>_close{padding:2px;float:right;}.<?php echo $chatCSSPrefix?>-min #<?php echo $chatCSSPrefix?>_min:before{content:url(<?php if ($iconsStatuses['restore_image_url'] == false) : ?><?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php endif; if ($theme !== false && $theme->restore_image_url != '') : ?><?php echo $theme->restore_image_url;?><?php else : ?><?php echo erLhcoreClassDesign::design('images/icons/restore.png');?><?php endif;?>)}#<?php echo $chatCSSPrefix?>_min:before{content: url('<?php if ($iconsStatuses['minimize_image_url'] == false) : ?><?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php endif; if ($theme !== false && $theme->minimize_image_url != '') : ?><?php echo $theme->minimize_image_url;?><?php else : ?><?php echo erLhcoreClassDesign::design('images/icons/min.png');?><?php endif;?>'); position: relative;left:0;top;0} #<?php echo $chatCSSPrefix?>_min{width:14px;height:14px;}\n#<?php echo $chatCSSPrefix?>_close:hover,#<?php echo $chatCSSPrefix?>_min:hover,#<?php echo $chatCSSPrefix?>_remote_window:hover{opacity:0.4;}\n#<?php echo $chatCSSLayoutOptions['container_id']?> {background-color:#FFF;-moz-user-select:none; -khtml-user-drag:element;cursor: move;cursor: -moz-grab;cursor: -webkit-grab;max-height: 100%;overflow: auto;\nz-index:2147483640;\n position: fixed;<?php echo $currentPosition['position_body']?>;-webkit-box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);-moz-box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);-webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px; }\n#<?php echo $chatCSSLayoutOptions['container_id']?> iframe{position:relative;display:block;}\n#<?php echo $chatCSSLayoutOptions['container_id']?> #<?php echo $chatCSSPrefix?>_iframe_container{border:<?php ($theme !== false && $theme->widget_border_width > 0) ? print $theme->widget_border_width : print '1' ?>px solid #<?php $theme !== false ? print $theme->widget_border_color : print 'cccccc' ?>;border-top: 0;border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;overflow: hidden;}\n#<?php echo $chatCSSLayoutOptions['container_id']?> iframe.<?php echo $chatCSSPrefix?>-loading{\nbackground: #FFF url(<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::design('images/general/loading.gif');?>) no-repeat center center; }\n@media only screen and (max-device-width : <?php ($theme !== false && $theme->widget_response_width > 0) ? print $theme->widget_response_width : print 640?>px) { .<?php echo $chatCSSPrefix?>-opened{position: fixed; overflow: hidden; right: 0px; left: 0px; top: 0px; bottom: 0px;} #<?php echo $chatCSSPrefix?>_header{height:30px;} #<?php echo $chatCSSPrefix?>_header a{padding:7px;}#<?php echo $chatCSSLayoutOptions['container_id']?>{position:fixed;left:0!important;right:0!important;bottom:0!important;top:0!important;border:0;border-radius:0}#<?php echo $chatCSSLayoutOptions['container_id']?> #<?php echo $chatCSSPrefix?>_iframe_container{border:0;height: calc(100% - 40px)}#<?php echo $chatCSSLayoutOptions['container_id']?> iframe{width:100% !important;height: 100%!important} .<?php echo $chatCSSPrefix?>-min #<?php echo $chatCSSPrefix?>_header a{padding:2px;} #<?php echo $chatCSSLayoutOptions['container_id']?>.<?php echo $chatCSSPrefix?>-min{<?php echo $currentPosition['mobile_position']?>}}";
<?php else : ?>
this.iframe_html = '<div id="<?php echo $chatCSSLayoutOptions['container_id']?>" <?= isset($currentPosition['full_height']) && $currentPosition['full_height'] ? 'style="height:100%"' : '' ?>>' +
                              '<a class="status-icon ' +
                              (this.isOnline == true ? 'online-status-icon' : 'offline-status-icon') +
                              '" id="<?php echo $chatCSSPrefix?>_status-icon-restore" href="#" ><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','New messages')?>" id="<?php echo $chatCSSPrefix?>-msg-number"></i></a><div id="<?php echo $chatCSSPrefix?>_header"><ul class="<?php echo $chatCSSPrefix?>-cf"><li><a href="#">&#9776;</a><ul class="<?php echo $chatCSSPrefix?>-cf"><?php if ($theme === false || $theme->hide_close == 0) : ?><li><a href="#" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>" id="<?php echo $chatCSSPrefix?>_close"><img src="<?php if ($iconsStatuses['close_image_url'] == false) : ?><?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php endif?><?php if ($theme !== false && $theme->close_image_url != '') : ?><?php echo $theme->close_image_url;?><?php else : ?><?php echo erLhcoreClassDesign::design('images/icons/cancel.png');?><?php endif;?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>" /></a></li><?php endif;?><?php if (erLhcoreClassModelChatConfig::fetch('disable_popup_restore')->current_value == 0 && ($theme === false || $theme->hide_popup == 0)) : ?><li><a href="#" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Open in a new window')?>" id="<?php echo $chatCSSPrefix?>_remote_window"><img src="<?php if ($iconsStatuses['popup_image_url'] == false) : ?><?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php endif; if ($theme !== false && $theme->popup_image_url != '') : ?><?php echo $theme->popup_image_url;?><?php else : ?><?php echo erLhcoreClassDesign::design('images/icons/application_double.png');?><?php endif;?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Open in a new window')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Open in a new window')?>" /></a></li><?php endif; ?></ul></li></ul><a href="#" id="<?php echo $chatCSSPrefix?>_min" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Minimize/Restore')?>"></a></div>' +
                              this.iframe_html + '</div>';

raw_css = "#<?php echo $chatCSSLayoutOptions['container_id']?>.<?php echo $chatCSSPrefix?>-min {overflow:hidden}#<?php echo $chatCSSLayoutOptions['container_id']?> .status-icon{display:none;}#<?php echo $chatCSSLayoutOptions['container_id']?>.<?php echo $chatCSSPrefix?>-min .status-icon{display:inline-block;<?php echo $currentPosition['border_status_modern']?>;<?php echo $currentPosition['widget_radius_modern']?>;-webkit-box-shadow: <?php echo $currentPosition['shadow_modern']?> rgba(50, 50, 50, 0.5);-moz-box-shadow:<?php echo $currentPosition['shadow_modern']?> rgba(50, 50, 50, 0.5);box-shadow: <?php echo $currentPosition['shadow_modern']?> rgba(50, 50, 50, 0.5);text-decoration:none;height:41px;width:41px;font-weight:bold;color:<?php $theme !== false ? print '#'.$theme->text_color : print '#000' ?>;display:block;padding:10px;background:#<?php $theme !== false ? print $theme->onl_bcolor : print '0c8fc4' ?> url('<?php if ($iconsStatuses['online_image_url'] == false) : ?><?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php endif; ?><?php if ($theme !== false && $theme->online_image_url !== false) : print $theme->online_image_url; else : ?><?php echo erLhcoreClassDesign::design('images/getstatus/online.svg');?><?php endif;?>') no-repeat center center}#<?php echo $chatCSSLayoutOptions['container_id']?>.<?php echo $chatCSSPrefix?>-min .status-icon.offline-status-icon{background:#888888 url('<?php if ($iconsStatuses['offline_image_url'] == false) : ?><?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php endif; ?><?php if ($theme !== false && $theme->offline_image_url !== false) : print $theme->offline_image_url; else : ?><?php echo erLhcoreClassDesign::design('images/getstatus/offline.svg');?><?php endif;?>') no-repeat center center}<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/burger_menu_css.tpl.php')); ?> #<?php echo $chatCSSPrefix?>_remote_window{padding-left:5px;}.<?php echo $chatCSSPrefix?>-min #<?php echo $chatCSSPrefix?>_header{min-width:107px} .<?php echo $chatCSSPrefix?>-min #<?php echo $chatCSSPrefix?>_iframe_container{display:none} .<?php echo $chatCSSPrefix?>-no-transition{ -webkit-transition: none !important; -moz-transition: none !important;-o-transition: none !important;-ms-transition: none !important;transition: none !important;}\n#<?php echo $chatCSSLayoutOptions['container_id']?>.<?php echo $chatCSSPrefix?>-delayed{visibility:hidden;position: fixed;left: -90000px!important;right: auto!important;}#<?php echo $chatCSSLayoutOptions['container_id']?>.<?php echo $chatCSSPrefix?>-delayed .<?php echo $chatCSSPrefix?>-cf{display:none}\n#<?php echo $chatCSSLayoutOptions['container_id']?> * {line-height:100%;direction:<?php (erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language') == 'ltr' || erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language') == '') ? print 'ltr;text-align:left;' : print 'rtl;text-align:right;'; ?>;font-family:arial;font-size:12px;line-height:100%;box-sizing: content-box;-moz-box-sizing:content-box;padding:0;margin:0;}\n#<?php echo $chatCSSLayoutOptions['container_id']?> img {border:0;max-width: none}\n#<?php echo $chatCSSPrefix?>_header{position:relative;z-index:2147483640;height:<?php ($theme !== false && $theme->header_height > 0) ? print $theme->header_height : print '17' ?>px;text-align:right;clear:both;background-color:#<?php $theme !== false ? print $theme->header_background : print '525252' ?>;padding:<?php ($theme !== false && $theme->header_padding > 0) ? print $theme->header_padding : print '5' ?>px;}#<?php echo $chatCSSPrefix?>-msg-number{float: left;color: #ffffff;font-size: 12px;font-weight: normal;line-height: 23px;position: absolute;background-color: red;border-radius: 37px;display: inline-block;padding-left: 8px;padding-right: 8px;margin-top: -5px;margin-left: -4px;} \n#<?php echo $chatCSSPrefix?>_min{float:right;padding:2px;}.<?php echo $chatCSSPrefix?>-min #<?php echo $chatCSSPrefix?>_min:before{content:url(<?php if ($iconsStatuses['restore_image_url'] == false) : ?><?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php endif; if ($theme !== false && $theme->restore_image_url != '') : ?><?php echo $theme->restore_image_url;?><?php else : ?><?php echo erLhcoreClassDesign::design('images/icons/restore.png');?><?php endif;?>)}#<?php echo $chatCSSPrefix?>_min:before{content: url('<?php if ($iconsStatuses['minimize_image_url'] == false) : ?><?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php endif; if ($theme !== false && $theme->minimize_image_url != '') : ?><?php echo $theme->minimize_image_url;?><?php else : ?><?php echo erLhcoreClassDesign::design('images/icons/min.png');?><?php endif;?>'); position: relative;left:0;top;0} #<?php echo $chatCSSPrefix?>_min{width:14px;height:14px;}\n#<?php echo $chatCSSPrefix?>_min:hover{opacity:0.4;}\n#<?php echo $chatCSSLayoutOptions['container_id']?> {background-color:#FFF;-moz-user-select:none; -khtml-user-drag:element;cursor: move;cursor: -moz-grab;cursor: -webkit-grab;max-height: 100%;	overflow: auto;\nz-index:2147483640;\n position: fixed;<?php echo $currentPosition['position_body']?>;-webkit-box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);-moz-box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);-webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px; }\n#<?php echo $chatCSSLayoutOptions['container_id']?> iframe{position:relative;display:block;}\n#<?php echo $chatCSSLayoutOptions['container_id']?> #<?php echo $chatCSSPrefix?>_iframe_container{border:<?php ($theme !== false && $theme->widget_border_width > 0) ? print $theme->widget_border_width : print '1' ?>px solid #<?php $theme !== false ? print $theme->widget_border_color : print 'cccccc' ?>;border-top: 0;border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;overflow: hidden;}\n#<?php echo $chatCSSLayoutOptions['container_id']?> iframe.<?php echo $chatCSSPrefix?>-loading{\nbackground: #FFF url(<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::design('images/general/loading.gif');?>) no-repeat center center; }\n@media only screen and (max-device-width : <?php ($theme !== false && $theme->widget_response_width > 0) ? print $theme->widget_response_width : print 640?>px) {ul.<?php echo $chatCSSPrefix?>-cf li ul a{padding:8px 14px!important} ul.<?php echo $chatCSSPrefix?>-cf li:hover ul, ul.<?php echo $chatCSSPrefix?>-cf li ul {top: 30px;} .<?php echo $chatCSSPrefix?>-opened{position: fixed; overflow: hidden; right: 0px; left: 0px; top: 0px; bottom: 0px;} #<?php echo $chatCSSPrefix?>_header{height:30px;} #<?php echo $chatCSSPrefix?>_header a{padding:7px;}#<?php echo $chatCSSLayoutOptions['container_id']?>{position:fixed;left:0!important;right:0!important;bottom:0!important;top:0!important;border:0;border-radius:0}#<?php echo $chatCSSLayoutOptions['container_id']?> #<?php echo $chatCSSPrefix?>_iframe_container{border:0;height: calc(100% - 40px)}#<?php echo $chatCSSLayoutOptions['container_id']?> iframe{width:100% !important;height: 100%!important}} .<?php echo $chatCSSPrefix?>-min #<?php echo $chatCSSPrefix?>_header{display:none;}.<?php echo $chatCSSPrefix?>-min #<?php echo $chatCSSPrefix?>_header a{padding:2px;} #<?php echo $chatCSSLayoutOptions['container_id']?>.<?php echo $chatCSSPrefix?>-min .status-icon{<?php echo $currentPosition['border_status_modern']?>;<?php echo $currentPosition['widget_radius_modern']?>;-webkit-box-shadow: <?php echo $currentPosition['shadow_modern']?> rgba(50, 50, 50, 0.5);-moz-box-shadow:<?php echo $currentPosition['shadow_modern']?> rgba(50, 50, 50, 0.5);box-shadow: <?php echo $currentPosition['shadow_modern']?> rgba(50, 50, 50, 0.5);text-decoration:none;height:41px;width:41px;}#<?php echo $chatCSSLayoutOptions['container_id']?>.<?php echo $chatCSSPrefix?>-min{<?php echo $currentPosition['mobile_position_modern']?>}";
<?php endif; ?>
