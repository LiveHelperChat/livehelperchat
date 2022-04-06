showStatusWidget : function() {
<?php if ($position == 'original' || $position == '') :
// You can style bottom HTML whatever you want. ?>
<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/native_placement.tpl.php')); ?>

<?php elseif (in_array($position, array_keys($positionArgument))) : ?>
	this.removeById('<?php echo $chatCSSPrefix?>_status_container');

    <?php if ($theme !== false && $theme->modern_look == 0) : ?>
    var statusTEXT = '<?php if ($theme !== false && isset($theme->bot_configuration_array['custom_html_status'])) : ?><?php echo str_replace(array("\n","\r","'"), array('','','\\\''),$theme->bot_configuration_array['custom_html_status'])?><?php endif?><a id="'+(this.isOnline == true ? 'online-icon' : 'offline-icon')+'" class="status-icon" href="#" onclick="return lh_inst.lh_openchatWindow()" ><span class="<?php echo $chatCSSPrefix?>-text-status">'+(this.isOnline ? <?php if ($theme !== false && $theme->online_text !== '') : print json_encode($theme->online_text); else : ?><?php echo json_encode(htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Live help is online...'),ENT_QUOTES))?><?php endif?> : <?php if ($theme !== false && $theme->offline_text != '') : print json_encode($theme->offline_text); else : ?><?php echo json_encode(htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Live help is offline...'),ENT_QUOTES))?><?php endif?>)+'</span></a>';

    if (!this.cssStatusWasAdded) {
      	this.cssStatusWasAdded = true;
      	<?php 
      	$iconsStatuses = array(
      	    'online_image_url' => ($theme !== false && $theme->online_image_url !== false && strpos($theme->online_image_url, 'http') !== false),
      	    'offline_image_url' => ($theme !== false && $theme->offline_image_url !== false && strpos($theme->offline_image_url, 'http') !== false)
      	);          	          	
      	?>
    	var raw_css = "#<?php echo $chatCSSPrefix?>_status_container.hide-status,#<?php echo $chatCSSPrefix?>_status_container.hide-status-delay{display:none!important;}#<?php echo $chatCSSPrefix?>_status_container * {direction:<?php (erConfigClassLhConfig::getInstance()->getOverrideValue('site','dir_language') == 'ltr' || erConfigClassLhConfig::getInstance()->getOverrideValue('site','dir_language') == '') ? print 'ltr;text-align:left;' : print 'rtl;text-align:right;'; ?>;font-family:arial;font-size:12px;box-sizing: content-box;zoom:1;margin:0;padding:0}\n#<?php echo $chatCSSPrefix?>_status_container .status-icon{text-decoration:none;font-size:12px;font-weight:bold;color:<?php $theme !== false ? print '#'.$theme->text_color : print '#000' ?>;display:block;padding:<?php echo $currentPosition['padding_text']?>;background:url('<?php if ($theme !== false && $theme->online_image_url !== false) : print $theme->online_image_url; else : ?><?php echo erLhcoreClassSystem::getHost()?><?php echo erLhcoreClassDesign::design('images/icons/user_green_chat.png');?><?php endif;?>') no-repeat <?php echo $currentPosition['background_position']?> center}\n#<?php echo $chatCSSPrefix?>_status_container:hover{<?php echo $currentPosition['widget_hover']?>}\n#<?php echo $chatCSSPrefix?>_status_container #offline-icon{background-image:url('<?php if ($theme !== false && $theme->offline_image_url !== false) : print $theme->offline_image_url; else : ?><?php echo erLhcoreClassSystem::getHost()?><?php echo erLhcoreClassDesign::design('images/icons/user_gray_chat.png');?><?php endif;?>')}\n#<?php echo $chatCSSPrefix?>_status_container{box-sizing: content-box;<?php echo $currentPosition['widget_radius']?>-webkit-box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);<?php echo $currentPosition['border_widget']?>;-moz-box-shadow:<?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);padding:5px 0px 0px 5px;width:190px;font-family:arial;font-size:12px;transition: 1s;position:fixed;<?php echo $currentPosition['position']?>;background-color:#<?php $theme !== false ? print $theme->onl_bcolor : print 'f6f6f6' ?>;z-index:2147483647;}@media only screen and (max-width : 640px) {#<?php echo $chatCSSPrefix?>_need_help_container{display:none;}#<?php echo $chatCSSPrefix?>_status_container .status-icon{padding:32px 15px 9px 31px;background-position:center center;}#<?php echo $chatCSSPrefix?>_status_container .<?php echo $chatCSSPrefix?>-text-status{display:none} #<?php echo $chatCSSPrefix?>_status_container{<?php echo $currentPosition['mobile_position_status']?>}}\n";
    	this.addCss(raw_css<?php ($theme !== false && $theme->custom_status_css_front !== '') ? print '+\''.str_replace(array("\n","\r","'"), array('','','\\\''), $theme->custom_status_css_front).'\'' : '' ?>);
	};
    <?php else : ?>
        var statusTEXT = '<?php if ($theme !== false && isset($theme->bot_configuration_array['custom_html_status'])) : ?><?php echo str_replace(array("\n","\r","'"), array('','','\\\''),$theme->bot_configuration_array['custom_html_status'])?><?php endif?><i class="<?php echo $chatCSSPrefix?>-invitation-status" onclick="return lh_inst.lh_openchatWindow()">!</i><a id="'+(this.isOnline == true ? 'online-icon' : 'offline-icon')+'" class="status-icon" href="#" onclick="return lh_inst.lh_openchatWindow()" ></a>';

    if (!this.cssStatusWasAdded) {
        this.cssStatusWasAdded = true;
        <?php
        $iconsStatuses = array(
            'online_image_url' => ($theme !== false && $theme->online_image_url !== false && strpos($theme->online_image_url, 'http') !== false),
            'offline_image_url' => ($theme !== false && $theme->offline_image_url !== false && strpos($theme->offline_image_url, 'http') !== false)
        );
        ?>
        var raw_css = "#<?php echo $chatCSSPrefix?>_status_container.hide-status,#<?php echo $chatCSSPrefix?>_status_container.hide-status-delay{display:none!important;}#<?php echo $chatCSSPrefix?>_status_container * {direction:<?php (erConfigClassLhConfig::getInstance()->getOverrideValue('site','dir_language') == 'ltr' || erConfigClassLhConfig::getInstance()->getOverrideValue('site','dir_language') == '') ? print 'ltr;text-align:left;' : print 'rtl;text-align:right;'; ?>;font-family:arial;font-size:12px;box-sizing: content-box;zoom:1;margin:0;padding:0}#<?php echo $chatCSSPrefix?>_status_container .<?php echo $chatCSSPrefix?>-invitation-status{display:none;position:absolute;}#<?php echo $chatCSSPrefix?>_status_container.<?php echo $chatCSSPrefix?>_invitation-mode .<?php echo $chatCSSPrefix?>-invitation-status{width: 16px;height:16px;padding: 2px;border-radius: 10px;text-align: center;font-weight: bold;color: #FFF;background-color: red;display: inline-block;}\n#<?php echo $chatCSSPrefix?>_status_container .status-icon{<?php echo $currentPosition['border_status_modern']?>;<?php echo $currentPosition['widget_radius_modern']?>;-webkit-box-shadow: <?php echo $currentPosition['shadow_modern']?> rgba(50, 50, 50, 0.5);-moz-box-shadow:<?php echo $currentPosition['shadow_modern']?> rgba(50, 50, 50, 0.5);box-shadow: <?php echo $currentPosition['shadow_modern']?> rgba(50, 50, 50, 0.5);text-decoration:none;height:41px;width:41px;font-weight:bold;color:<?php $theme !== false ? print '#'.$theme->text_color : print '#000' ?>;display:block;padding:10px;background:#<?php $theme !== false ? print $theme->onl_bcolor : print '0c8fc4' ?> url('<?php if ($theme !== false && $theme->online_image_url !== false) : print $theme->online_image_url; else : ?><?php echo erLhcoreClassSystem::getHost()?><?php echo erLhcoreClassDesign::design('images/getstatus/online.svg');?><?php endif;?>') no-repeat center center}\n#<?php echo $chatCSSPrefix?>_status_container:hover{<?php echo $currentPosition['widget_hover_modern']?>}\n#<?php echo $chatCSSPrefix?>_status_container #offline-icon{background:#888888 url('<?php if ($theme !== false && $theme->offline_image_url !== false) : print $theme->offline_image_url; else : ?><?php echo erLhcoreClassSystem::getHost()?><?php echo erLhcoreClassDesign::design('images/getstatus/offline.svg');?><?php endif;?>') no-repeat center}\n#<?php echo $chatCSSPrefix?>_status_container{box-sizing: content-box;padding:5px 0px 0px 5px;font-family:arial;font-size:12px;transition: 1s;position:fixed;<?php echo $currentPosition['position_modern']?>;z-index:2147483647;}@media only screen and (max-width : 640px) {#<?php echo $chatCSSPrefix?>_need_help_container{display:none;}#<?php echo $chatCSSPrefix?>_status_container .status-icon{background-position:center center;}#<?php echo $chatCSSPrefix?>_status_container .<?php echo $chatCSSPrefix?>-text-status{display:none} #<?php echo $chatCSSPrefix?>_status_container{<?php echo $currentPosition['mobile_position_status_modern']?>}}\n";
        this.addCss(raw_css<?php ($theme !== false && $theme->custom_status_css_front !== '') ? print '+\''.str_replace(array("\n","\r","'"), array('','','\\\''), $theme->custom_status_css_front).'\'' : '' ?>);
    };
    <?php endif; ?>

	<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/we_here.tpl.php')); ?>

    var statusInitial = '';
    <?php if ($theme !== false && $theme->show_status_delay > 0) : ?>
        statusInitial = ' class="hide-status-delay" ';
        setTimeout( function() {
            var dm = document.getElementById('<?php echo $chatCSSPrefix?>_status_container');
            that.removeClass(dm,'hide-status-delay');
        },<?php echo $theme->show_status_delay*1000?>);
    <?php endif ?>

    var htmlStatus = '<div id="<?php echo $chatCSSPrefix?>_status_container"' + statusInitial + '>'+subStatus+statusTEXT+'</div>';

    var fragment = this.appendHTML(htmlStatus);
    
    document.body.insertBefore(fragment, document.body.childNodes[0]);

    var that = this;
    if (subStatus != '') {
        document.getElementById('<?php echo $chatCSSPrefix?>_need_help_image').onclick = function() { that.lhc_need_help_click(); return false; };
        document.getElementById('<?php echo $chatCSSPrefix?>_need_help_main_title').onclick = function() { that.lhc_need_help_click(); return false; };
        document.getElementById('<?php echo $chatCSSPrefix?>_need_help_sub_title').onclick = function() { that.lhc_need_help_click(); return false; };
        document.getElementById('<?php echo $chatCSSPrefix?>_need_help_close').onclick = function() { that.lhc_need_help_hide(); return false; };
    }

<?php endif; ?>
	if (typeof <?php echo $chatOptionsVariable?> != 'undefined' && typeof <?php echo $chatOptionsVariable?>.callback != 'undefined' && typeof <?php echo $chatOptionsVariable?>.callback.show_widget_cb != 'undefined') {
		<?php echo $chatOptionsVariable?>.callback.show_widget_cb(this);    		
	};
},
