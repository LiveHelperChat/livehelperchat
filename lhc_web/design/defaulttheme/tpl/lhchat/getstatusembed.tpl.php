<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/is_online_help.tpl.php')); ?>

<?php

// Perhaps user does not want to show live help when it's offline
if ( !($isOnlineHelp == false && $hide_offline == 'true') ) : ?>

/*! Cookies.js - 0.3.1; Copyright (c) 2013, Scott Hamper; http://www.opensource.org/licenses/MIT */
(function(e){"use strict";var a=function(b,d,c){return 1===arguments.length?a.get(b):a.set(b,d,c)};a._document=document;a._navigator=navigator;a.defaults={path:"/"};a.get=function(b){a._cachedDocumentCookie!==a._document.cookie&&a._renewCache();return a._cache[b]};a.set=function(b,d,c){c=a._getExtendedOptions(c);c.expires=a._getExpiresDate(d===e?-1:c.expires);a._document.cookie=a._generateCookieString(b,d,c);return a};a.expire=function(b,d){return a.set(b,e,d)};a._getExtendedOptions=function(b){return{path:b&&
b.path||a.defaults.path,domain:b&&b.domain||a.defaults.domain,expires:b&&b.expires||a.defaults.expires,secure:b&&b.secure!==e?b.secure:a.defaults.secure}};a._isValidDate=function(b){return"[object Date]"===Object.prototype.toString.call(b)&&!isNaN(b.getTime())};a._getExpiresDate=function(b,d){d=d||new Date;switch(typeof b){case "number":b=new Date(d.getTime()+1E3*b);break;case "string":b=new Date(b)}if(b&&!a._isValidDate(b))throw Error("`expires` parameter cannot be converted to a valid Date instance");
return b};a._generateCookieString=function(b,a,c){b=encodeURIComponent(b);a=(a+"").replace(/[^!#$&-+\--:<-\[\]-~]/g,encodeURIComponent);c=c||{};b=b+"="+a+(c.path?";path="+c.path:"");b+=c.domain?";domain="+c.domain:"";b+=c.expires?";expires="+c.expires.toUTCString():"";return b+=c.secure?";secure":""};a._getCookieObjectFromString=function(b){var d={};b=b?b.split("; "):[];for(var c=0;c<b.length;c++){var f=a._getKeyValuePairFromCookieString(b[c]);d[f.key]===e&&(d[f.key]=f.value)}return d};a._getKeyValuePairFromCookieString=
function(b){var a=b.indexOf("="),a=0>a?b.length:a;return{key:decodeURIComponent(b.substr(0,a)),value:decodeURIComponent(b.substr(a+1))}};a._renewCache=function(){a._cache=a._getCookieObjectFromString(a._document.cookie);a._cachedDocumentCookie=a._document.cookie};a._areEnabled=function(){return a._navigator.cookieEnabled||"1"===a.set("cookies.js",1).get("cookies.js")};a.enabled=a._areEnabled();"function"===typeof define&&define.amd?define(function(){return a}):"undefined"!==typeof exports?("undefined"!==
typeof module&&module.exports&&(exports=module.exports=a),exports.lhc_Cookies=a):window.lhc_Cookies=a})();

var lh_inst_page  = {
	JSON : {
            parse: window.JSON && (window.JSON.parse || window.JSON.decode) || String.prototype.evalJSON && function(str){return String(str).evalJSON();} || $.parseJSON || $.evalJSON,
            stringify:  Object.toJSON || window.JSON && (window.JSON.stringify || window.JSON.encode) || $.toJSON
    },
	cookieData : {},

    hide : function() {
        var th = document.getElementsByTagName('head')[0];
        var s = document.createElement('script');
        s.setAttribute('type','text/javascript');
        s.setAttribute('src','<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/chatwidgetclosed')?>');
        th.appendChild(s);
    },

    parseOptions : function() {
		argumentsQuery = new Array();

		if (typeof LHCChatOptionsPage != 'undefined') {
	    	if (typeof LHCChatOptionsPage.attr != 'undefined') {
	    		if (LHCChatOptionsPage.attr.length > 0){
					for (var index in LHCChatOptionsPage.attr) {
						argumentsQuery.push('name[]='+encodeURIComponent(LHCChatOptionsPage.attr[index].name)+'&value[]='+encodeURIComponent(LHCChatOptionsPage.attr[index].value)+'&type[]='+encodeURIComponent(LHCChatOptionsPage.attr[index].type)+'&size[]='+encodeURIComponent(LHCChatOptionsPage.attr[index].size)+'&req[]='+(typeof LHCChatOptionsPage.attr[index].req != 'undefined' && LHCChatOptionsPage.attr[index].req == true ? 't' : 'f')+'&sh[]='+((typeof LHCChatOptionsPage.attr[index].show != 'undefined' && (LHCChatOptionsPage.attr[index].show == 'on' || LHCChatOptionsPage.attr[index].show == 'off')) ? LHCChatOptionsPage.attr[index].show : 'b'));
					};
	    		};
	    	};

	    	if (typeof LHCChatOptionsPage.attr_prefill != 'undefined') {
	    		if (LHCChatOptionsPage.attr_prefill.length > 0){
					for (var index in LHCChatOptionsPage.attr_prefill) {
						argumentsQuery.push('prefill['+LHCChatOptionsPage.attr_prefill[index].name+']='+encodeURIComponent(LHCChatOptionsPage.attr_prefill[index].value));
					};
	    		};
	    	};

	    	if (argumentsQuery.length > 0) {
	    		return '&'+argumentsQuery.join('&');
	    	};
    	};

    	return '';
    },

    getAppendCookieArguments : function() {
		    var hashAppend = this.cookieData.hash ? '/(hash)/'+this.cookieData.hash : '';
		    var hashResume = this.cookieData.hash_resume ? '/(hash_resume)/'+this.cookieData.hash_resume : '';
		    var soundOption = this.cookieData.s ? '/(sound)/'+this.cookieData.s : '';
		    return hashAppend+hashResume+soundOption;
    },

	addCookieAttribute : function(attr, value){
    	if (!this.cookieData[attr] || this.cookieData[attr] != value){
	    	this.cookieData[attr] = value;
	    	this.storeSesCookie();
    	}
    },

    showStartWindow : function() {

         this.initial_iframe_url = "<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/chatwidget')?><?php $leaveamessage == true ? print '/(leaveamessage)/true' : ''?><?php $department !== false ? print '/(department)/'.$department : ''?><?php $priority !== false ? print '/(priority)/'.$priority : ''?>/(mode)/embed"+this.getAppendCookieArguments()+'?URLReferer='+encodeURIComponent(document.location)+this.parseOptions();

         this.iframe_html = '<iframe id="fdbk_iframe_page" allowTransparency="true" scrolling="no" class="loading" frameborder="0" ' +
                       ( this.initial_iframe_url != '' ? ' src="'    + this.initial_iframe_url + '"' : '' ) +
                       ' width="100%"' +
                       ' height="340"' +
                       ' style="width: 100%; height: 340x;"></iframe>';

		  document.getElementById('lhc_status_container_page').innerHTML = this.iframe_html;
    },

	storeSesCookie : function(){
    	if (sessionStorage) {
    		sessionStorage.setItem('lhc_ses',this.JSON.stringify(this.cookieData));
    	} else {
	    	lhc_Cookies('lhc_ses',this.JSON.stringify(this.cookieData));
	    }
    },

    initSessionStorage : function(){
    	if (sessionStorage && sessionStorage.getItem('lhc_ses')) {
    		this.cookieData = this.JSON.parse(sessionStorage.getItem('lhc_ses'));
    	} else {
	    	var cookieData = lhc_Cookies('lhc_ses');
			if ( typeof cookieData === "string" && cookieData ) {
				this.cookieData = this.JSON.parse(cookieData);
			}
		}
    },

	removeCookieAttr : function(attr){
    	if (this.cookieData[attr]) {
    		delete this.cookieData[attr];
    		this.storeSesCookie();
    	}
    },

    hide : function() {
        var th = document.getElementsByTagName('head')[0];
        var s = document.createElement('script');
        s.setAttribute('type','text/javascript');
        s.setAttribute('src','<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/chatwidgetclosed')?>'+this.getAppendCookieArguments());
        th.appendChild(s);
        this.removeCookieAttr('hash');
        this.showStartWindow();
    },

    handleMessage : function(e) {
    	var action = e.data.split(':')[0];
    	if (action == 'lhc_sizing_chat_page') {
    		var height = e.data.split(':')[1];
    		var elementObject = document.getElementById('fdbk_iframe_page');
    		elementObject.height = height;
    		elementObject.style.height = height+'px';
    	} else if (action == 'lhc_ch') {
    		var parts = e.data.split(':');
    		if (parts[1] != '' && parts[2] != '') {
    			lh_inst_page.addCookieAttribute(parts[1],parts[2]);
    		};
    	} else if (action == 'lhc_close') {
    		lh_inst_page.hide();
    	}
    }
};

lh_inst_page.initSessionStorage();

if ( window.attachEvent ) {
	// IE
	window.attachEvent("onmessage", lh_inst_page.handleMessage);
};

if ( document.attachEvent ) {
	// IE
	document.attachEvent("onmessage", lh_inst_page.handleMessage);
};

if ( window.addEventListener ){
	// FF
	window.addEventListener("message", lh_inst_page.handleMessage, false);
};

lh_inst_page.showStartWindow();

<?php endif;
exit;?>