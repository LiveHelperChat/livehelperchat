<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/is_online_help.tpl.php')); ?>

<?php

// Perhaps user does not want to show live help when it's offline
if ( !($isOnlineHelp == false && $hide_offline == 'true') ) : ?>

/*! Cookies.js - 0.4.0; Copyright (c) 2014, Scott Hamper; http://www.opensource.org/licenses/MIT */
(function(e){"use strict";var b=function(a,d,c){return 1===arguments.length?b.get(a):b.set(a,d,c)};b._document=document;b._navigator=navigator;b.defaults={path:"/"};b.get=function(a){b._cachedDocumentCookie!==b._document.cookie&&b._renewCache();return b._cache[a]};b.set=function(a,d,c){c=b._getExtendedOptions(c);c.expires=b._getExpiresDate(d===e?-1:c.expires);b._document.cookie=b._generateCookieString(a,d,c);return b};b.expire=function(a,d){return b.set(a,e,d)};b._getExtendedOptions=function(a){return{path:a&& a.path||b.defaults.path,domain:a&&a.domain||b.defaults.domain,expires:a&&a.expires||b.defaults.expires,secure:a&&a.secure!==e?a.secure:b.defaults.secure}};b._isValidDate=function(a){return"[object Date]"===Object.prototype.toString.call(a)&&!isNaN(a.getTime())};b._getExpiresDate=function(a,d){d=d||new Date;switch(typeof a){case "number":a=new Date(d.getTime()+1E3*a);break;case "string":a=new Date(a)}if(a&&!b._isValidDate(a))throw Error("`expires` parameter cannot be converted to a valid Date instance"); return a};b._generateCookieString=function(a,b,c){a=a.replace(/[^#$&+\^`|]/g,encodeURIComponent);a=a.replace(/\(/g,"%28").replace(/\)/g,"%29");b=(b+"").replace(/[^!#$&-+\--:<-\[\]-~]/g,encodeURIComponent);c=c||{};a=a+"="+b+(c.path?";path="+c.path:"");a+=c.domain?";domain="+c.domain:"";a+=c.expires?";expires="+c.expires.toUTCString():"";return a+=c.secure?";secure":""};b._getCookieObjectFromString=function(a){var d={};a=a?a.split("; "):[];for(var c=0;c<a.length;c++){var f=b._getKeyValuePairFromCookieString(a[c]); d[f.key]===e&&(d[f.key]=f.value)}return d};b._getKeyValuePairFromCookieString=function(a){var b=a.indexOf("="),b=0>b?a.length:b;try {return{key:decodeURIComponent(a.substr(0,b)),value:decodeURIComponent(a.substr(b+1))}} catch(e) {return{key:a.substr(0,b),value:a.substr(b+1)}}};b._renewCache=function(){b._cache=b._getCookieObjectFromString(b._document.cookie);b._cachedDocumentCookie=b._document.cookie};b._areEnabled=function(){var a="1"===b.set("cookies_lhc.js",1).get("cookies_lhc.js");b.expire("cookies_lhc.js");return a};b.enabled=b._areEnabled();window.lhc_Cookies=b})();

<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/options_variable_page.tpl.php')); ?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/functions/part/javascript_variables.tpl.php')); ?>

var lh_inst_page  = {
	JSON : {
            parse: window.JSON && (window.JSON.parse || window.JSON.decode) || String.prototype.evalJSON && function(str){return String(str).evalJSON();} || $.parseJSON || $.evalJSON,
            stringify:  Object.toJSON || window.JSON && (window.JSON.stringify || window.JSON.encode) || $.toJSON
    },
	cookieData : {},

    js_variables : <?php echo json_encode($jsVars);?>,

    lang: '<?php echo erLhcoreClassSystem::instance()->WWWDirLang?>',

    langDefault: '/<?php echo erLhcoreClassSystem::instance()->SiteAccess?>',

    hasSurvey : false,
    survey_id : '',
    surveyShown : false,
    reset : <?php isset($fresh) && $fresh == true ? print 'true' : print 'false'?>,
    hide : function() {
        if (!this.cookieData.hash || this.hasSurvey == false || this.surveyShown == true) {
            var th = document.getElementsByTagName('head')[0];
            var s = document.createElement('script');
            s.setAttribute('type','text/javascript');
            s.setAttribute('src','<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/chatwidgetclosed')?>/(eclose)/t'+this.getAppendCookieArguments());
            th.appendChild(s);
            s.onreadystatechange = s.onload = function(){
                lh_inst_page.removeCookieAttr('hash');
                lh_inst_page.showStartWindow();
                lh_inst_page.surveyShown = false;
            };
        } else {
            this.showSurvey();
        }
    },

    storeReferrer : function(ref){
        if (sessionStorage && !sessionStorage.getItem('lhc_ref')) {
            try {
                sessionStorage.setItem('lhc_ref',ref);
            } catch(err) {};
        }
    },

    parseStorageArguments : function() {
        if (sessionStorage && sessionStorage.getItem('lhc_ref') && sessionStorage.getItem('lhc_ref') != '') {
            return '&r='+encodeURIComponent(sessionStorage.getItem('lhc_ref'));
        }
        return '';
    },

    <?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/functions/part/show_survey_page.tpl.php')); ?>

    parseOptions : function() {
		argumentsQuery = new Array();
        var paramsReturn = '';
		if (typeof <?php echo $chatOptionsVariablePage?> != 'undefined') {
	    	if (typeof <?php echo $chatOptionsVariablePage?>.attr != 'undefined') {
	    		if (<?php echo $chatOptionsVariablePage?>.attr.length > 0){
					for (var index in <?php echo $chatOptionsVariablePage?>.attr) {
						if (typeof <?php echo $chatOptionsVariablePage?>.attr[index] != 'undefined' && typeof <?php echo $chatOptionsVariablePage?>.attr[index].type != 'undefined') {							
							argumentsQuery.push('name[]='+encodeURIComponent(<?php echo $chatOptionsVariablePage?>.attr[index].name)+'&encattr[]='+(typeof <?php echo $chatOptionsVariablePage?>.attr[index].encrypted != 'undefined' && <?php echo $chatOptionsVariablePage?>.attr[index].encrypted == true ? 't' : 'f')+'&value[]='+encodeURIComponent(<?php echo $chatOptionsVariablePage?>.attr[index].value)+'&type[]='+encodeURIComponent(<?php echo $chatOptionsVariablePage?>.attr[index].type)+'&size[]='+encodeURIComponent(<?php echo $chatOptionsVariablePage?>.attr[index].size)+'&req[]='+(typeof <?php echo $chatOptionsVariablePage?>.attr[index].req != 'undefined' && <?php echo $chatOptionsVariablePage?>.attr[index].req == true ? 't' : 'f')+'&sh[]='+((typeof <?php echo $chatOptionsVariablePage?>.attr[index].show != 'undefined' && (<?php echo $chatOptionsVariablePage?>.attr[index].show == 'on' || <?php echo $chatOptionsVariablePage?>.attr[index].show == 'off')) ? <?php echo $chatOptionsVariablePage?>.attr[index].show : 'b'));
						};
					};
	    		};
	    	};

	    	if (typeof <?php echo $chatOptionsVariablePage?>.attr_prefill != 'undefined') {
	    		if (<?php echo $chatOptionsVariablePage?>.attr_prefill.length > 0){
					for (var index in <?php echo $chatOptionsVariablePage?>.attr_prefill) {
						if (typeof <?php echo $chatOptionsVariablePage?>.attr_prefill[index] != 'undefined' && typeof <?php echo $chatOptionsVariablePage?>.attr_prefill[index].name != 'undefined') {
							argumentsQuery.push('prefill['+<?php echo $chatOptionsVariablePage?>.attr_prefill[index].name+']='+encodeURIComponent(<?php echo $chatOptionsVariablePage?>.attr_prefill[index].value));
							if (typeof <?php echo $chatOptionsVariablePage?>.attr_prefill[index].hidden != 'undefined') {
								argumentsQuery.push('hattr[]='+encodeURIComponent(<?php echo $chatOptionsVariablePage?>.attr_prefill[index].name));
							};
						};
					};
	    		};
	    	};

	    	if (typeof <?php echo $chatOptionsVariablePage?>.attr_prefill_admin != 'undefined') {
	    		if (<?php echo $chatOptionsVariablePage?>.attr_prefill_admin.length > 0){
					for (var index in <?php echo $chatOptionsVariablePage?>.attr_prefill_admin) {
						if (typeof <?php echo $chatOptionsVariablePage?>.attr_prefill_admin[index] != 'undefined') {
							argumentsQuery.push('value_items_admin['+<?php echo $chatOptionsVariablePage?>.attr_prefill_admin[index].index+']='+encodeURIComponent(<?php echo $chatOptionsVariablePage?>.attr_prefill_admin[index].value));	

						    if (typeof <?php echo $chatOptionsVariablePage?>.attr_prefill_admin[index].hidden != 'undefined') {
							     argumentsQuery.push('via_hidden['+<?php echo $chatOptionsVariablePage?>.attr_prefill_admin[index].index+']='+encodeURIComponent(<?php echo $chatOptionsVariablePage?>.attr_prefill_admin[index].hidden == true ? 't' : 'f'));
							};

						    if (typeof <?php echo $chatOptionsVariablePage?>.attr_prefill_admin[index].encrypted != 'undefined') {
							     argumentsQuery.push('via_encrypted['+<?php echo $chatOptionsVariablePage?>.attr_prefill_admin[index].index+']='+encodeURIComponent(<?php echo $chatOptionsVariablePage?>.attr_prefill_admin[index].encrypted == true ? 't' : 'f'));
							};
						};
					};
	    		};
	    	};
	    	
	    	if (argumentsQuery.length > 0) {
	    		paramsReturn = '&'+argumentsQuery.join('&');
	    	};
    	};

        var js_args = [];
        var currentVar = null;
        for (var index in this.js_variables) {
            try {
                currentVar = eval(this.js_variables[index].var);
                if (typeof currentVar !== 'undefined' && currentVar !== null && currentVar !== '') {
                    js_args.push('jsvar['+this.js_variables[index].id+']='+encodeURIComponent(currentVar));
                }
            } catch(err) {
                
            }
        }

        if (js_args.length > 0) {
            paramsReturn = paramsReturn + '&' + js_args.join('&');
        }

    	return paramsReturn;
    },

    getAppendCookieArguments : function() {
		    var hashAppend = this.cookieData.hash && this.reset == false ? '/(hash)/'+this.cookieData.hash : '';
		    var hashResume = this.cookieData.hash_resume && this.reset == false ? '/(hash_resume)/'+this.cookieData.hash_resume : '';
		    var soundOption = this.cookieData.s ? '/(sound)/'+this.cookieData.s : '';

		    var paid_hash = '';
		    if (typeof <?php echo $chatOptionsVariablePage?> != 'undefined' && typeof <?php echo $chatOptionsVariablePage?>.attr_paid != 'undefined') {
		          paid_hash = '/(phash)/'+ <?php echo $chatOptionsVariablePage?>.attr_paid.phash;
		          paid_hash = paid_hash + '/(pvhash)/'+ <?php echo $chatOptionsVariablePage?>.attr_paid.pvhash;
		    };

		    return hashAppend+hashResume+soundOption+paid_hash+this.survey_id;
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

	addCookieAttribute : function(attr, value){
    	if (!this.cookieData[attr] || this.cookieData[attr] != value){
	    	this.cookieData[attr] = value;
	    	this.storeSesCookie();
    	}
    },

    showStartWindow : function(url_to_open) {

        var locationCurrent = encodeURIComponent(window.location.href.substring(window.location.protocol.length));

        var mobileFullHeight = typeof <?php echo $chatOptionsVariablePage?>.opt.mobile === 'undefined' || <?php echo $chatOptionsVariablePage?>.opt.mobile === true;

        if ( url_to_open != undefined ) {
            this.initial_iframe_url = url_to_open+this.getAppendCookieArguments()+'?URLReferer='+locationCurrent+this.parseOptions()+'&dt='+encodeURIComponent(document.title)+this.parseStorageArguments();
        } else {
            this.initial_iframe_url = "<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/chatwidget')?><?php $leaveamessage == true ? print '/(leaveamessage)/true' : ''?><?php $department !== false ? print '/(department)/'.$department : ''?><?php $theme !== false ? print '/(theme)/'.$theme : ''?><?php $operator !== false ? print '/(operator)/'.$operator : ''?><?php $priority !== false ? print '/(priority)/'.$priority : ''?><?php $bot_id !== null ? print '/(bot_id)/'.$bot_id : ''?><?php $uarguments !== false ? print '/(ua)/'.$uarguments : ''?>/(mode)/embed" + (mobileFullHeight === false ? '/(mobile)/false' : '') + this.getAppendCookieArguments()+'?URLReferer='+locationCurrent+this.parseOptions()+this.parseStorageArguments();
        };

        if (mobileFullHeight === true) {
            var raw_css = "@media only screen and (max-device-width : 640px) { #<?php echo $chatCSSPrefix?>_status_container_page{position: fixed; overflow: hidden; right: 0px; left: 0px; top: 0px; bottom: 0px;} #<?php echo $chatCSSPrefix?>_status_container_page iframe{width:100% !important;height: 100%!important}}";
            this.addCss(raw_css);
        }

        this.iframe_html = '<iframe id="<?php echo $chatCSSPrefix?>_iframe_page" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Live Help')?>" allowTransparency="true" scrolling="no" class="loading" frameborder="0" ' +
                       ( this.initial_iframe_url != '' ? ' src="'    + this.initial_iframe_url + '"' : '' ) +
                       ' width="100%"' +
                       ' height="340"' +
                       ' style="width: 100%; height: 340px;"></iframe>';

		document.getElementById('<?php echo $chatCSSPrefix?>_status_container_page').innerHTML = this.iframe_html;
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

    appendArg : function(args) {
        var tt = args.length/2;
        for (i = 0; i < tt; i++) {
            var argument = args[i*2];
            var value = args[(i*2)+1];
            if (argument == 'survey_id') {
                this.survey_id = '/(survey)/'+value;
                this.hasSurvey = true;
            }
        }
    },

	genericCallback : function(name){
    	if (typeof <?php echo $chatOptionsVariablePage?> != 'undefined' && typeof <?php echo $chatOptionsVariablePage?>.callback != 'undefined' && typeof <?php echo $chatOptionsVariablePage?>.callback[name] != 'undefined') {
    		<?php echo $chatOptionsVariablePage?>.callback[name](this);    	
    	}
    },

    <?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/functions/send_notification.tpl.php')); ?>

    handleMessage : function(e) {
        if (typeof e.data !== 'string') { return; }
    	var action = e.data.split(':')[0];
    	if (action == 'lhc_sizing_chat_page') {
    		var height = e.data.split(':')[1];
    		var elementObject = document.getElementById('<?php echo $chatCSSPrefix?>_iframe_page');
            if (parseInt(height) < (<?php echo $chatOptionsVariablePage?>.opt.height ? <?php echo $chatOptionsVariablePage?>.opt.height : 300)) {
                elementObject.height = (<?php echo $chatOptionsVariablePage?>.opt.height ? <?php echo $chatOptionsVariablePage?>.opt.height : 300);
                elementObject.style.height = (<?php echo $chatOptionsVariablePage?>.opt.height ? <?php echo $chatOptionsVariablePage?>.opt.height : 300)+'px';
            } else {
                if (height > elementObject.height) {
                    elementObject.height = height;
                    elementObject.style.height = height+'px';
                }
            }
    	} else if (action == 'lhc_ch') {
    		var parts = e.data.split(':');
    		if (parts[1] != '' && parts[2] != '') {
    			lh_inst_page.addCookieAttribute(parts[1],parts[2]);
    		};
    	} else if (action == 'lhc_chat_redirect') {
            document.location = e.data.split(':')[1].replace(new RegExp('__SPLIT__','g'),':');
    	} else if (action == 'lh_callback') {
    		var functionName = e.data.split(':')[1];
    		lh_inst_page.genericCallback(functionName);
        } else if (action == 'lhc_chat_survey') {
    		var value = e.data.split(':')[1];
            lh_inst_page.survey_id = '/(survey)/'+value;
            lh_inst_page.hasSurvey = true;
    	} else if (action == 'lhc_close') {
    		lh_inst_page.hide();
        } else if (action == 'lhc_notification') {
            var parts = e.data.split(':');
            lh_inst_page.sendNotification(parts);
    	} else if (action == 'lhc_chat_closed_explicit') {
    		lh_inst_page.hide();
    	} <?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/handlemessageembed_multiinclude.tpl.php')); ?>
    }
};

<?php if ($referrer != '') : ?>
lh_inst_page.storeReferrer(<?php echo json_encode($referrer)?>);
<?php endif; ?>

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

if ('serviceWorker' in navigator) {
    navigator.serviceWorker.addEventListener('message', function(event) {
        if (typeof event.data.lhc_ch !== 'undefined' && typeof event.data.lhc_cid !== 'undefined') {
            lh_inst_page.readNotification(event.data.lhc_cid, event.data.lhc_ch);
        }
    });
}

<?php endif;
exit;?>