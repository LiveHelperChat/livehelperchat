<?php $trackDomain = erLhcoreClassModelChatConfig::fetch('track_domain')->current_value;?>

/*! Cookies.js - 0.3.1; Copyright (c) 2013, Scott Hamper; http://www.opensource.org/licenses/MIT */
(function(e){"use strict";var a=function(b,d,c){return 1===arguments.length?a.get(b):a.set(b,d,c)};a._document=document;a._navigator=navigator;a.defaults={path:"/"};a.get=function(b){a._cachedDocumentCookie!==a._document.cookie&&a._renewCache();return a._cache[b]};a.set=function(b,d,c){c=a._getExtendedOptions(c);c.expires=a._getExpiresDate(d===e?-1:c.expires);a._document.cookie=a._generateCookieString(b,d,c);return a};a.expire=function(b,d){return a.set(b,e,d)};a._getExtendedOptions=function(b){return{path:b&&
b.path||a.defaults.path,domain:b&&b.domain||a.defaults.domain,expires:b&&b.expires||a.defaults.expires,secure:b&&b.secure!==e?b.secure:a.defaults.secure}};a._isValidDate=function(b){return"[object Date]"===Object.prototype.toString.call(b)&&!isNaN(b.getTime())};a._getExpiresDate=function(b,d){d=d||new Date;switch(typeof b){case "number":b=new Date(d.getTime()+1E3*b);break;case "string":b=new Date(b)}if(b&&!a._isValidDate(b))throw Error("`expires` parameter cannot be converted to a valid Date instance");
return b};a._generateCookieString=function(b,a,c){b=encodeURIComponent(b);a=(a+"").replace(/[^!#$&-+\--:<-\[\]-~]/g,encodeURIComponent);c=c||{};b=b+"="+a+(c.path?";path="+c.path:"");b+=c.domain?";domain="+c.domain:"";b+=c.expires?";expires="+c.expires.toUTCString():"";return b+=c.secure?";secure":""};a._getCookieObjectFromString=function(b){var d={};b=b?b.split("; "):[];for(var c=0;c<b.length;c++){var f=a._getKeyValuePairFromCookieString(b[c]);d[f.key]===e&&(d[f.key]=f.value)}return d};a._getKeyValuePairFromCookieString=
function(b){var a=b.indexOf("="),a=0>a?b.length:a;return{key:decodeURIComponent(b.substr(0,a)),value:decodeURIComponent(b.substr(a+1))}};a._renewCache=function(){a._cache=a._getCookieObjectFromString(a._document.cookie);a._cachedDocumentCookie=a._document.cookie};a._areEnabled=function(){return a._navigator.cookieEnabled||"1"===a.set("cookies.js",1).get("cookies.js")};a.enabled=a._areEnabled();"function"===typeof define&&define.amd?define(function(){return a}):"undefined"!==typeof exports?("undefined"!==
typeof module&&module.exports&&(exports=module.exports=a),exports.lhc_Cookies=a):window.lhc_Cookies=a})();

lhc_Cookies.defaults = {path:"/",secure: <?php erLhcoreClassModelChatConfig::fetch('use_secure_cookie')->current_value == 1 ? print 'true' : print 'false' ?>};

var lhc_BrowseOffer = {
	JSON : {
            parse: window.JSON && (window.JSON.parse || window.JSON.decode) || String.prototype.evalJSON && function(str){return String(str).evalJSON();} || $.parseJSON || $.evalJSON,
            stringify:  Object.toJSON || window.JSON && (window.JSON.stringify || window.JSON.encode) || $.toJSON
    },
    canOpenOffer : true,
    offset_data : {},    
	cookieData : {},
	cookieDataPers : {'of':[]},
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
        s.setAttribute('src','<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('browseoffer/widgetclosed')?>/<?php echo $invite->hash?>');
        th.appendChild(s);
        this.removeById('lhc_container_browseoffer');
        this.removeById('lhc_browseoffer-bg');
        this.cookieDataPers.of.push(<?php echo $invite->id?>);
        this.storePersistenCookie();
        this.addCookieAttributePersistent('was_opened',true);        
    },
    		
	showBrowseOffer : function() {

   		  this.removeById('lhc_container_browseoffer');
		
   		  <?php if ($invite->custom_iframe_url != '' || $invite->lhc_iframe_content == 1) : ?>
   		  this.iframe_html = '<iframe id="lhcbrowseoffer_iframe" allowTransparency="true" scrolling="no" frameborder="0" ' +
                       ' src="<?php if ($invite->custom_iframe_url != '') : ?><?php echo $invite->custom_iframe_url ?><?php else : ?><?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('browseoffer/widget')?>/<?php echo $invite->hash?><?php endif;?>"' +                    
                       ' style="width: 100%; height: <?php echo $size_height?>px;"></iframe>';
         <?php else : ?>
         this.iframe_html = "<div id=\"lhcbrowseoffer_content\">"+<?php echo json_encode($invite->content)?>+"</div>";
         <?php endif; ?>
          	  
         this.iframe_html = '<div id="lhc_container_browseoffer">' +
                              '<div id="lhc_browseoffer_header"><?php include(erLhcoreClassDesign::designtpl('lhchat/widget_brand/browse_offers.tpl.php')); ?><a href="#" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>" id="lhc_browseoffer_close">×</a></div>' +
                              this.iframe_html + '</div>';

         raw_css = "#lhcbrowseoffer_content{padding:5px;}#lhcbrowseoffer_content iframe,#lhcbrowseoffer_content video{width:100%;}\n#lhc_browseoffer-bg{position: fixed;height: 100%;width: 100%;background: #000;background: rgba(0, 0, 0, 0.45);z-index: 99;display: none;top: 0;left: 0;};#lhc_container_browseoffer * {direction:<?php (erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language') == 'ltr' || erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language') == '') ? print 'ltr;text-align:left;' : print 'rtl;text-align:right;'; ?>;font-family:arial\;line-height:100%\;font-size:12px\;box-sizing: content-box\;-moz-box-sizing:content-box;padding:0;margin:0;}\n#lhc_container_browseoffer img {border:0;}\n#lhc_browseoffer_title{float:left;}\n#lhc_browseoffer_header{position:relative;z-index:9990;height:15px;overflow:hidden;background-color:#FFF;text-align:right;clear:both;padding:2px;}\n#lhc_browseoffer_close,#lhc-copyright-link{color: #ccc;text-decoration:none;font-family:arial;line-height:0.5;font-size:24px;font-weight: bold;padding:2px;float:right;}\n#lhc-copyright-link{font-size:12px}\n#lhc_browseoffer_close:hover{color:#575555}\n#lhc_container_browseoffer {box-shadow: 0 0 10px rgba(0, 0, 0, 0.4);max-width:95%;max-height:95%;width:<?php echo $size,$units?>;top:-100%;margin-top:-100%;left:50%;-webkit-transition: top 1s ease-in-out;-moz-transition: top 1s ease-in-out;  -o-transition: top 1s ease-in-out;  transition: top 1s ease-in-out;overflow: hidden;margin-left:-100%;background-color:#FFF\;\nz-index:9990;\n position: fixed;border:1px solid #CCC;-moz-user-select:none; }\n#lhc_container_browseoffer iframe{transition-property: height;transition-duration: 0.4s;-webkit-transition: height 0.4s ease-in-out;transition: height 0.4s;}\n";

         if (!this.cssWasAdded) {
          		this.cssWasAdded = true;
          		this.addCss(raw_css);
		 };

         var fragment = this.appendHTML(this.iframe_html);
         document.body.insertBefore(fragment, document.body.childNodes[0]);

         var lhc_obj = this;
         document.getElementById('lhc_browseoffer_close').onclick = function() { lhc_obj.hide(); return false; };
         setTimeout(function(){
         	lhc_obj.reflow();         	       	
         },500);
         
         var th = document.getElementsByTagName('head')[0];
         var s = document.createElement('script');
         s.setAttribute('type','text/javascript');
         s.setAttribute('src','<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('browseoffer/addhit')?>/<?php echo $invite->hash?>');
         th.appendChild(s);        
    },

   reflow : function(){
   			<?php if ($showoverlay === true) : ?>
   			this.removeById('lhc_browseoffer-bg');
         	var fragment = this.appendHTML('<div id="lhc_browseoffer-bg" style="display: block;"></div>');
         	document.body.insertBefore(fragment, document.body.childNodes[0]);
         	<?php endif; ?>
         	document.getElementById('lhc_container_browseoffer').style.marginTop = '-'+(document.getElementById('lhc_container_browseoffer').offsetHeight/2)+'px';
         	document.getElementById('lhc_container_browseoffer').style.marginLeft = '-<?php if ($units == '%') : ?><?php echo $size/2,$units?><?php else : ?>'+(document.getElementById('lhc_container_browseoffer').offsetWidth/2)+'px<?php endif;?>';
         	document.getElementById('lhc_container_browseoffer').style.top = '50%';  
   },

   handleMessage : function(e) {
    	var action = e.data.split(':')[0];
    	if (action == 'lhc_sizing_browseoffer' || action == 'lhc_sizing_faq_embed') {
    		var height = e.data.split(':')[1];
    		var elementObject = document.getElementById('lhcbrowseoffer_iframe');
    		var iframeContainer = document.getElementById('lhc_container_browseoffer');
    		elementObject.height = height;
    		elementObject.style.height = height+'px';
    		iframeContainer.className = iframeContainer.className;
    		iframeContainer.style.height = (parseInt(height)+26)+'px';    		
    		lhc_BrowseOffer.reflow();    		
    	};
   },
   
   storePersistenCookie : function(){
   		var parameters = {};
   		<?php $trackDomain != '' ? print 'parameters.domain = \''.$trackDomain.'\';' : ''?>
   		<?php $timeout !== false ? print 'parameters.expires = '.($timeout * 24*3600).';' : ''?>
	    lhc_Cookies('lhc_bo_per',this.JSON.stringify(this.cookieDataPers),parameters);
   },
    
   getPersistentAttribute : function(attr) {    
	    	if (this.cookieDataPers[attr]){
		    	return this.cookieDataPers[attr];
	    	}
	    	return null;    	    	
    },
    
    addCookieAttributePersistent : function(attr, value){    	
    	if (!this.cookieDataPers[attr] || this.cookieDataPers[attr] != value){
	    	this.cookieDataPers[attr] = value;
	    	this.storePersistenCookie();	    	
    	}    	
    }
};

var cookieData = lhc_Cookies('lhc_bo_per');

if ( typeof cookieData === "string" && cookieData ) {
	lhc_BrowseOffer.cookieDataPers = lhc_BrowseOffer.JSON.parse(cookieData);	
	if ( (lhc_BrowseOffer.cookieDataPers.was_opened && false == <?php echo $canreopen == true ? 'true' : 'false'?>) || (lhc_BrowseOffer.cookieDataPers.of && lhc_BrowseOffer.cookieDataPers.of.indexOf(<?php echo $invite->id?>) >= 0)) {
		lhc_BrowseOffer.canOpenOffer = false;
	}
};

if ( window.attachEvent ) {
	// IE
	window.attachEvent("onmessage",function(e){lhc_BrowseOffer.handleMessage(e);});
};

if ( document.attachEvent ) {
	// IE
	document.attachEvent("onmessage",function(e){lhc_BrowseOffer.handleMessage(e);});
};

if ( window.addEventListener ){
	// FF
	window.addEventListener("message",function(e){lhc_BrowseOffer.handleMessage(e);}, false);
};

if (lhc_BrowseOffer.canOpenOffer == true) {
	setTimeout(function(){
		lhc_BrowseOffer.showBrowseOffer();
	},<?php echo $invite->time_on_site*1000?>)	
};
