iframePreloaded : false,
iframeCustomUrl : false,

preloadWidget : function() {
    <?php if ((int)erLhcoreClassModelChatConfig::fetch('preload_iframes')->current_value == 1) : ?>
    var locationCurrent = encodeURIComponent(window.location.href.substring(window.location.protocol.length));
    var widgetWidth = (typeof <?php echo $chatOptionsVariable?> != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt.widget_width != 'undefined') ? parseInt(<?php echo $chatOptionsVariable?>.opt.widget_width) : 300;
    var widgetHeight = (typeof <?php echo $chatOptionsVariable?> != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt.widget_height != 'undefined') ? parseInt(<?php echo $chatOptionsVariable?>.opt.widget_height) : 340;
    var widgetHeightUnit = 'px';

    if (this.is_full_height === true) {
        widgetHeight = 100;
        widgetHeightUnit = '%';
    }

    this.initial_iframe_url = "<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurlsite()?>"+this.lang+"/chat/chatwidget<?php $leaveamessage == true ? print '/(leaveamessage)/true' : ''?><?= isset($currentPosition['full_height']) && $currentPosition['full_height'] ?  '/(fullheight)/true' : '/(fullheight)/false' ?><?php $department !== false ? print '/(department)/'.$department : ''?><?php $theme !== false ? print '/(theme)/'.$theme->id : ''?><?php $bot_id !== null ? print '/(bot_id)/'.$bot_id : ''?><?php $operator !== false ? print '/(operator)/'.$operator : ''?><?php $priority !== false ? print '/(priority)/'.$priority : ''?><?php $uarguments !== false ? print '/(ua)/'.$uarguments : '' ?>"+this.getAppendCookieArguments()+'?URLReferer='+locationCurrent+this.parseOptions()+this.parseStorageArguments()+'&dt='+encodeURIComponent(document.title);

    this.iframePreloaded = true;

    this.iframe_html = '<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/before_iframe_container.tpl.php')); ?>'+'<div id="<?php echo $chatCSSPrefix?>_iframe_container" <?= isset($currentPosition['full_height']) && $currentPosition['full_height'] ? 'style="height: calc(100% - 25px);"' : '' ?>><iframe id="<?php echo $chatCSSPrefix?>_iframe" allowTransparency="true" scrolling="no" class="<?php echo $chatCSSPrefix?>-loading" frameborder="0" ' +
        ( this.initial_iframe_url != '' ? ' src="'    + this.initial_iframe_url + '"' : '' ) +
        ' width="'+widgetWidth+'"' +
        ' height="'+widgetHeight+'"' +
        ' style="width: '+widgetWidth+'px;height: '+widgetHeight+widgetHeightUnit+';" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Live Help')?>"></iframe></div>';

    <?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/container.tpl.php')); ?>

    if (!this.cssWasAdded) {
        this.cssWasAdded = true;
        this.addCss(raw_css<?php ($theme !== false && $theme->custom_container_css !== '') ? print '+\''.str_replace(array("\n","\r"), '', $theme->custom_container_css).'\'' : '' ?>);
    };

    var fragment = this.appendHTML(this.iframe_html);

    var parentElement = document.body;

    if (typeof <?php echo $chatOptionsVariable?> != 'undefined' &&
        typeof <?php echo $chatOptionsVariable?>.opt != 'undefined' &&
        typeof <?php echo $chatOptionsVariable?>.opt.widget_parent != 'undefined') {
        if(document.getElementById(<?php echo $chatOptionsVariable?>.opt.widget_parent) != null) {
            parentElement = document.getElementById(<?php echo $chatOptionsVariable?>.opt.widget_parent);
        }
    }

    parentElement.insertBefore(fragment, parentElement.childNodes[0]);

    this.addClass(document.getElementById('<?php echo $chatCSSLayoutOptions['container_id']?>'),'<?php echo $chatCSSPrefix?>-delayed');
    this.iframeCustomUrl = false;
    <?php endif; ?>
},

