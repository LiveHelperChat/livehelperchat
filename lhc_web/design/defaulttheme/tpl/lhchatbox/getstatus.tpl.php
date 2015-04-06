<?php

$positionArgument = array (
		'bottom_left' => array (
				'radius' => 'right',
				'position' => 'bottom:0;left:0;',
				'position_body' => 'bottom:0;left:0;',
				'shadow' => '1px -1px 5px',
				'moz_radius' => 'topright',
				'widget_hover' => '',
				'posv' => 'b',
				'pos' => 'l',
				'padding_text' => '10px 10px 10px 35px',
				'chrome_radius' => 'top-right',
				'border_widget' => 'border:1px solid #'.($theme !== false ? $theme->bor_bcolor : 'e3e3e3').';border-left:0;border-bottom:0;',
				'background_position' => '0',
				'widget_radius' => '-webkit-border-top-right-radius: 20px;-moz-border-radius-topright: 20px;border-top-right-radius: 20px;'
		),
		'bottom_right' => array (
				'pos' => 'r',
				'posv' => 'b',
				'radius' => 'left',
				'position' => 'bottom:0;right:0;',
				'position_body' => 'bottom:0;right:0;',
				'shadow' => '1px -1px 5px',
				'moz_radius' => 'topleft',
				'widget_hover' => '',
				'padding_text' => '10px 10px 10px 35px',
				'border_widget' => 'border:1px solid #'.($theme !== false ? $theme->bor_bcolor : 'e3e3e3').';border-right:0;border-bottom:0;',
				'background_position' => 'left',
				'chrome_radius' => 'top-left',
				'widget_radius' => '-webkit-border-top-left-radius: 20px;-moz-border-radius-topleft: 20px;border-top-left-radius: 20px;'
		),
		'middle_right' => array (
				'pos' => 'r',
				'posv' => 't',
				'radius' => 'left',
				'position' => "top:{$top_pos}{$units};right:-155px;",
				'position_body' => "top:{$top_pos}{$units};right:0px;",
				'shadow' => '1px -1px 5px',
				'widget_hover' => 'right:0;transition: 1s;',
				'moz_radius' => 'topleft',
				'padding_text' => '10px 10px 10px 35px',
				'border_widget' => 'border:1px solid #'.($theme !== false ? $theme->bor_bcolor : 'e3e3e3').';border-right:0;',
				'background_position' => '0',
				'chrome_radius' => 'top-left',
				'widget_radius' => '-webkit-border-top-left-radius: 20px;-moz-border-radius-topleft: 20px;border-top-left-radius: 20px;      -webkit-border-bottom-left-radius: 20px;-moz-border-radius-bottomleft: 20px;border-bottom-left-radius: 20px;'
		),
		'middle_left' => array (
				'posv' => 't',
				'pos' => 'l',
				'radius' => 'left',
				'position' => "top:{$top_pos}{$units};left:-155px;",
				'position_body' => "top:{$top_pos}{$units};left:0px;",
				'shadow' => '1px -1px 5px',
				'padding_text' => '10px 35px 10px 9px',
				'widget_hover' => 'left:0;transition: 1s;',
				'moz_radius' => 'topright',
				'border_widget' => 'border:1px solid #'.($theme !== false ? $theme->bor_bcolor : 'e3e3e3').';border-left:0;',
				'background_position' => '95%',
				'chrome_radius' => 'top-right',
				'widget_radius' => '-webkit-border-top-right-radius: 20px;-moz-border-radius-topright: 20px;border-top-right-radius: 20px;      -webkit-border-bottom-right-radius: 20px;-moz-border-radius-bottomright: 20px;border-bottom-right-radius: 20px;'
		)
);

if (key_exists($position, $positionArgument)){
	$currentPosition = $positionArgument[$position];
} else {
	$currentPosition = $positionArgument['bottom_left'];
}

?>

<?php include(erLhcoreClassDesign::designtpl('lhchatbox/getstatus/options_variable.tpl.php')); ?>

