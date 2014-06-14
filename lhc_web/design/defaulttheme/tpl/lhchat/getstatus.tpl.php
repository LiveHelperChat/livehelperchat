<?php

$positionArgument = array (
		'bottom_left' => array (
				'radius' => 'right',
				'position' => 'bottom:0;left:0;',
				'posv' => 'b',
				'pos' => 'l',
				'position_body' => 'bottom:0;left:0;',
				'shadow' => '1px -1px 5px',
				'moz_radius' => 'topright',
				'widget_hover' => '',
				'padding_text' => '10px 10px 10px 35px',
				'chrome_radius' => 'top-right',
				'border_widget' => 'border:1px solid #e3e3e3;border-left:0;border-bottom:0;',
				'background_position' => '0',
				'widget_radius' => '-webkit-border-top-right-radius: 20px;-moz-border-radius-topright: 20px;border-top-right-radius: 20px;',
				'nh_hor_pos' => 'margin-left:10px;',
				'nh_tr_pos' => 'left:15px;'
		),
		'bottom_right' => array (
				'pos' => 'r',
				'posv' => 'b',
				'radius' => 'left',
				'position' => 'bottom:0;right:0;',
				'position_body' => 'bottom:0;right:0;',
				'shadow' => '-1px -1px 5px',
				'moz_radius' => 'topleft',
				'widget_hover' => '',
				'padding_text' => '10px 10px 10px 35px',
				'background_position' => 'left',
				'chrome_radius' => 'top-left',
				'border_widget' => 'border:1px solid #e3e3e3;border-right:0;border-bottom:0;',
				'widget_radius' => '-webkit-border-top-left-radius: 20px;-moz-border-radius-topleft: 20px;border-top-left-radius: 20px;',
				'nh_hor_pos' => 'margin-left:-80px;',
				'nh_tr_pos' => 'right:15px;'
		),
		'middle_right' => array (
				'pos' => 'r',
				'posv' => 't',
				'radius' => 'left',
				'position' => "top:{$top_pos}{$units};right:-155px;",
				'position_body' => "top:{$top_pos}{$units};right:0px;",
				'shadow' => '1px -1px 5px',
				'border_widget' => 'border:1px solid #e3e3e3;border-right:0;',
				'widget_hover' => 'right:0;transition: 1s;',
				'moz_radius' => 'topleft',
				'padding_text' => '10px 10px 10px 35px',
				'background_position' => '0',
				'chrome_radius' => 'top-left',
				'widget_radius' => '-webkit-border-top-left-radius: 20px;-moz-border-radius-topleft: 20px;border-top-left-radius: 20px;      -webkit-border-bottom-left-radius: 20px;-moz-border-radius-bottomleft: 20px;border-bottom-left-radius: 20px;',
				'nh_hor_pos' => 'margin-left:-230px;',
				'nh_tr_pos' => 'right:15px;'
		),
		'middle_left' => array (
				'radius' => 'left',
				'position' => "top:{$top_pos}{$units};left:-155px;",
				'position_body' => "top:{$top_pos}{$units};left:0px;",
				'shadow' => '1px -1px 5px',
				'border_widget' => 'border:1px solid #e3e3e3;border-left:0;',
				'padding_text' => '10px 35px 10px 9px',
				'widget_hover' => 'left:0;transition: 1s;',
				'moz_radius' => 'topright',
				'posv' => 't',
				'pos' => 'l',
				'background_position' => '95%',
				'chrome_radius' => 'top-right',
				'widget_radius' => '-webkit-border-top-right-radius: 20px;-moz-border-radius-topright: 20px;border-top-right-radius: 20px;      -webkit-border-bottom-right-radius: 20px;-moz-border-radius-bottomright: 20px;border-bottom-right-radius: 20px;',
				'nh_hor_pos' => 'margin-left:160px;',
				'nh_tr_pos' => ''
		)
);

if (key_exists($position, $positionArgument)){
	$currentPosition = $positionArgument[$position];
} else {
	$currentPosition = $positionArgument['bottom_right'];
}

$trackDomain = erLhcoreClassModelChatConfig::fetch('track_domain')->current_value;
$disableHTML5Storage = (int)erLhcoreClassModelChatConfig::fetch('disable_html5_storage')->current_value;

?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/is_online_help.tpl.php')); ?>

<?php
if ($isOnlineHelp == false && erLhcoreClassModelChatConfig::fetch('pro_active_show_if_offline')->current_value == 0) {
	$disable_pro_active = true;
};

// Perhaps user do not want to show live help when it's offline
if ( !($isOnlineHelp == false && $hide_offline == 'true') ) : ?>

