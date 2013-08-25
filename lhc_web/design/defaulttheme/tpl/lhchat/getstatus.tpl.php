<?php

$positionArgument = array (
		'bottom_left' => array (
				'radius' => 'right',
				'position' => 'bottom:0;left:0;',
				'position_body' => 'bottom:0;left:0;',
				'shadow' => '2px -2px 5px',
				'moz_radius' => 'topright',
				'widget_hover' => '',
				'padding_text' => '9px 10px 9px 35px',
				'chrome_radius' => 'top-right',
				'border_widget' => 'border:1px solid #e3e3e3;border-left:0;border-bottom:0;',
				'background_position' => '0',
				'widget_radius' => '-webkit-border-top-right-radius: 20px;-moz-border-radius-topright: 20px;border-top-right-radius: 20px;'
		),
		'bottom_right' => array (
				'radius' => 'left',
				'position' => 'bottom:0;right:0;',
				'position_body' => 'bottom:0;right:0;',
				'shadow' => '-2px -2px 5px',
				'moz_radius' => 'topleft',
				'widget_hover' => '',
				'padding_text' => '9px 10px 9px 35px',
				'background_position' => 'left',
				'chrome_radius' => 'top-left',
				'border_widget' => 'border:1px solid #e3e3e3;border-right:0;border-bottom:0;',
				'widget_radius' => '-webkit-border-top-left-radius: 20px;-moz-border-radius-topleft: 20px;border-top-left-radius: 20px;'
		),
		'middle_right' => array (
				'radius' => 'left',
				'position' => "top:{$top_pos}{$units};right:-155px;",
				'position_body' => "top:{$top_pos}{$units};right:0px;",
				'shadow' => '0px 0px 10px',
				'border_widget' => 'border:1px solid #e3e3e3;border-right:0;',
				'widget_hover' => 'right:0;transition: 1s;',
				'moz_radius' => 'topleft',
				'padding_text' => '9px 10px 9px 35px',
				'background_position' => '0',
				'chrome_radius' => 'top-left',
				'widget_radius' => '-webkit-border-top-left-radius: 20px;-moz-border-radius-topleft: 20px;border-top-left-radius: 20px;      -webkit-border-bottom-left-radius: 20px;-moz-border-radius-bottomleft: 20px;border-bottom-left-radius: 20px;'
		),
		'middle_left' => array (
				'radius' => 'left',
				'position' => "top:{$top_pos}{$units};left:-155px;",
				'position_body' => "top:{$top_pos}{$units};left:0px;",
				'shadow' => '0px 0px 10px',
				'border_widget' => 'border:1px solid #e3e3e3;border-left:0;',
				'padding_text' => '9px 35px 9px 9px',
				'widget_hover' => 'left:0;transition: 1s;',
				'moz_radius' => 'topright',
				'background_position' => '95%',
				'chrome_radius' => 'top-right',
				'widget_radius' => '-webkit-border-top-right-radius: 20px;-moz-border-radius-topright: 20px;border-top-right-radius: 20px;      -webkit-border-bottom-right-radius: 20px;-moz-border-radius-bottomright: 20px;border-bottom-right-radius: 20px;'
		)
);

if (key_exists($position, $positionArgument)){
	$currentPosition = $positionArgument[$position];
} else {
	$currentPosition = $positionArgument['bottom_right'];
}

$isOnlineHelp = erLhcoreClassChat::isOnline($department);