var lhc_Chatbox = {
	JSON : {
            parse: window.JSON && (window.JSON.parse || window.JSON.decode) || String.prototype.evalJSON && function(str){return String(str).evalJSON();} || $.parseJSON || $.evalJSON,
            stringify:  Object.toJSON || window.JSON && (window.JSON.stringify || window.JSON.encode) || $.toJSON
    },
	cookieData : {},

	hasClass : function(el, name) {
	   return new RegExp('(\\s|^)'+name+'(\\s|$)').test(el.className);
	},
	
	addClass : function(el, name) {
	   if (!this.hasClass(el, name)) { el.className += (el.className ? ' ' : '') +name; }
	},

	removeClass : function(el, name) {
	   if (this.hasClass(el, name)) {
	      el.className=el.className.replace(new RegExp('(\\s|^)'+name+'(\\s|$)'),' ').replace(/^\s+|\s+$/g, '');
	   }
    },
    storePos : function(dm) {
		    var cookiePos = '';
			<?php if ($currentPosition['pos'] == 'r') : ?>
		    	cookiePos += dm.style.right;			    	   	
		    <?php else : ?>
		    	cookiePos += dm.style.left;	
		    <?php endif;?>	    
		    <?php if ($currentPosition['posv'] == 't') : ?>
		    cookiePos += ","+dm.style.top;
		    <?php else : ?>
		    cookiePos += ","+dm.style.bottom;		
		    <?php endif;?>		    
		    this.addCookieAttribute('pos',cookiePos);	
    },
    
	min : function(initial) {
		var dm = document.getElementById('lhc_container_chatbox');					
		if (!dm.attrIsMin || dm.attrIsMin == false) {
			dm.attrHeight = dm.style.height;
			dm.attrIsMin = true;
			this.addClass(dm,'lhc-min');									
			<?php if ($currentPosition['posv'] == 'b') : ?>			
			if(dm.style.bottom!='' && dm.attrHeight!=''){
				dm.style.bottom = (parseInt(dm.style.bottom)+parseInt(dm.attrHeight)-35)+'px';							
			} else {
				if (initial == undefined) {
					dm.style.bottom = (parseInt(dm.style.bottom) + parseInt(document.getElementById('lhc_chatbox_iframe_container').offsetHeight)-10)+'px';
				}			
			}
			<?php endif; ?>			
			this.addCookieAttribute('m',1);
			this.storePos(dm);
			<?php if ($currentPosition['posv'] == 'b' && isset($minimize_action) && $minimize_action == 'br') : ?>
					dm.attrBottomOrigin = dm.style.bottom;
					dm.style.bottom = '';										
					<?php if ($currentPosition['pos'] == 'r') : ?>
					dm.attrRightOrigin = dm.style.right;
					dm.style.right = '0px';	
					<?php else : ?>
					dm.attrLeftOrigin = dm.style.left;
					dm.style.left = '0px';	
					<?php endif;?>													
			<?php endif;?>
		} else {	
			dm.attrIsMin = false;
			<?php if ($currentPosition['posv'] == 'b') : ?>
			if (dm.attrBottomOrigin)	{
				dm.style.bottom = (parseInt(dm.attrBottomOrigin)-parseInt(document.getElementById('lhcchatbox_iframe').style.height)+9)+'px';
				<?php if ($currentPosition['pos'] == 'r') : ?>
				dm.style.right = dm.attrRightOrigin;	
				<?php else : ?>
				dm.style.left = dm.attrLeftOrigin;	
				<?php endif;?>
			} else if (dm.style.bottom!=''){		
				dm.style.bottom = (parseInt(dm.style.bottom)-parseInt(document.getElementById('lhcchatbox_iframe').style.height)+9)+'px';
			}
			<?php endif;?>		
			this.removeCookieAttr('m');
			this.removeClass(dm,'lhc-min');
			var inst = this;		
			this.storePos(dm);
		};
	},
	
	
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
   is_dragging : false,
   offset_data : '',
   appendHTML : function(htmlStr) {
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
        s.setAttribute('src','<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chatbox/chatwidgetclosed')?>');
        th.appendChild(s);
        this.removeById('lhc_container_chatbox');
        this.removeCookieAttr('pos');
        this.removeCookieAttr('is_opened',0);
        this.removeCookieAttr('m');
    },

 	getAppendCookieArguments : function() {
		    var soundOption = this.cookieData.s ? '/(sound)/'+this.cookieData.s : '';
		    return soundOption;
    },

 	getAppendRequestArguments : function() {
		    var nickOption = (typeof <?php echo $chatboxOptionsVariable;?>.nick !== 'undefined') ?  '&nick='+encodeURIComponent(<?php echo $chatboxOptionsVariable;?>.nick) : (this.cookieData.nick ? '&nick='+encodeURIComponent(this.cookieData.nick) : '');
		    var disableOption = (typeof <?php echo $chatboxOptionsVariable;?>.disable_nick_change !== 'undefined') ?  '&dnc=true' : '';
		    var chatboxName = (typeof <?php echo $chatboxOptionsVariable;?>.chatbox_name !== 'undefined') ?  '&chtbx_name='+encodeURIComponent(<?php echo $chatboxOptionsVariable;?>.chatbox_name) : '';
		    return nickOption+disableOption+chatboxName;
    },
    
	addEvent : (function () {
	  if (document.addEventListener) {
	    return function (el, type, fn) {
	      if (el && el.nodeName || el === window) {
	        el.addEventListener(type, fn, false);
	      } else if (el && el.length) {
	        for (var i = 0; i < el.length; i++) {
	          lhc_Chatbox.addEvent(el[i], type, fn);
	        }
	      }
	    };
	  } else {
	    return function (el, type, fn) {
	      if (el && el.nodeName || el === window) {
	        el.attachEvent('on' + type, function () { return fn.call(el, window.event); });
	      } else if (el && el.length) {
	        for (var i = 0; i < el.length; i++) {
	          lhc_Chatbox.addEvent(el[i], type, fn);
	        }
	      }
	    };
	  }
	})(),
	
	showVotingForm : function() {

   		  this.removeById('lhc_container_chatbox');
		  var locationCurrent = encodeURIComponent(window.location.href.substring(window.location.protocol.length));
		  
   		  this.initial_iframe_url = "<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chatbox/chatwidget')?>/(chat_height)/<?php echo $heightchatcontent;?><?php $theme !== false ? print '/(theme)/'.$theme->id : ''?>/(identifier)/"+<?php echo $chatboxOptionsVariable;?>.identifier+'/(hashchatbox)/'+<?php echo $chatboxOptionsVariable;?>.hashchatbox+this.getAppendCookieArguments()+'?URLReferer='+locationCurrent+this.getAppendRequestArguments();

   		  if (window.innerWidth < 1024) {
          		window.open(this.initial_iframe_url,"_blank");
          		return;
          };
   		  
   		  this.addCookieAttribute('is_opened',1);

   		  this.iframe_html = '<div id="lhc_chatbox_iframe_container"><iframe id="lhcchatbox_iframe" allowTransparency="true" scrolling="no" class="lhc-loading" frameborder="0" ' +
                       ( this.initial_iframe_url != '' ? ' src="'    + this.initial_iframe_url + '"' : '' ) +
                       ' width="<?php echo $widthwidget?>"' +
                       ' height="<?php echo $heightwidget?>"' +
                       ' style="width: <?php echo $widthwidget?>px; height: <?php echo $heightwidget?>px;"></iframe></div>';

          this.iframe_html = '<div id="lhc_container_chatbox">' +
                              '<div id="lhc_chatbox_header"><?php include(erLhcoreClassDesign::designtpl('lhchat/widget_brand/chatbox.tpl.php')); ?><?php if ($show_content === false) : ?><a href="#" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>" id="lhc_chatbox_close"><img src="<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php if ($theme !== false && $theme->close_image_url != '') : ?><?php echo $theme->close_image_url;?><?php else : ?><?php echo erLhcoreClassDesign::design('images/icons/cancel.png');?><?php endif;?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>" /></a><?php endif;?><?php if ($disable_min === false) : ?><a href="#" id="lhc_chatbox_min" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Minimize/Restore')?>"></a><?php endif;?></div><div id="lhc_iframe_container">' +
                              this.iframe_html + '</div></div>';

          raw_css = ".lhc-no-transition{ -webkit-transition: none !important; -moz-transition: none !important;-o-transition: none !important;-ms-transition: none !important;transition: none !important;}\n.lhc-min{height:35px !important}\n#lhc_container_chatbox * {direction:<?php (erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language') == 'ltr' || erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language') == '') ? print 'ltr;text-align:left;' : print 'rtl;text-align:right;'; ?>;font-family:arial\;font-size:12px\;line-height:100%\;box-sizing: content-box\;-moz-box-sizing:content-box;padding:0;margin:0;}\n#lhc_container_chatbox img {border:0;}\n#lhc_chatbox_title{float:left;}\n#lhc_chatbox_header{position:relative;z-index:9990;height:<?php ($theme !== false && $theme->header_height > 0) ? print $theme->header_height : print '15' ?>px;overflow:hidden;background-color:#<?php $theme !== false ? print $theme->header_background : print '525252' ?>;text-align:right;clear:both;padding:<?php ($theme !== false && $theme->header_padding > 0) ? print $theme->header_padding : print '5' ?>px;}\n#lhc_chatbox_close,#lhc_chatbox_min{padding:2px;float:right;}.lhc-min #lhc_chatbox_min{background-image:url(<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php if ($theme !== false && $theme->restore_image_url != '') : ?><?php echo $theme->restore_image_url;?><?php else : ?><?php echo erLhcoreClassDesign::design('images/icons/restore.png');?><?php endif;?>)}#lhc_chatbox_min{width:14px;height:14px;background:url(<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php if ($theme !== false && $theme->minimize_image_url != '') : ?><?php echo $theme->minimize_image_url;?><?php else : ?><?php echo erLhcoreClassDesign::design('images/icons/min.png');?><?php endif;?>) no-repeat center center;}\n\n#lhc_chatbox_close:hover,#lhc_chatbox_min:hover{opacity:0.4}\n#lhc_container_chatbox {overflow: hidden;transition-property: height;transition-duration: 0.4s;-webkit-transition: height 0.4s ease-in-out;transition: height 0.4s;background-color:#FFF\;\nz-index:9990;\n position: fixed;<?php echo $currentPosition['position_body']?>;-webkit-box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);-moz-box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);-webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;-moz-user-select:none; -khtml-user-drag:element;cursor: move;cursor: -moz-grab;cursor: -webkit-grab; }\n#lhc_container_chatbox iframe{position:relative;display:block;transition-property: height;transition-duration: 0.4s;-webkit-transition: height 0.4s ease-in-out;transition: height 0.4s;}\n#lhc_container_chatbox iframe.lhc-loading{\nbackground: #FFF url(<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::design('images/general/loading.gif');?>) no-repeat center center; }\n#lhc_container_chatbox #lhc_iframe_container{border:<?php ($theme !== false && $theme->widget_border_width > 0) ? print $theme->widget_border_width : print '1' ?>px solid #<?php $theme !== false ? print $theme->widget_border_color : print 'cccccc' ?>;border-top: 0;border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;overflow: hidden;}\n@media only screen and (max-width : 640px) {#lhc_container_chatbox{margin-bottom:5px;position:relative;right:0 !important;bottom:0 !important;top:0 !important}#lhc_container_chatbox iframe{width:100% !important}}";

          if (!this.cssWasAdded) {
          		this.cssWasAdded = true;
          		this.addCss(raw_css<?php ($theme !== false && $theme->custom_container_css !== '') ? print '+\''.str_replace(array("\n","\r"), '', $theme->custom_container_css).'\'' : '' ?>);
		  };

          var fragment = this.appendHTML(this.iframe_html);
          document.body.insertBefore(fragment, document.body.childNodes[0]);

		  var lhc_obj = this;
          <?php if ($show_content === false) : ?>
          document.getElementById('lhc_chatbox_close').onclick = function() { lhc_obj.hide(); return false; };
          <?php endif;?>
                   
          <?php if ($disable_min === false) : ?>
          document.getElementById('lhc_chatbox_min').onclick = function() { lhc_obj.min(); return false; };         
          <?php endif;?>
                   
          var domContainer = document.getElementById('lhc_container_chatbox');
          var domIframe = 'lhcchatbox_iframe';
          var domContainerId = 'lhc_container_chatbox';
		  <?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/drag_drop_logic.tpl.php')); ?>
		  
		  if (this.cookieData.m) {this.min(true);};
		  
   },

   showStatusWidget : function() {
   		<?php if ($show_content === false) : ?>
       var statusTEXT = '<a id="chatbox-icon" class="status-icon" href="#" >'+<?php echo $chatboxOptionsVariable;?>.status_text+'</a>';
       var raw_css = "#lhc_chatbox_container * {direction:<?php (erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language') == 'ltr' || erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language') == '') ? print 'ltr;text-align:left;' : print 'rtl;text-align:right;'; ?>;font-family:arial;font-size:12px;line-height:100%;box-sizing: content-box;zoom:1;margin:0;padding:0}\n#lhc_chatbox_container .status-icon{text-decoration:none;font-size:12px;font-weight:bold;color:#<?php $theme !== false ? print $theme->text_color : print '000' ?>;display:block;padding:<?php echo $currentPosition['padding_text']?>;background:url('<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::design('images/icons/emotion_amazing.png');?>') no-repeat <?php echo $currentPosition['background_position']?> center}\n#lhc_chatbox_container:hover{<?php echo $currentPosition['widget_hover']?>}\n#lhc_chatbox_container{box-sizing: content-box;<?php echo $currentPosition['widget_radius']?>-webkit-box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);-moz-box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);<?php echo $currentPosition['border_widget']?>;padding:5px 0px 3px 5px;width:190px;font-family:arial;font-size:12px;line-height:100%;transition: 1s;position:fixed;<?php echo $currentPosition['position']?>;background-color:#<?php $theme !== false ? print $theme->onl_bcolor : print 'f6f6f6' ?>;z-index:9989;}\n<?php if ($noresponse == false) : ?>@media only screen and (max-width : 640px) {#lhc_chatbox_container{position:relative;top:0;right:0;bottom:0;left:0;width:auto;border-radius:2px;box-shadow:none;border:1px solid #e3e3e3;margin-bottom:5px;}}\n<?php endif;?>";
       this.addCss(raw_css<?php ($theme !== false && $theme->custom_status_css !== '') ? print '+\''.str_replace(array("\n","\r"), '', $theme->custom_status_css).'\'' : '' ?>);
       var htmlStatus = '<div id="lhc_chatbox_container">'+statusTEXT+'</div>';
       var fragment = this.appendHTML(htmlStatus);
       document.body.insertBefore(fragment, document.body.childNodes[0]);
       var lhc_obj = this;
       document.getElementById('chatbox-icon').onclick = function() { lhc_obj.showVotingForm(); return false; };
       <?php endif;?>
   },

   removeCookieAttr : function(attr){
    	if (this.cookieData[attr]) {
    		delete this.cookieData[attr];
    		this.storeSesCookie();
    	}
   },

   storeSesCookie : function(){
    	if (localStorage) {
    		localStorage.setItem('lhc_chb',this.JSON.stringify(this.cookieData));
    	}
   },

   initSessionStorage : function(){
    	if (localStorage && localStorage.getItem('lhc_chb')) {
    		this.cookieData = this.JSON.parse(localStorage.getItem('lhc_chb'));
    	};
    	<?php if ($show_content === true) : ?>
    	if (!this.cookieData.is_opened) {
    		this.cookieData.is_opened = 1;
    		<?php if ($show_content_min === true) : ?>
    		if (!this.cookieData.m) {
    			this.cookieData.m = 1;
    		}
    		<?php endif;?>
    	}
    	<?php endif;?>
   },

   addCookieAttribute : function(attr, value){
    	if (!this.cookieData[attr] || this.cookieData[attr] != value){
	    	this.cookieData[attr] = value;
	    	this.storeSesCookie();
    	}
   },

   handleMessage : function(e) {
    	var action = e.data.split(':')[0];
    	if (action == 'lhc_sizing_chatbox') {
    		var height = e.data.split(':')[1];
    		var elementObject = document.getElementById('lhcchatbox_iframe');
    		var iframeContainer = document.getElementById('lhc_container_chatbox');
    		elementObject.height = height;
    		elementObject.style.height = height+'px';
    		iframeContainer.className = iframeContainer.className;    
    	} else if (action == 'lhc_ch') {
    		var parts = e.data.split(':');
    		if (parts[1] != '' && parts[2] != '') {
    			lhc_Chatbox.addCookieAttribute(parts[1],parts[2]);
    		}
    	} else if (action == 'lhc_chb') {
    		var parts = e.data.split(':');
    		if (parts[1] != '' && parts[2] != '') {
    			lhc_Chatbox.addCookieAttribute(parts[1],parts[2]);
    		}
    	}
   }
};

lhc_Chatbox.initSessionStorage();
lhc_Chatbox.showStatusWidget();

if ( window.attachEvent ) {
	// IE
	window.attachEvent("onmessage",function(e){lhc_Chatbox.handleMessage(e);});
};

if ( document.attachEvent ) {
	// IE
	document.attachEvent("onmessage",function(e){lhc_Chatbox.handleMessage(e);});
};

if ( window.addEventListener ){
	// FF
	window.addEventListener("message",function(e){lhc_Chatbox.handleMessage(e);}, false);
};

if (lhc_Chatbox.cookieData.is_opened) {
	if (window.innerWidth > 1023) {
		lhc_Chatbox.showVotingForm();
	}
}