showStartWindow : function(url_to_open,delayShow) {
		  
	  if (this.isOnline == false && typeof <?php echo $chatOptionsVariable?> != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt.offline_redirect != 'undefined'){
			window.open(<?php echo $chatOptionsVariable?>.opt.offline_redirect,"_blank");
			return;
	  };	
	  this.lhc_need_help_hide();

      // Do not check for new messages
      this.stopCheckNewMessage();

      var locationCurrent = encodeURIComponent(window.location.href.substring(window.location.protocol.length));

    if (this.isProactivePending == 1 && this.invitationUrl != ''){
        url_to_open = this.invitationUrl;
        this.invitationUrl = '';
        var elm = document.getElementById('<?php echo $chatCSSPrefix?>_status_container');
        if (elm !== null) {
            this.removeClass(document.getElementById('<?php echo $chatCSSPrefix?>_status_container'),'<?php echo $chatCSSPrefix?>_invitation-mode');
        }
    };

      if ( url_to_open != undefined ) {
       		this.chatOpenedCallback('internal_invitation');	
            this.initial_iframe_url = url_to_open+this.getAppendCookieArguments()+'?URLReferer='+locationCurrent+this.parseOptions()+this.parseStorageArguments()+'&dt='+encodeURIComponent(document.title);
            this.iframeCustomUrl = true;
      } else {
      		this.chatOpenedCallback(this.isOnline == false ? 'internal_offline' : 'internal');	
            this.initial_iframe_url = "<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurlsite()?>"+this.lang+"/chat/chatwidget<?php $leaveamessage == true ? print '/(leaveamessage)/true' : ''?><?= isset($currentPosition['full_height']) && $currentPosition['full_height'] ?  '/(fullheight)/true' : '/(fullheight)/false' ?><?php $department !== false ? print '/(department)/'.$department : ''?><?php $bot_id !== null ? print '/(bot_id)/'.$bot_id : ''?><?php $theme !== false ? print '/(theme)/'.$theme->id : ''?><?php $operator !== false ? print '/(operator)/'.$operator : ''?><?php $priority !== false ? print '/(priority)/'.$priority : ''?><?php $uarguments !== false ? print '/(ua)/'.$uarguments : '' ?>"+this.getAppendCookieArguments()+'?URLReferer='+locationCurrent+this.parseOptions()+this.parseStorageArguments()+'&dt='+encodeURIComponent(document.title);
      };
       
      this.addClass(document.body,'<?php echo $chatCSSPrefix?>-opened');
      
      lh_inst.surveyShown = false;                             
      lh_inst.timeoutStatusWidgetOpen = 1;
      
      var widgetWidth = (typeof <?php echo $chatOptionsVariable?> != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt.widget_width != 'undefined') ? parseInt(<?php echo $chatOptionsVariable?>.opt.widget_width) : 300;
	  var widgetHeight = (typeof <?php echo $chatOptionsVariable?> != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt.widget_height != 'undefined') ? parseInt(<?php echo $chatOptionsVariable?>.opt.widget_height) : 340;
	  var widgetHeightUnit = 'px';

      if(this.is_full_height === true) {
		widgetHeight = 100;
		widgetHeightUnit = '%';
	  }

      <?php
        $iconsStatuses = array(
            'online_image_url' => ($theme !== false && $theme->online_image_url !== false && strpos($theme->online_image_url, 'http') !== false),
            'offline_image_url' => ($theme !== false && $theme->offline_image_url !== false && strpos($theme->offline_image_url, 'http') !== false)
        );
      ?>
      var needLoading = true;

      var iframe_html_source = this.iframe_html;

       this.iframe_html = '<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/before_iframe_container.tpl.php')); ?>'+'<div id="<?php echo $chatCSSPrefix?>_iframe_container" <?= isset($currentPosition['full_height']) && $currentPosition['full_height'] ? 'style="height: calc(100% - 25px);"' : '' ?>><iframe id="<?php echo $chatCSSPrefix?>_iframe" allow="microphone" allowTransparency="true" scrolling="no" class="<?php echo $chatCSSPrefix?>-loading" frameborder="0" ' +
                   ( this.initial_iframe_url != '' ? ' src="'    + this.initial_iframe_url + '"' : '' ) +
                   ' width="'+widgetWidth+'"' +
                   ' height="'+widgetHeight+'"' +
                   ' style="width: '+widgetWidth+'px;height: '+widgetHeight+widgetHeightUnit+';" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Live Help')?>"></iframe></div>';

      <?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/container.tpl.php')); ?>

        if (iframe_html_source != this.iframe_html) {
            this.removeById('<?php echo $chatCSSLayoutOptions['container_id']?>');
        } else {
            needLoading = false;
        }

      if (!this.cssWasAdded) {
      	this.cssWasAdded = true;
      	this.addCss(raw_css<?php ($theme !== false && $theme->custom_container_css !== '') ? print '+\''.str_replace(array("\n","\r"), '', $theme->custom_container_css).'\'' : '' ?>);
	  };

      if (needLoading === true) {

        this.iframePreloaded = false;

        var fragment = this.appendHTML(this.iframe_html);

        var parentElement = document.body;

        if (typeof <?php echo $chatOptionsVariable?> != 'undefined' &&
        typeof <?php echo $chatOptionsVariable?>.opt != 'undefined' &&
        typeof <?php echo $chatOptionsVariable?>.opt.widget_parent != 'undefined') {
            if(document.getElementById(<?php echo $chatOptionsVariable?>.opt.widget_parent) != null) {
                parentElement = document.getElementById(<?php echo $chatOptionsVariable?>.opt.widget_parent);
            }
        }
        parentElement.insertBefore(fragment, parentElement.childNodes[0]);

        this.addClass(document.getElementById('<?php echo $chatCSSLayoutOptions['container_id']?>'),'<?php echo $chatCSSPrefix?>-delayed');

    } else {
        lh_inst.removeClass(document.getElementById('<?php echo $chatCSSLayoutOptions['container_id']?>'),'<?php echo $chatCSSPrefix?>-delayed');
    }

    var lhc_obj = this;

      <?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/functions/part/close_handler.tpl.php')); ?>		
      
      document.getElementById('<?php echo $chatCSSPrefix?>_min').onclick = function() { lhc_obj.min(); return false; };

      <?php if ($theme === false || $theme->modern_look == 1) : ?>
      document.getElementById('<?php echo $chatCSSPrefix?>_status-icon-restore').onclick = function() { lhc_obj.min(); return false; };
      <?php endif ?>

      <?php if (erLhcoreClassModelChatConfig::fetch('disable_popup_restore')->current_value == 0 && ($theme === false || $theme->hide_popup == 0)) : ?>
      document.getElementById('<?php echo $chatCSSPrefix?>_remote_window').onclick = function() { lhc_obj.openRemoteWindow(); return false; };
	  <?php endif; ?>
	  
	  var domContainer = document.getElementById('<?php echo $chatCSSLayoutOptions['container_id']?>');
	  var domIframe = '<?php echo $chatCSSPrefix?>_iframe';
	  var domContainerId = '<?php echo $chatCSSLayoutOptions['container_id']?>';
	  <?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/drag_drop_logic.tpl.php')); ?>		  

	  if (this.cookieData.m) {
        if (this.cookieData.hash) {
            this.min(true);
        }
      };
	  
	  if (typeof delayShow === 'undefined') {
	  		this.toggleStatusWidget(true);
	  }
	  
	  // If proactive invitation is shown. Check for status changes and hide popup if operator goes offline
	  this.checkStatusChat();	  
},