// Perhaps user do not want to show live help when it's offline
if ( !($isOnlineHelp == false && $hide_offline == 'true') ) : ?>
var lh_inst  = {

    urlopen : "<?php echo erLhcoreClassSystem::instance()->baseHTTP?><?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/startchat')?><?php $leaveamessage == true ? print '/(leaveamessage)/true' : ''?><?php $department !== false ? print '/(department)/'.$department : ''?><?php $priority !== false ? print '/(priority)/'.$priority : ''?>",

    windowname : "startchatwindow",

    addCss : function(css_content) {
        var head = document.getElementsByTagName('head')[0];
        var style = document.createElement('style');
        style.type = 'text/css';

        if(style.styleSheet) {
          style.styleSheet.cssText = css_content;
        } else {
          rules = document.createTextNode(css_content);
          style.appendChild(rules);
        };

        head.appendChild(style);
    },

    appendHTML : function (htmlStr) {
        var frag = document.createDocumentFragment(),
            temp = document.createElement('div');
        temp.innerHTML = htmlStr;
        while (temp.firstChild) {
            frag.appendChild(temp.firstChild);
        };
        return frag;
    },

    removeById : function(EId)
    {
        return(EObj=document.getElementById(EId))?EObj.parentNode.removeChild(EObj):false;
    },

    hide : function() {
        var th = document.getElementsByTagName('head')[0];
        var s = document.createElement('script');
        s.setAttribute('type','text/javascript');
        s.setAttribute('src','<?php echo erLhcoreClassSystem::instance()->baseHTTP?><?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/chatwidgetclosed')?>');
        th.appendChild(s);
        this.removeById('lhc_container');
        <?php if ($check_operator_messages == 'true') : ?>
        this.startNewMessageCheck();
        <?php endif; ?>
    },

    openRemoteWindow : function() {
        this.removeById('lhc_container');
        var popupHeight = (typeof LHCChatOptions != 'undefined' && typeof LHCChatOptions.opt != 'undefined' && typeof LHCChatOptions.opt.popup_height != 'undefined') ? parseInt(LHCChatOptions.opt.popup_height) : 520;
        var popupWidth = (typeof LHCChatOptions != 'undefined' && typeof LHCChatOptions.opt != 'undefined' && typeof LHCChatOptions.opt.popup_width != 'undefined') ? parseInt(LHCChatOptions.opt.popup_width) : 500;
        window.open(this.urlopen+'?URLReferer='+escape(document.location)+this.parseOptions(),this.windowname,"menubar=1,resizable=1,width="+popupWidth+",height="+popupHeight);
    },

    parseOptions : function() {
		argumentsQuery = new Array();

		if (typeof LHCChatOptions != 'undefined') {
	    	if (typeof LHCChatOptions.attr != 'undefined') {
	    		if (LHCChatOptions.attr.length > 0){
					for (var index in LHCChatOptions.attr) {
						if (typeof LHCChatOptions.attr[index] != 'undefined' && typeof LHCChatOptions.attr[index].type != 'undefined') {
							argumentsQuery.push('name[]='+encodeURIComponent(LHCChatOptions.attr[index].name)+'&value[]='+encodeURIComponent(LHCChatOptions.attr[index].value)+'&type[]='+encodeURIComponent(LHCChatOptions.attr[index].type)+'&size[]='+encodeURIComponent(LHCChatOptions.attr[index].size));
						};
					};
	    		};
	    	};

	    	if (typeof LHCChatOptions.attr_prefill != 'undefined') {
	    		if (LHCChatOptions.attr_prefill.length > 0){
					for (var index in LHCChatOptions.attr_prefill) {
						if (typeof LHCChatOptions.attr_prefill[index] != 'undefined' && typeof LHCChatOptions.attr_prefill[index].name != 'undefined') {
							argumentsQuery.push('prefill['+LHCChatOptions.attr_prefill[index].name+']='+encodeURIComponent(LHCChatOptions.attr_prefill[index].value));
						};
					};
	    		};
	    	};

	    	if (argumentsQuery.length > 0) {
	    		return '&'+argumentsQuery.join('&');
	    	};
    	};

    	return '';
    },

    showStartWindow : function(url_to_open) {

          this.removeById('lhc_container');

          if ( url_to_open != undefined ) {
                this.initial_iframe_url = url_to_open+'?URLReferer='+escape(document.location)+this.parseOptions();
          } else {
                this.initial_iframe_url = "<?php echo erLhcoreClassSystem::instance()->baseHTTP?><?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/chatwidget')?><?php $leaveamessage == true ? print '/(leaveamessage)/true' : ''?><?php $department !== false ? print '/(department)/'.$department : ''?><?php $priority !== false ? print '/(priority)/'.$priority : ''?>"+'?URLReferer='+escape(document.location)+this.parseOptions();
          };

          var widgetWidth = (typeof LHCChatOptions != 'undefined' && typeof LHCChatOptions.opt != 'undefined' && typeof LHCChatOptions.opt.widget_width != 'undefined') ? parseInt(LHCChatOptions.opt.widget_width) : 300;
		  var widgetHeight = (typeof LHCChatOptions != 'undefined' && typeof LHCChatOptions.opt != 'undefined' && typeof LHCChatOptions.opt.widget_height != 'undefined') ? parseInt(LHCChatOptions.opt.widget_height) : 340;

          this.iframe_html = '<iframe id="fdbk_iframe" allowTransparency="true" scrolling="no" class="loading" frameborder="0" ' +
                       ( this.initial_iframe_url != '' ? ' src="'    + this.initial_iframe_url + '"' : '' ) +
                       ' width="'+widgetWidth+'"' +
                       ' height="'+widgetHeight+'"' +
                       ' style="width: '+widgetWidth+'px; height: '+widgetHeight+'px;"></iframe>';

          <?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/container.tpl.php')); ?>

          this.addCss(raw_css);

          var fragment = this.appendHTML(this.iframe_html);

          document.body.insertBefore(fragment, document.body.childNodes[0]);

          var lhc_obj = this;
          document.getElementById('lhc_close').onclick = function() { lhc_obj.hide(); return false; };
          document.getElementById('lhc_remote_window').onclick = function() { lhc_obj.openRemoteWindow(); return false; };

          // Do not check for new messages
          this.stopCheckNewMessage();
    },

    lh_openchatWindow : function() {
        <?php if ($click == 'internal') : ?>
        this.showStartWindow();
        <?php else : ?>
        var popupHeight = (typeof LHCChatOptions != 'undefined' && typeof LHCChatOptions.opt != 'undefined' && typeof LHCChatOptions.opt.popup_height != 'undefined') ? parseInt(LHCChatOptions.opt.popup_height) : 520;
        var popupWidth = (typeof LHCChatOptions != 'undefined' && typeof LHCChatOptions.opt != 'undefined' && typeof LHCChatOptions.opt.popup_width != 'undefined') ? parseInt(LHCChatOptions.opt.popup_width) : 500;
        window.open(this.urlopen+'?URLReferer='+escape(document.location)+this.parseOptions(),this.windowname,"menubar=1,resizable=1,width="+popupWidth+",height="+popupHeight);
        <?php endif; ?>
        return false;
    },

    showStatusWidget : function() {

        var statusTEXT = '<a id="<?php ($isOnlineHelp == true) ? print 'online-icon' : print 'offline-icon' ?>" class="status-icon" href="#" onclick="return lh_inst.lh_openchatWindow()" ><?php if ($isOnlineHelp == true) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus',"Live help is online...")?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus',"Live help is offline...")?><?php endif;?></a>';

        var raw_css = "#lhc_status_container * {direction:<?php (erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language') == 'ltr' || erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language') == '') ? print 'ltr;text-align:left;' : print 'rtl;text-align:right;'; ?>;font-family:arial;font-size:12px;box-sizing: content-box;zoom:1;margin:0;padding:0}\n#lhc_status_container .status-icon{text-decoration:none;font-size:12px;font-weight:bold;color:#000;display:block;padding:<?php echo $currentPosition['padding_text']?>;background:url('<?php echo erLhcoreClassSystem::instance()->baseHTTP?><?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::design('images/icons/user_green_chat.png');?>') no-repeat <?php echo $currentPosition['background_position']?> center}\n#lhc_status_container:hover{<?php echo $currentPosition['widget_hover']?>}\n#lhc_status_container #offline-icon{background-image:url('<?php echo erLhcoreClassSystem::instance()->baseHTTP?><?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::design('images/icons/user_gray_chat.png');?>')}\n#lhc_status_container{<?php echo $currentPosition['widget_radius']?>-webkit-box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);<?php echo $currentPosition['border_widget']?>;-moz-box-shadow:<?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);padding:5px 0px 0px 5px;width:190px;font-family:arial;font-size:12px;transition: 1s;position:fixed;<?php echo $currentPosition['position']?>;background-color:#f6f6f6;z-index:9998;}\n";
        this.addCss(raw_css);

        var htmlStatus = '<div id="lhc_status_container">'+statusTEXT+'</div>';

        var fragment = this.appendHTML(htmlStatus);

        document.body.insertBefore(fragment, document.body.childNodes[0]);
    },

    timeoutInstance : null,

    stopCheckNewMessage : function() {
        clearTimeout(this.timeoutInstance);
    },

    startNewMessageCheck : function() {
        this.timeoutInstance = setTimeout(function() {
            lh_inst.removeById('lhc_operator_message');
            var th = document.getElementsByTagName('head')[0];
            var s = document.createElement('script');
            s.setAttribute('id','lhc_operator_message');
            s.setAttribute('type','text/javascript');
            s.setAttribute('src','<?php echo erLhcoreClassSystem::instance()->baseHTTP?><?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/chatcheckoperatormessage')?><?php $priority !== false ? print '/(priority)/'.$priority : ''?>?l='+escape(document.location));
            th.appendChild(s);
            lh_inst.startNewMessageCheck();
        }, <?php echo (int)(erConfigClassLhConfig::getInstance()->getSetting('chat','check_for_operator_msg')*1000) ?> );
    },

    handleMessage : function(e) {
    	var action = e.data.split(':')[0];
    	if (action == 'lhc_sizing_chat') {
    		var height = e.data.split(':')[1];
    		var elementObject = document.getElementById('fdbk_iframe');
    		var iframeContainer = document.getElementById('lhc_container');
    		elementObject.height = height;
    		elementObject.style.height = height+'px';
    		iframeContainer.className = iframeContainer.className;
    		iframeContainer.style.height = (parseInt(height)+26)+'px';
    	};
    }
};

