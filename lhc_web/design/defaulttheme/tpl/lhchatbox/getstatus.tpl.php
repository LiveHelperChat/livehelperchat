<?php

$positionArgument = array (
		'bottom_left' => array (
				'radius' => 'right',
				'position' => 'bottom:0;left:0;',
				'position_body' => 'bottom:0;left:0;',
				'shadow' => '2px -2px 5px',
				'moz_radius' => 'topright',
				'widget_hover' => '',
				'posv' => 'b',
				'pos' => 'l',
				'padding_text' => '10px 10px 10px 35px',
				'chrome_radius' => 'top-right',
				'border_widget' => 'border:1px solid #e3e3e3;border-left:0;border-bottom:0;',
				'background_position' => '0',
				'widget_radius' => '-webkit-border-top-right-radius: 20px;-moz-border-radius-topright: 20px;border-top-right-radius: 20px;'
		),
		'bottom_right' => array (
				'pos' => 'r',
				'posv' => 'b',
				'radius' => 'left',
				'position' => 'bottom:0;right:0;',
				'position_body' => 'bottom:0;right:0;',
				'shadow' => '-2px -2px 5px',
				'moz_radius' => 'topleft',
				'widget_hover' => '',
				'padding_text' => '10px 10px 10px 35px',
				'border_widget' => 'border:1px solid #e3e3e3;border-right:0;border-bottom:0;',
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
				'shadow' => '0px 0px 10px',
				'widget_hover' => 'right:0;transition: 1s;',
				'moz_radius' => 'topleft',
				'padding_text' => '10px 10px 10px 35px',
				'border_widget' => 'border:1px solid #e3e3e3;border-right:0;',
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
				'shadow' => '0px 0px 10px',
				'padding_text' => '10px 35px 10px 9px',
				'widget_hover' => 'left:0;transition: 1s;',
				'moz_radius' => 'topright',
				'border_widget' => 'border:1px solid #e3e3e3;border-left:0;',
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
    
    min : function() {
		var dm = document.getElementById('lhc_container_chatbox');	
		if (!dm.attrIsMin || dm.attrIsMin == false) {
			dm.attrHeight = dm.style.height;
			dm.attrIsMin = true;
			this.addClass(dm,'lhc-no-transition');
			this.addClass(dm,'lhc-min');			
			<?php if ($currentPosition['posv'] == 'b') : ?>			
			if(dm.style.bottom!='' && dm.attrHeight!='')dm.style.bottom = (parseInt(dm.style.bottom)+parseInt(dm.attrHeight)-35)+'px';
			<?php endif; ?>			
			this.addCookieAttribute('m',1);
			this.storePos(dm);
		} else {	
			dm.attrIsMin = false;
			<?php if ($currentPosition['posv'] == 'b') : ?>
			if(dm.style.bottom!='')dm.style.bottom = (parseInt(dm.style.bottom)-parseInt(document.getElementById('lhcchatbox_iframe').style.height)+9)+'px';	
			<?php endif;?>		
			this.removeCookieAttr('m');
			this.removeClass(dm,'lhc-min');
			var inst = this;
			setTimeout(function(){
				inst.removeClass(dm,'lhc-no-transition');
			},500);
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
		    var nickOption = (typeof LHCChatboxOptions.nick !== 'undefined') ?  '&nick='+encodeURIComponent(LHCChatboxOptions.nick) : (this.cookieData.nick ? '&nick='+encodeURIComponent(this.cookieData.nick) : '');
		    var disableOption = (typeof LHCChatboxOptions.disable_nick_change !== 'undefined') ?  '&dnc=true' : '';
		    return nickOption+disableOption;
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

   		  this.initial_iframe_url = "<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chatbox/chatwidget')?>/(chat_height)/<?php echo $heightchatcontent;?>/(identifier)/"+LHCChatboxOptions.identifier+'/(hashchatbox)/'+LHCChatboxOptions.hashchatbox+this.getAppendCookieArguments()+'?URLReferer='+encodeURIComponent(document.location)+this.getAppendRequestArguments();

   		  this.addCookieAttribute('is_opened',1);

   		  this.iframe_html = '<iframe id="lhcchatbox_iframe" allowTransparency="true" scrolling="no" class="lhc-loading" frameborder="0" ' +
                       ( this.initial_iframe_url != '' ? ' src="'    + this.initial_iframe_url + '"' : '' ) +
                       ' width="<?php echo $widthwidget?>"' +
                       ' height="<?php echo $heightwidget?>"' +
                       ' style="width: <?php echo $widthwidget?>px; height: <?php echo $heightwidget?>px;"></iframe>';

          this.iframe_html = '<div id="lhc_container_chatbox">' +
                              '<div id="lhc_chatbox_header"><span id="lhc_chatbox_title"><a title="Powered by Live Helper Chat" href="http://livehelperchat.com" target="_blank"><img src="<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::design('images/icons/emotion_amazing_16x16.png');?>" alt="Live Helper Chat" /></a></span><?php if ($show_content === false) : ?><a href="#" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>" id="lhc_chatbox_close"><img src="<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::design('images/icons/cancel.png');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>" /></a><?php endif;?><?php if ($disable_min === false) : ?><a href="#" id="lhc_chatbox_min" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Minimize/Restore')?>"><img src="<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::design('images/icons/min.png');?>"></a><?php endif;?></div>' +
                              this.iframe_html + '</div>';

          raw_css = ".lhc-no-transition{ -webkit-transition: none !important; -moz-transition: none !important;-o-transition: none !important;-ms-transition: none !important;transition: none !important;}\n.lhc-min{height:35px !important}\n#lhc_container_chatbox * {direction:<?php (erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language') == 'ltr' || erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language') == '') ? print 'ltr;text-align:left;' : print 'rtl;text-align:right;'; ?>;font-family:arial\;font-size:12px\;line-height:100%\;box-sizing: content-box\;-moz-box-sizing:content-box;padding:0;margin:0;}\n#lhc_container_chatbox img {border:0;}\n#lhc_chatbox_title{float:left;}\n#lhc_chatbox_header{position:relative;z-index:9990;height:15px;overflow:hidden;-webkit-border-<?php echo $currentPosition['chrome_radius']?>-radius: 10px;-moz-border-radius-<?php echo $currentPosition['moz_radius']?>: 10px;border-<?php echo $currentPosition['chrome_radius']?>-radius: 10px;background-color:#FFF;text-align:right;clear:both;border-bottom:1px solid #CCC;padding:5px;}\n#lhc_chatbox_close,#lhc_chatbox_min{padding:2px;float:right;}\n#lhc_chatbox_close:hover,#lhc_chatbox_min:hover{background:#e5e5e5;}\n#lhc_container_chatbox {height:<?php echo $heightwidget?>px;overflow: hidden;transition-property: height;transition-duration: 0.4s;-webkit-transition: height 0.4s ease-in-out;transition: height 0.4s;background-color:#FFF\;\nz-index:9990;\n position: fixed;<?php echo $currentPosition['position_body']?>;-webkit-box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);-moz-box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);border:1px solid #CCC;-webkit-border-radius: 10px;-moz-border-radius: 10px;border-radius: 10px;-moz-user-select:none; -khtml-user-drag:element;cursor: move;cursor: -moz-grab;cursor: -webkit-grab; }\n#lhc_container_chatbox iframe{transition-property: height;transition-duration: 0.4s;-webkit-transition: height 0.4s ease-in-out;transition: height 0.4s;}\n#lhc_container_chatbox iframe.lhc-loading{\nbackground: #FFF url(<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::design('images/general/loading.gif');?>) no-repeat center center; }";

          if (!this.cssWasAdded) {
          		this.cssWasAdded = true;
          		this.addCss(raw_css);
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
		  
		  if (this.cookieData.m) {this.min();};	
		  
   },

   showStatusWidget : function() {
   		<?php if ($show_content === false) : ?>
       var statusTEXT = '<a id="chatbox-icon" class="status-icon" href="#" >'+LHCChatboxOptions.status_text+'</a>';
       var raw_css = "#lhc_chatbox_container * {direction:<?php (erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language') == 'ltr' || erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language') == '') ? print 'ltr;text-align:left;' : print 'rtl;text-align:right;'; ?>;font-family:arial;font-size:12px;line-height:100%;box-sizing: content-box;zoom:1;margin:0;padding:0}\n#lhc_chatbox_container .status-icon{text-decoration:none;font-size:12px;font-weight:bold;color:#000;display:block;padding:<?php echo $currentPosition['padding_text']?>;background:url('<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::design('images/icons/emotion_amazing.png');?>') no-repeat <?php echo $currentPosition['background_position']?> center}\n#lhc_chatbox_container:hover{<?php echo $currentPosition['widget_hover']?>}\n#lhc_chatbox_container{box-sizing: content-box;<?php echo $currentPosition['widget_radius']?>-webkit-box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);-moz-box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);<?php echo $currentPosition['border_widget']?>;padding:5px 0px 3px 5px;width:190px;font-family:arial;font-size:12px;line-height:100%;transition: 1s;position:fixed;<?php echo $currentPosition['position']?>;background-color:#f6f6f6;z-index:9989;}\n";
       this.addCss(raw_css);
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
    	if (sessionStorage) {
    		sessionStorage.setItem('lhc_chb',this.JSON.stringify(this.cookieData));
    	}
   },

   initSessionStorage : function(){
    	if (sessionStorage && sessionStorage.getItem('lhc_chb')) {
    		this.cookieData = this.JSON.parse(sessionStorage.getItem('lhc_chb'));
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
    		iframeContainer.style.height = (parseInt(height)+26)+'px';
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
	lhc_Chatbox.showVotingForm();
}