/*! Cookies.js - 0.3.1; Copyright (c) 2013, Scott Hamper; http://www.opensource.org/licenses/MIT */
(function(e){"use strict";var a=function(b,d,c){return 1===arguments.length?a.get(b):a.set(b,d,c)};a._document=document;a._navigator=navigator;a.defaults={path:"/"};a.get=function(b){a._cachedDocumentCookie!==a._document.cookie&&a._renewCache();return a._cache[b]};a.set=function(b,d,c){c=a._getExtendedOptions(c);c.expires=a._getExpiresDate(d===e?-1:c.expires);a._document.cookie=a._generateCookieString(b,d,c);return a};a.expire=function(b,d){return a.set(b,e,d)};a._getExtendedOptions=function(b){return{path:b&&
b.path||a.defaults.path,domain:b&&b.domain||a.defaults.domain,expires:b&&b.expires||a.defaults.expires,secure:b&&b.secure!==e?b.secure:a.defaults.secure}};a._isValidDate=function(b){return"[object Date]"===Object.prototype.toString.call(b)&&!isNaN(b.getTime())};a._getExpiresDate=function(b,d){d=d||new Date;switch(typeof b){case "number":b=new Date(d.getTime()+1E3*b);break;case "string":b=new Date(b)}if(b&&!a._isValidDate(b))throw Error("`expires` parameter cannot be converted to a valid Date instance");
return b};a._generateCookieString=function(b,a,c){b=encodeURIComponent(b);a=(a+"").replace(/[^!#$&-+\--:<-\[\]-~]/g,encodeURIComponent);c=c||{};b=b+"="+a+(c.path?";path="+c.path:"");b+=c.domain?";domain="+c.domain:"";b+=c.expires?";expires="+c.expires.toUTCString():"";return b+=c.secure?";secure":""};a._getCookieObjectFromString=function(b){var d={};b=b?b.split("; "):[];for(var c=0;c<b.length;c++){var f=a._getKeyValuePairFromCookieString(b[c]);d[f.key]===e&&(d[f.key]=f.value)}return d};a._getKeyValuePairFromCookieString=
function(b){var a=b.indexOf("="),a=0>a?b.length:a;return{key:decodeURIComponent(b.substr(0,a)),value:decodeURIComponent(b.substr(a+1))}};a._renewCache=function(){a._cache=a._getCookieObjectFromString(a._document.cookie);a._cachedDocumentCookie=a._document.cookie};a._areEnabled=function(){return a._navigator.cookieEnabled||"1"===a.set("cookies.js",1).get("cookies.js")};a.enabled=a._areEnabled();"function"===typeof define&&define.amd?define(function(){return a}):"undefined"!==typeof exports?("undefined"!==
typeof module&&module.exports&&(exports=module.exports=a),exports.lhc_Cookies=a):window.lhc_Cookies=a})();

lhc_Cookies.defaults = {path:"/",secure: <?php erLhcoreClassModelChatConfig::fetch('use_secure_cookie')->current_value == 1 ? print 'true' : print 'false' ?>};

var lh_inst  = {
   JSON : {
            parse: window.JSON && (window.JSON.parse || window.JSON.decode) || String.prototype.evalJSON && function(str){return String(str).evalJSON();} || $.parseJSON || $.evalJSON,
            stringify:  Object.toJSON || window.JSON && (window.JSON.stringify || window.JSON.encode) || $.toJSON
    },
	offset_data : '',
	is_dragging : false,
    urlopen : "<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/startchat')?><?php $leaveamessage == true ? print '/(leaveamessage)/true' : ''?><?php $theme !== false ? print '/(theme)/'.$theme->id : ''?><?php $department !== false ? print '/(department)/'.$department : ''?><?php $priority !== false ? print '/(priority)/'.$priority : ''?>",

    windowname : "startchatwindow",
	substatus : '',
    cookieData : {},
    cookieDataPers : {},

    getCookieDomain : function(domain) {
    	return '.'+document.location.hostname.replace(/^(?:[a-z0-9\-\.]+\.)??([a-z0-9\-]+)?(\.com|\.net|\.org|\.biz|\.ws|\.in|\.me|\.co\.uk|\.co|\.org\.uk|\.ltd\.uk|\.plc\.uk|\.me\.uk|\.edu|\.mil|\.br\.com|\.cn\.com|\.eu\.com|\.hu\.com|\.no\.com|\.qc\.com|\.sa\.com|\.se\.com|\.se\.net|\.us\.com|\.uy\.com|\.ac|\.co\.ac|\.gv\.ac|\.or\.ac|\.ac\.ac|\.af|\.am|\.as|\.at|\.ac\.at|\.co\.at|\.gv\.at|\.or\.at|\.asn\.au|\.com\.au|\.edu\.au|\.org\.au|\.net\.au|\.id\.au|\.be|\.ac\.be|\.adm\.br|\.adv\.br|\.am\.br|\.arq\.br|\.art\.br|\.bio\.br|\.cng\.br|\.cnt\.br|\.com\.br|\.ecn\.br|\.eng\.br|\.esp\.br|\.etc\.br|\.eti\.br|\.fm\.br|\.fot\.br|\.fst\.br|\.g12\.br|\.gov\.br|\.ind\.br|\.inf\.br|\.jor\.br|\.lel\.br|\.med\.br|\.mil\.br|\.net\.br|\.nom\.br|\.ntr\.br|\.odo\.br|\.org\.br|\.ppg\.br|\.pro\.br|\.psc\.br|\.psi\.br|\.rec\.br|\.slg\.br|\.tmp\.br|\.tur\.br|\.tv\.br|\.vet\.br|\.zlg\.br|\.br|\.ab\.ca|\.bc\.ca|\.mb\.ca|\.nb\.ca|\.nf\.ca|\.ns\.ca|\.nt\.ca|\.on\.ca|\.pe\.ca|\.qc\.ca|\.sk\.ca|\.yk\.ca|\.ca|\.cc|\.ac\.cn|\.com\.cn|\.edu\.cn|\.gov\.cn|\.org\.cn|\.bj\.cn|\.sh\.cn|\.tj\.cn|\.cq\.cn|\.he\.cn|\.nm\.cn|\.ln\.cn|\.jl\.cn|\.hl\.cn|\.js\.cn|\.zj\.cn|\.ah\.cn|\.gd\.cn|\.gx\.cn|\.hi\.cn|\.sc\.cn|\.gz\.cn|\.yn\.cn|\.xz\.cn|\.sn\.cn|\.gs\.cn|\.qh\.cn|\.nx\.cn|\.xj\.cn|\.tw\.cn|\.hk\.cn|\.mo\.cn|\.cn|\.cx|\.cz|\.de|\.dk|\.fo|\.com\.ec|\.tm\.fr|\.com\.fr|\.asso\.fr|\.presse\.fr|\.fr|\.gf|\.gs|\.co\.il|\.net\.il|\.ac\.il|\.k12\.il|\.gov\.il|\.muni\.il|\.ac\.in|\.co\.in|\.org\.in|\.ernet\.in|\.gov\.in|\.net\.in|\.res\.in|\.is|\.it|\.ac\.jp|\.co\.jp|\.go\.jp|\.or\.jp|\.ne\.jp|\.ac\.kr|\.co\.kr|\.go\.kr|\.ne\.kr|\.nm\.kr|\.or\.kr|\.li|\.lt|\.lu|\.asso\.mc|\.tm\.mc|\.com\.mm|\.org\.mm|\.net\.mm|\.edu\.mm|\.gov\.mm|\.ms|\.nl|\.no|\.nu|\.pl|\.ro|\.org\.ro|\.store\.ro|\.tm\.ro|\.firm\.ro|\.www\.ro|\.arts\.ro|\.rec\.ro|\.info\.ro|\.nom\.ro|\.nt\.ro|\.se|\.si|\.com\.sg|\.org\.sg|\.net\.sg|\.gov\.sg|\.sk|\.st|\.tf|\.ac\.th|\.co\.th|\.go\.th|\.mi\.th|\.net\.th|\.or\.th|\.tm|\.to|\.com\.tr|\.edu\.tr|\.gov\.tr|\.k12\.tr|\.net\.tr|\.org\.tr|\.com\.tw|\.org\.tw|\.net\.tw|\.ac\.uk|\.uk\.com|\.uk\.net|\.gb\.com|\.gb\.net|\.vg|\.sh|\.kz|\.ch|\.info|\.ua|\.gov|\.name|\.pro|\.ie|\.hk|\.com\.hk|\.org\.hk|\.net\.hk|\.edu\.hk|\.us|\.tk|\.cd|\.by|\.ad|\.lv|\.eu\.lv|\.bz|\.es|\.jp|\.cl|\.ag|\.mobi|\.eu|\.co\.nz|\.org\.nz|\.net\.nz|\.maori\.nz|\.iwi\.nz|\.io|\.la|\.md|\.sc|\.sg|\.vc|\.tw|\.travel|\.my|\.se|\.tv|\.pt|\.com\.pt|\.edu\.pt|\.asia|\.fi|\.com\.ve|\.net\.ve|\.fi|\.org\.ve|\.web\.ve|\.info\.ve|\.co\.ve|\.tel|\.im|\.gr|\.ru|\.net\.ru|\.org\.ru|\.hr|\.com\.hr)$/, '$1$2');
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
		var dm = document.getElementById('lhc_container');	
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
			if(dm.style.bottom!='')dm.style.bottom = (parseInt(dm.style.bottom)-parseInt(document.getElementById('lhc_iframe').style.height)+9)+'px';	
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
	
    hide : function() {
        var th = document.getElementsByTagName('head')[0];
        var s = document.createElement('script');
        s.setAttribute('type','text/javascript');
        s.setAttribute('src','<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/chatwidgetclosed')?>'+this.getAppendCookieArguments());
        th.appendChild(s);
        this.removeById('lhc_container');
        this.removeCookieAttr('hash');
        this.removeCookieAttr('pos');
        this.removeCookieAttr('m');      
        <?php if ($check_operator_messages == 'true') : ?>
        this.startNewMessageCheck();
        <?php endif; ?>
    },

    getAppendCookieArguments : function() {
		    var hashAppend = this.cookieData.hash ? '/(hash)/'+this.cookieData.hash : '';
		    var vidAppend = this.cookieDataPers.vid ? '/(vid)/'+this.cookieDataPers.vid : '';
		    var hashResume = this.cookieData.hash_resume ? '/(hash_resume)/'+this.cookieData.hash_resume : '';
		    var soundOption = this.cookieData.s ? '/(sound)/'+this.cookieData.s : '';
		    return hashAppend+vidAppend+hashResume+soundOption;
    },

    openRemoteWindow : function() {
        this.removeById('lhc_container');
        var popupHeight = (typeof LHCChatOptions != 'undefined' && typeof LHCChatOptions.opt != 'undefined' && typeof LHCChatOptions.opt.popup_height != 'undefined') ? parseInt(LHCChatOptions.opt.popup_height) : 520;
        var popupWidth = (typeof LHCChatOptions != 'undefined' && typeof LHCChatOptions.opt != 'undefined' && typeof LHCChatOptions.opt.popup_width != 'undefined') ? parseInt(LHCChatOptions.opt.popup_width) : 500;
        var locationCurrent = encodeURIComponent(window.location.href.substring(window.location.protocol.length));        
        window.open(this.urlopen+this.getAppendCookieArguments()+'?URLReferer='+locationCurrent+this.parseOptions()+this.parseStorageArguments(),this.windowname,"scrollbars=yes,menubar=1,resizable=1,width="+popupWidth+",height="+popupHeight);
        this.removeCookieAttr('hash');
    },

    parseOptions : function() {
		argumentsQuery = new Array();

		if (typeof LHCChatOptions != 'undefined') {
	    	if (typeof LHCChatOptions.attr != 'undefined') {
	    		if (LHCChatOptions.attr.length > 0){
					for (var index in LHCChatOptions.attr) {
						if (typeof LHCChatOptions.attr[index] != 'undefined' && typeof LHCChatOptions.attr[index].type != 'undefined') {							
							argumentsQuery.push('name[]='+encodeURIComponent(LHCChatOptions.attr[index].name)+'&value[]='+encodeURIComponent(LHCChatOptions.attr[index].value)+'&type[]='+encodeURIComponent(LHCChatOptions.attr[index].type)+'&size[]='+encodeURIComponent(LHCChatOptions.attr[index].size)+'&req[]='+(typeof LHCChatOptions.attr[index].req != 'undefined' && LHCChatOptions.attr[index].req == true ? 't' : 'f')+'&sh[]='+((typeof LHCChatOptions.attr[index].show != 'undefined' && (LHCChatOptions.attr[index].show == 'on' || LHCChatOptions.attr[index].show == 'off')) ? LHCChatOptions.attr[index].show : 'b'));
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

    parseOptionsOnline : function(){
    	argumentsQuery = new Array();

		if (typeof LHCChatOptions != 'undefined') {
	    	
	    	if (typeof LHCChatOptions.attr_online != 'undefined') {
	    		if (LHCChatOptions.attr_online.length > 0){
					for (var index in LHCChatOptions.attr_online) {
						if (typeof LHCChatOptions.attr_online[index] != 'undefined' && typeof LHCChatOptions.attr_online[index].name != 'undefined') {
							argumentsQuery.push('onattr['+LHCChatOptions.attr_online[index].name+']='+encodeURIComponent(LHCChatOptions.attr_online[index].value));
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
    
    parseStorageArguments : function() {
    	if (localStorage && localStorage.getItem('lhc_ref') && localStorage.getItem('lhc_ref') != '') {
    		return '&r='+encodeURIComponent(localStorage.getItem('lhc_ref'));
    	}
    	return '';
    },
    
	addEvent : (function () {
	  if (document.addEventListener) {
	    return function (el, type, fn) {
	      if (el && el.nodeName || el === window) {
	        el.addEventListener(type, fn, false);
	      } else if (el && el.length) {
	        for (var i = 0; i < el.length; i++) {
	          lh_inst.addEvent(el[i], type, fn);
	        }
	      }
	    };
	  } else {
	    return function (el, type, fn) {
	      if (el && el.nodeName || el === window) {
	        el.attachEvent('on' + type, function () { return fn.call(el, window.event); });
	      } else if (el && el.length) {
	        for (var i = 0; i < el.length; i++) {
	          lh_inst.addEvent(el[i], type, fn);
	        }
	      }
	    };
	  }
	})(),

    showStartWindow : function(url_to_open) {

    	  this.lhc_need_help_hide();

	      // Do not check for new messages
          this.stopCheckNewMessage();

          this.removeById('lhc_container');	
          	  
		  var locationCurrent = encodeURIComponent(window.location.href.substring(window.location.protocol.length));
		  
          if ( url_to_open != undefined ) {
           		this.chatOpenedCallback('internal_invitation');	
                this.initial_iframe_url = url_to_open+this.getAppendCookieArguments()+'?URLReferer='+locationCurrent+this.parseOptions()+this.parseStorageArguments()+'&dt='+encodeURIComponent(document.title);
          } else {
          		this.chatOpenedCallback('internal');	
                this.initial_iframe_url = "<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/chatwidget')?><?php $leaveamessage == true ? print '/(leaveamessage)/true' : ''?><?php $department !== false ? print '/(department)/'.$department : ''?><?php $theme !== false ? print '/(theme)/'.$theme->id : ''?><?php $priority !== false ? print '/(priority)/'.$priority : ''?>"+this.getAppendCookieArguments()+'?URLReferer='+locationCurrent+this.parseOptions()+this.parseStorageArguments()+'&dt='+encodeURIComponent(document.title);
          };

          var widgetWidth = (typeof LHCChatOptions != 'undefined' && typeof LHCChatOptions.opt != 'undefined' && typeof LHCChatOptions.opt.widget_width != 'undefined') ? parseInt(LHCChatOptions.opt.widget_width) : 300;
		  var widgetHeight = (typeof LHCChatOptions != 'undefined' && typeof LHCChatOptions.opt != 'undefined' && typeof LHCChatOptions.opt.widget_height != 'undefined') ? parseInt(LHCChatOptions.opt.widget_height) : 340;

          this.iframe_html = '<div id="lhc_iframe_container"><iframe id="lhc_iframe" allowTransparency="true" scrolling="no" class="lhc-loading" frameborder="0" ' +
                       ( this.initial_iframe_url != '' ? ' src="'    + this.initial_iframe_url + '"' : '' ) +
                       ' width="'+widgetWidth+'"' +
                       ' height="'+widgetHeight+'"' +
                       ' style="width: '+widgetWidth+'px; height: '+widgetHeight+'px;"></iframe></div>';

          <?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/container.tpl.php')); ?>
         
          if (!this.cssWasAdded) {
          	this.cssWasAdded = true;
          	this.addCss(raw_css<?php ($theme !== false && $theme->custom_container_css !== '') ? print '+\''.str_replace(array("\n","\r"), '', $theme->custom_container_css).'\'' : '' ?>);
		  };

          var fragment = this.appendHTML(this.iframe_html);

          document.body.insertBefore(fragment, document.body.childNodes[0]);

          var lhc_obj = this;
          document.getElementById('lhc_close').onclick = function() { lhc_obj.hide(); return false; };
          document.getElementById('lhc_min').onclick = function() { lhc_obj.min(); return false; };
          <?php if (erLhcoreClassModelChatConfig::fetch('disable_popup_restore')->current_value == 0) : ?>
          document.getElementById('lhc_remote_window').onclick = function() { lhc_obj.openRemoteWindow(); return false; };
		  <?php endif; ?>
		  
		  var domContainer = document.getElementById('lhc_container');
		  var domIframe = 'lhc_iframe';
		  var domContainerId = 'lhc_container';
		  <?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/drag_drop_logic.tpl.php')); ?>		  
		      
		  if (this.cookieData.m) {this.min();};		  
		 	  
    },

    lh_openchatWindow : function() {    	
        <?php if ($click == 'internal') : ?>
        this.showStartWindow();
        <?php else : ?>
        this.lhc_need_help_hide();
        var locationCurrent = encodeURIComponent(window.location.href.substring(window.location.protocol.length));
         
        var popupHeight = (typeof LHCChatOptions != 'undefined' && typeof LHCChatOptions.opt != 'undefined' && typeof LHCChatOptions.opt.popup_height != 'undefined') ? parseInt(LHCChatOptions.opt.popup_height) : 520;
        var popupWidth = (typeof LHCChatOptions != 'undefined' && typeof LHCChatOptions.opt != 'undefined' && typeof LHCChatOptions.opt.popup_width != 'undefined') ? parseInt(LHCChatOptions.opt.popup_width) : 500;
        window.open(this.urlopen+this.getAppendCookieArguments()+'?URLReferer='+locationCurrent+this.parseOptions()+this.parseStorageArguments(),this.windowname,"scrollbars=yes,menubar=1,resizable=1,width="+popupWidth+",height="+popupHeight);
        this.chatOpenedCallback('external');
        <?php endif; ?>
        return false;
    },

    chatOpenedCallback : function(type){
    	if (typeof LHCChatOptions != 'undefined' && typeof LHCChatOptions.callback != 'undefined' && typeof LHCChatOptions.callback.start_chat_cb != 'undefined') {
    		LHCChatOptions.callback.start_chat_cb(type+this.substatus);
    		this.substatus = '';
    	}
    },
    
    showStatusWidget : function() {

        var statusTEXT = '<a id="<?php ($isOnlineHelp == true) ? print 'online-icon' : print 'offline-icon' ?>" class="status-icon" href="#" onclick="return lh_inst.lh_openchatWindow()" ><?php if ($isOnlineHelp == true) : ?><?php if ($theme !== false && $theme->online_text !== '') : print htmlspecialchars_decode($theme->online_text,ENT_QUOTES); else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Live help is online...')?><?php endif; else : ?><?php if ($theme !== false && $theme->offline_text != '') : print htmlspecialchars_decode($theme->offline_text,ENT_QUOTES); else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Live help is offline...')?><?php endif;endif;?></a>';

        var raw_css = "#lhc_status_container * {direction:<?php (erConfigClassLhConfig::getInstance()->getOverrideValue('site','dir_language') == 'ltr' || erConfigClassLhConfig::getInstance()->getOverrideValue('site','dir_language') == '') ? print 'ltr;text-align:left;' : print 'rtl;text-align:right;'; ?>;font-family:arial;font-size:12px;box-sizing: content-box;zoom:1;margin:0;padding:0}\n#lhc_status_container .status-icon{text-decoration:none;font-size:12px;font-weight:bold;color:<?php $theme !== false ? print '#'.$theme->text_color : print '#000' ?>;display:block;padding:<?php echo $currentPosition['padding_text']?>;background:url('<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php if ($theme !== false && $theme->online_image_url !== false) : print $theme->online_image_url; else : ?><?php echo erLhcoreClassDesign::design('images/icons/user_green_chat.png');?><?php endif;?>') no-repeat <?php echo $currentPosition['background_position']?> center}\n#lhc_status_container:hover{<?php echo $currentPosition['widget_hover']?>}\n#lhc_status_container #offline-icon{background-image:url('<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php if ($theme !== false && $theme->offline_image_url !== false) : print $theme->offline_image_url; else : ?><?php echo erLhcoreClassDesign::design('images/icons/user_gray_chat.png');?><?php endif;?>')}\n#lhc_status_container{box-sizing: content-box;<?php echo $currentPosition['widget_radius']?>-webkit-box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);<?php echo $currentPosition['border_widget']?>;-moz-box-shadow:<?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);padding:5px 0px 0px 5px;width:190px;font-family:arial;font-size:12px;transition: 1s;position:fixed;<?php echo $currentPosition['position']?>;background-color:#<?php $theme !== false ? print $theme->onl_bcolor : print 'f6f6f6' ?>;z-index:9989;}<?php if ($noresponse == false) : ?>@media only screen and (max-width : 640px) {#lhc_need_help_container{display:none;}#lhc_status_container{position:relative;top:0;right:0;bottom:0;left:0;width:auto;border-radius:2px;box-shadow:none;border:1px solid #e3e3e3;margin-bottom:5px;}}\n<?php endif;?>";
        this.addCss(raw_css<?php ($theme !== false && $theme->custom_status_css !== '') ? print '+\''.str_replace(array("\n","\r"), '', $theme->custom_status_css).'\'' : '' ?>);
	
		<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/we_here.tpl.php')); ?>	
        
        var htmlStatus = '<div id="lhc_status_container">'+subStatus+statusTEXT+'</div>';

        var fragment = this.appendHTML(htmlStatus);
        
		
        
        document.body.insertBefore(fragment, document.body.childNodes[0]);
    },
    
    timeoutInstance : null,

    stopCheckNewMessage : function() {
        clearTimeout(this.timeoutInstance);
    },

    startNewMessageCheck : function() {
    	var vid = this.cookieDataPers.vid;
    	var inst = this;
    	var locationCurrent = encodeURIComponent(window.location.href.substring(window.location.protocol.length));
    	
        this.timeoutInstance = setTimeout(function() {
            lh_inst.removeById('lhc_operator_message');
            var th = document.getElementsByTagName('head')[0];
            var s = document.createElement('script');
            s.setAttribute('id','lhc_operator_message');
            s.setAttribute('type','text/javascript');
            s.setAttribute('src','<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/chatcheckoperatormessage')?><?php $priority !== false ? print '/(priority)/'.$priority : ''?><?php $theme !== false ? print '/(theme)/'.$theme->id : ''?><?php $department !== false ? print '/(department)/'.$department : ''?><?php $identifier !== false ? print '/(identifier)/'.htmlspecialchars($identifier) : ''?>/(vid)/'+vid+'?l='+locationCurrent+'&dt='+encodeURIComponent(document.title));
            th.appendChild(s);
            lh_inst.startNewMessageCheck();        
        }, <?php echo (int)(erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['check_for_operator_msg']*1000); ?> );
    },

    startNewMessageCheckSingle : function() {
    	var vid = this.cookieDataPers.vid;
        lh_inst.removeById('lhc_operator_message');
        var th = document.getElementsByTagName('head')[0];
        var s = document.createElement('script');
        var locationCurrent = encodeURIComponent(window.location.href.substring(window.location.protocol.length));
        
        s.setAttribute('id','lhc_operator_message');
        s.setAttribute('type','text/javascript');
        s.setAttribute('src','<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/chatcheckoperatormessage')?><?php $priority !== false ? print '/(priority)/'.$priority : ''?><?php $theme !== false ? print '/(theme)/'.$theme->id : ''?><?php $department !== false ? print '/(department)/'.$department : ''?><?php $identifier !== false ? print '/(identifier)/'.htmlspecialchars($identifier) : ''?>/(count_page)/1/(vid)/'+vid+'?l='+locationCurrent+this.parseStorageArguments()+this.parseOptionsOnline()+'&dt='+encodeURIComponent(document.title));
        th.appendChild(s);
    },

    removeCookieAttr : function(attr){
    	if (this.cookieData[attr]) {
    		delete this.cookieData[attr];
    		this.storeSesCookie();
    	}
    },

    addCookieAttribute : function(attr, value){
    	if (!this.cookieData[attr] || this.cookieData[attr] != value){
	    	this.cookieData[attr] = value;
	    	this.storeSesCookie();
    	}
    },

    storePersistenCookie : function(){
	    lhc_Cookies('lhc_per',this.JSON.stringify(this.cookieDataPers),{expires:16070400<?php $trackDomain != '' || $disableHTML5Storage == 1 ? ($trackDomain != '' ? print ",domain:'.{$trackDomain}'" : print ",domain:this.getCookieDomain()") : ''?>});
    },

    storeSesCookie : function(){
    	<?php if ($trackDomain == '' && $disableHTML5Storage == 0) : ?>
    	if (localStorage) {
    		localStorage.setItem('lhc_ses',this.JSON.stringify(this.cookieData));
    	} else {
    	<?php endif;?>
	    	lhc_Cookies('lhc_ses',this.JSON.stringify(this.cookieData),{<?php $trackDomain != '' || $disableHTML5Storage == 1 ? ($trackDomain != '' ? print "domain:'.{$trackDomain}'" : print "domain:this.getCookieDomain()") : ''?>});
	    <?php if ($trackDomain == '' && $disableHTML5Storage == 0) : ?>}<?php endif;?>
    },

    initSessionStorage : function(){
    	<?php if ($trackDomain == '' && $disableHTML5Storage == 0) : ?>
    	if (localStorage && localStorage.getItem('lhc_ses')) {
    		this.cookieData = this.JSON.parse(localStorage.getItem('lhc_ses'));
    	} else {
    	<?php endif;?>
	    	var cookieData = lhc_Cookies('lhc_ses');
			if ( typeof cookieData === "string" && cookieData ) {
				this.cookieData = this.JSON.parse(cookieData);
			}
		<?php if ($trackDomain == '' && $disableHTML5Storage == 0) : ?>}<?php endif;?>
    },

    storeReferrer : function(ref){
    	if (localStorage && !localStorage.getItem('lhc_ref')) {
    		localStorage.setItem('lhc_ref',ref);
    	}
    },

    makeScreenshot : function() {    	
    	var inst = this;
    	if (typeof html2canvas == "undefined") {    					   		
		   		var th = document.getElementsByTagName('head')[0];
		        var s = document.createElement('script');
		        s.setAttribute('type','text/javascript');
		        s.setAttribute('src','<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::design('js/html2canvas.min.js');?>');
		        th.appendChild(s);		        
		        s.onreadystatechange = s.onload = function(){
		        	inst.makeScreenshot();
		        };		        
    	} else {
		    	try {
				  	html2canvas(document.body, {
						  onrendered: function(canvas) {
						         var xhr = new XMLHttpRequest();
						         xhr.open( "POST", '<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('file/storescreenshot')?>'+inst.getAppendCookieArguments(), true);
							     xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
							     xhr.send( "data=" + encodeURIComponent( canvas.toDataURL() ) );			         
						  }
					});
			   } catch(err) {
			  	
			   }
    	};    			
    },
    
    lhc_need_help_hide :function() {
    	this.removeById('lhc_need_help_container');
    	<?php if (erLhcoreClassModelChatConfig::fetch('need_help_tip_timeout')->current_value > 0) : ?>    	
    	this.addCookieAttributePersistent('lhc_hnh','<?php echo ((erLhcoreClassModelChatConfig::fetch('need_help_tip_timeout')->current_value * 3600) + time())?>');
    	<?php else : ?>    	
    	if (localStorage) {    	
	    	localStorage.removeItem('lhc_hnh');
    	};
    	this.addCookieAttribute('lhc_hnh','<?php echo ((24 * 3600) + time())?>');    	
    	<?php endif; ?>
    	
    	return false;
    },
    
    getPersistentAttribute : function(attr) {
    	<?php if ($trackDomain == '' && $disableHTML5Storage == 0) : ?>
    	if (localStorage) {    	
	    	return localStorage.getItem(attr);
    	} else {
    	<?php endif;?>
	    	if (this.cookieDataPers[attr]){
		    	return this.cookieDataPers[attr];
	    	}
	    	return null;    	
    	<?php if ($trackDomain == '' && $disableHTML5Storage == 0) : ?>}<?php endif;?>
    },
    
    addCookieAttributePersistent : function(attr, value){
    	<?php if ($trackDomain == '' && $disableHTML5Storage == 0) : ?>
    	if (localStorage) {
    		localStorage.setItem(attr,value);
    	} else {
    	<?php endif;?>
    	if (!this.cookieDataPers[attr] || this.cookieDataPers[attr] != value){
	    	this.cookieDataPers[attr] = value;
	    	this.storePersistenCookie();	    	
    	}
    	<?php if ($trackDomain == '' && $disableHTML5Storage == 0) : ?>}<?php endif;?>
    },
    
    lhc_need_help_click : function() {
    	this.lhc_need_help_hide();
    	this.lh_openchatWindow();    	
    },
    
    handleMessage : function(e) {
    	var action = e.data.split(':')[0];
    	if (action == 'lhc_sizing_chat') {
    		var height = e.data.split(':')[1];
    		var elementObject = document.getElementById('lhc_iframe');
    		var iframeContainer = document.getElementById('lhc_container');
    		
    		if (elementObject){
    			elementObject.height = height;
    			elementObject.style.height = height+'px';
    		}
    		
    		iframeContainer.className = iframeContainer.className;
    		//iframeContainer.style.height = (parseInt(height)+27)+'px';
    	} else if (action == 'lhc_ch') {
    		var parts = e.data.split(':');
    		if (parts[1] != '' && parts[2] != '') {
    			lh_inst.addCookieAttribute(parts[1],parts[2]);
    		}
    	} else if (action == 'lhc_screenshot') {
    		lh_inst.makeScreenshot();
    	}
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

var cookieData = lhc_Cookies('lhc_per');
if ( typeof cookieData === "string" && cookieData ) {
	lh_inst.cookieDataPers = lh_inst.JSON.parse(cookieData);	
	if (!lh_inst.cookieDataPers.vid) {
		lh_inst.cookieDataPers = {<?php isset($vid) ? print 'vid:\''.$vid.'\'' : ''?>};
		lh_inst.storePersistenCookie();
	};
} else {
	lh_inst.cookieDataPers = {<?php isset($vid) ? print 'vid:\''.$vid.'\'' : ''?>};
	lh_inst.storePersistenCookie();
};

lh_inst.initSessionStorage();

<?php if ($referrer != '') : ?>
lh_inst.storeReferrer('<?php echo htmlspecialchars($referrer,ENT_QUOTES)?>');
<?php endif; ?>

<?php if ($position == 'original' || $position == '') :
// You can style bottom HTML whatever you want. ?>
<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/native_placement.tpl.php')); ?>

<?php elseif (in_array($position, array('bottom_right','bottom_left','middle_right','middle_left'))) : ?>
lh_inst.showStatusWidget();
<?php endif; ?>

if (lh_inst.cookieData.hash) {
	lh_inst.stopCheckNewMessage();
	lh_inst.substatus = '_reopen';
    lh_inst.showStartWindow();
}

<?php if ($check_operator_messages == 'true' && $disable_pro_active == false) : ?>
if (!lh_inst.cookieData.hash) {
	lh_inst.startNewMessageCheck();
}
<?php endif; ?>

<?php if ($disable_pro_active == false && $track_online_users == true) : ?>
if (!lh_inst.cookieData.hash) {
	lh_inst.startNewMessageCheckSingle();
}
<?php endif;?>

<?php
endif; // hide if offline
exit; ?>