if ( window.attachEvent ) {
	// IE
	window.attachEvent("onmessage", lh_inst.handleMessage);
};

if ( document.attachEvent ) {
	// IE
	document.attachEvent("onmessage", lh_inst.handleMessage);
};

if ( window.addEventListener ){
	// FF
	window.addEventListener("message", lh_inst.handleMessage, false);
};

<?php if ($check_operator_messages == 'true' && $disable_pro_active == false) : ?>
lh_inst.startNewMessageCheck();
<?php endif; ?>

<?php if ($position == 'original' || $position == '') :
// You can style bottom HTML whatever you want. ?>
document.getElementById('lhc_status_container').innerHTML = '<p><a href="#" onclick="return lh_inst.lh_openchatWindow()"><?php if ($isOnlineHelp == true) : ?><img src="<?php echo erLhcoreClassSystem::instance()->baseHTTP?><?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::design('images/icons/user_green_chat.png');?>" alt="" /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus',"Live help is online...")?><?php else : ?><img src="<?php echo erLhcoreClassSystem::instance()->baseHTTP?><?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::design('images/icons/user_gray_chat.png');?>" alt="" /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus',"Live help is offline...")?><?php endif;?></a></p>';
<?php elseif (in_array($position, array('bottom_right','bottom_left','middle_right','middle_left'))) : ?>
lh_inst.showStatusWidget();
<?php endif;

// User has pending chat
if (($hashSession = CSCacheAPC::getMem()->getSession('chat_hash_widget')) !== false) : ?>
   lh_inst.stopCheckNewMessage();
   lh_inst.showStartWindow();
<?php elseif (isset($visitor) && is_object($visitor) && $disable_pro_active == false && $visitor->has_message_from_operator == true && (erLhcoreClassModelChatConfig::fetch('pro_active_limitation')->current_value == -1 || erLhcoreClassChat::getPendingChatsCountPublic() <= erLhcoreClassModelChatConfig::fetch('pro_active_limitation')->current_value )) : ?>
   lh_inst.stopCheckNewMessage();
   lh_inst.showStartWindow('<?php echo erLhcoreClassSystem::instance()->baseHTTP?><?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/readoperatormessage')?><?php $priority !== false ? print '/(priority)/'.$priority : ''?>');
<?php endif;

endif; // hide if offline

exit; ?>