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
				'border_widget' => 'border:1px solid #'.($theme !== false ? $theme->bor_bcolor : 'e3e3e3').';border-left:0;border-bottom:0;',
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
				'border_widget' => 'border:1px solid #'.($theme !== false ? $theme->bor_bcolor : 'e3e3e3').';border-right:0;border-bottom:0;',
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
				'border_widget' => 'border:1px solid #'.($theme !== false ? $theme->bor_bcolor : 'e3e3e3').';border-right:0;',
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
				'border_widget' => 'border:1px solid #'.($theme !== false ? $theme->bor_bcolor : 'e3e3e3').';border-left:0;',
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
		),
		'full_height_right' => array (
			'full_height' => true,
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
			'border_widget' => 'border:1px solid #'.($theme !== false ? $theme->bor_bcolor : 'e3e3e3').';border-right:0;border-bottom:0;',
			'widget_radius' => '-webkit-border-top-left-radius: 20px;-moz-border-radius-topleft: 20px;border-top-left-radius: 20px;',
			'nh_hor_pos' => 'margin-left:-80px;',
			'nh_tr_pos' => 'right:15px;'
		),
		'full_height_left' => array (
			'full_height' => true,
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
			'border_widget' => 'border:1px solid #'.($theme !== false ? $theme->bor_bcolor : 'e3e3e3').';border-left:0;border-bottom:0;',
			'background_position' => '0',
			'widget_radius' => '-webkit-border-top-right-radius: 20px;-moz-border-radius-topright: 20px;border-top-right-radius: 20px;',
			'nh_hor_pos' => 'margin-left:10px;',
			'nh_tr_pos' => 'left:15px;'
		),
);

if (key_exists($position, $positionArgument)){
	$currentPosition = $positionArgument[$position];
} else {
	$currentPosition = $positionArgument['bottom_right'];
}

$trackDomain = erLhcoreClassModelChatConfig::fetch('track_domain')->current_value;
$disableHTML5Storage = (int)erLhcoreClassModelChatConfig::fetch('disable_html5_storage')->current_value;
$trackOnline = (int)erLhcoreClassModelChatConfig::fetch('track_if_offline')->current_value;

?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/is_online_help.tpl.php')); ?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/geo_adjustment.tpl.php')); ?>

<?php

if ($isOnlineHelp == false && erLhcoreClassModelChatConfig::fetch('pro_active_show_if_offline')->current_value == 0) {
	$disable_pro_active = true;
};

// Perhaps user do not want to show live help when it's offline
if ( !($isOnlineHelp == false && $hide_offline == 'true') || $trackOnline == 1 ) : ?>

/*! Cookies.js - 0.4.0; Copyright (c) 2014, Scott Hamper; http://www.opensource.org/licenses/MIT */
(function(e){"use strict";var b=function(a,d,c){return 1===arguments.length?b.get(a):b.set(a,d,c)};b._document=document;b._navigator=navigator;b.defaults={path:"/"};b.get=function(a){b._cachedDocumentCookie!==b._document.cookie&&b._renewCache();return b._cache[a]};b.set=function(a,d,c){c=b._getExtendedOptions(c);c.expires=b._getExpiresDate(d===e?-1:c.expires);b._document.cookie=b._generateCookieString(a,d,c);return b};b.expire=function(a,d){return b.set(a,e,d)};b._getExtendedOptions=function(a){return{path:a&& a.path||b.defaults.path,domain:a&&a.domain||b.defaults.domain,expires:a&&a.expires||b.defaults.expires,secure:a&&a.secure!==e?a.secure:b.defaults.secure}};b._isValidDate=function(a){return"[object Date]"===Object.prototype.toString.call(a)&&!isNaN(a.getTime())};b._getExpiresDate=function(a,d){d=d||new Date;switch(typeof a){case "number":a=new Date(d.getTime()+1E3*a);break;case "string":a=new Date(a)}if(a&&!b._isValidDate(a))throw Error("`expires` parameter cannot be converted to a valid Date instance"); return a};b._generateCookieString=function(a,b,c){a=a.replace(/[^#$&+\^`|]/g,encodeURIComponent);a=a.replace(/\(/g,"%28").replace(/\)/g,"%29");b=(b+"").replace(/[^!#$&-+\--:<-\[\]-~]/g,encodeURIComponent);c=c||{};a=a+"="+b+(c.path?";path="+c.path:"");a+=c.domain?";domain="+c.domain:"";a+=c.expires?";expires="+c.expires.toUTCString():"";return a+=c.secure?";secure":""};b._getCookieObjectFromString=function(a){var d={};a=a?a.split("; "):[];for(var c=0;c<a.length;c++){var f=b._getKeyValuePairFromCookieString(a[c]); d[f.key]===e&&(d[f.key]=f.value)}return d};b._getKeyValuePairFromCookieString=function(a){var b=a.indexOf("="),b=0>b?a.length:b;try {return{key:decodeURIComponent(a.substr(0,b)),value:decodeURIComponent(a.substr(b+1))}} catch(e) {return{key:a.substr(0,b),value:a.substr(b+1)}}};b._renewCache=function(){b._cache=b._getCookieObjectFromString(b._document.cookie);b._cachedDocumentCookie=b._document.cookie};b._areEnabled=function(){var a="1"===b.set("cookies_lhc.js",1).get("cookies_lhc.js");b.expire("cookies_lhc.js");return a};b.enabled=b._areEnabled();window.lhc_Cookies=b})();

lhc_Cookies.defaults = {path:"/",secure: <?php erLhcoreClassModelChatConfig::fetch('use_secure_cookie')->current_value == 1 ? print 'true' : print 'false' ?>};

<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/options_variable.tpl.php')); ?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/custom_get_status_js.tpl.php')); ?>

var lh_inst  = {
   JSON : {
            parse: window.JSON && (window.JSON.parse || window.JSON.decode) || String.prototype.evalJSON && function(str){return String(str).evalJSON();} || $.parseJSON || $.evalJSON,
            stringify:  Object.toJSON || window.JSON && (window.JSON.stringify || window.JSON.encode) || $.toJSON
    },
    isOnline : <?php echo $isOnlineHelp == true ? 'true' : 'false'?>,
    disabledGeo : <?php echo (isset($disableByGeoAdjustment) && $disableByGeoAdjustment == true) ? 'true' : 'false' ?>,
    checkOperatorMessage : <?php echo $check_operator_messages == true ? 'true' : 'false'?>,
	offset_data : '',
	lang: '<?php echo erLhcoreClassSystem::instance()->WWWDirLang?>',
	langDefault: '/<?php echo erLhcoreClassSystem::instance()->SiteAccess?>',
	is_dragging : false,
	is_full_height : <?= isset($currentPosition['full_height']) && $currentPosition['full_height'] ? 'true' : 'false' ?>,
	online_tracked : false,
    urlopen : function(){   
    	return "<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurlsite()?>"+this.lang+"/chat/startchat<?php $leaveamessage == true ? print '/(leaveamessage)/true' : ''?><?php $theme !== false ? print '/(theme)/'.$theme->id : ''?><?php $department !== false ? print '/(department)/'.$department : ''?><?php $operator !== false ? print '/(operator)/'.$operator : ''?><?php $priority !== false ? print '/(priority)/'.$priority : ''?><?php $uarguments !== false ? print '/(ua)/'.$uarguments : ''?>"+this.survey_id;
    },
	<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/custom_get_status_lh_inst_js.tpl.php')); ?>	
    hasSurvey : <?php echo $survey !== false ? 'true ': 'false'?>,
    survey_id : '<?php echo $survey !== false ? '/(survey)/' . $survey : ''?>',
    surveyShown : false,
    explicitClose : false,
    windowname : "startchatwindow",
	substatus : '',
    cookieData : {},
    cookieDataPers : {},
	domain : false,
    isSharing : false,
    extensionArgs : '',    
    prefillMessage : '',    
    getCookieDomain : function(domain) {    
    	 if (this.domain !== false) {
    	 	return this.domain;
    	 } else {    
	    	if (typeof <?php echo $chatOptionsVariable?> != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt.domain != 'undefined') {
	    		this.domain = '.'+<?php echo $chatOptionsVariable?>.opt.domain;
	    	} else {    	
	    		this.domain = '.'+document.location.hostname.replace(/^(?:[a-z0-9\-\.]+\.)??([a-z0-9\-]+)?(\.com|\.net|\.org|\.biz|\.ws|\.in|\.me|\.co\.uk|\.co|\.org\.uk|\.ltd\.uk|\.plc\.uk|\.me\.uk|\.edu|\.mil|\.br\.com|\.cn\.com|\.eu\.com|\.hu\.com|\.no\.com|\.qc\.com|\.sa\.com|\.se\.com|\.se\.net|\.us\.com|\.uy\.com|\.ac|\.co\.ac|\.gv\.ac|\.or\.ac|\.ac\.ac|\.af|\.am|\.as|\.at|\.ac\.at|\.co\.at|\.gv\.at|\.or\.at|\.asn\.au|\.com\.au|\.edu\.au|\.org\.au|\.net\.au|\.id\.au|\.be|\.ac\.be|\.adm\.br|\.adv\.br|\.am\.br|\.arq\.br|\.art\.br|\.bio\.br|\.cng\.br|\.cnt\.br|\.com\.br|\.ecn\.br|\.eng\.br|\.esp\.br|\.etc\.br|\.eti\.br|\.fm\.br|\.fot\.br|\.fst\.br|\.g12\.br|\.gov\.br|\.ind\.br|\.inf\.br|\.jor\.br|\.lel\.br|\.med\.br|\.mil\.br|\.net\.br|\.nom\.br|\.ntr\.br|\.odo\.br|\.org\.br|\.ppg\.br|\.pro\.br|\.psc\.br|\.psi\.br|\.rec\.br|\.slg\.br|\.tmp\.br|\.tur\.br|\.tv\.br|\.vet\.br|\.zlg\.br|\.br|\.ab\.ca|\.bc\.ca|\.mb\.ca|\.nb\.ca|\.nf\.ca|\.ns\.ca|\.nt\.ca|\.on\.ca|\.pe\.ca|\.qc\.ca|\.sk\.ca|\.yk\.ca|\.ca|\.cc|\.ac\.cn|\.com\.cn|\.edu\.cn|\.gov\.cn|\.org\.cn|\.bj\.cn|\.sh\.cn|\.tj\.cn|\.cq\.cn|\.he\.cn|\.nm\.cn|\.ln\.cn|\.jl\.cn|\.hl\.cn|\.js\.cn|\.zj\.cn|\.ah\.cn|\.gd\.cn|\.gx\.cn|\.hi\.cn|\.sc\.cn|\.gz\.cn|\.yn\.cn|\.xz\.cn|\.sn\.cn|\.gs\.cn|\.qh\.cn|\.nx\.cn|\.xj\.cn|\.tw\.cn|\.hk\.cn|\.mo\.cn|\.cn|\.cx|\.cz|\.de|\.dk|\.fo|\.com\.ec|\.tm\.fr|\.com\.fr|\.asso\.fr|\.presse\.fr|\.fr|\.gf|\.gs|\.co\.il|\.net\.il|\.ac\.il|\.k12\.il|\.gov\.il|\.muni\.il|\.ac\.in|\.co\.in|\.org\.in|\.ernet\.in|\.gov\.in|\.net\.in|\.res\.in|\.is|\.it|\.ac\.jp|\.co\.jp|\.go\.jp|\.or\.jp|\.ne\.jp|\.ac\.kr|\.co\.kr|\.go\.kr|\.ne\.kr|\.nm\.kr|\.or\.kr|\.li|\.lt|\.lu|\.asso\.mc|\.tm\.mc|\.com\.mm|\.org\.mm|\.net\.mm|\.edu\.mm|\.gov\.mm|\.ms|\.nl|\.no|\.nu|\.pl|\.ro|\.org\.ro|\.store\.ro|\.tm\.ro|\.firm\.ro|\.www\.ro|\.arts\.ro|\.rec\.ro|\.info\.ro|\.nom\.ro|\.nt\.ro|\.se|\.si|\.com\.sg|\.org\.sg|\.net\.sg|\.gov\.sg|\.sk|\.st|\.tf|\.ac\.th|\.co\.th|\.go\.th|\.mi\.th|\.net\.th|\.or\.th|\.tm|\.to|\.com\.tr|\.edu\.tr|\.gov\.tr|\.k12\.tr|\.net\.tr|\.org\.tr|\.com\.tw|\.org\.tw|\.net\.tw|\.ac\.uk|\.uk\.com|\.uk\.net|\.gb\.com|\.gb\.net|\.vg|\.sh|\.kz|\.ch|\.info|\.ua|\.gov|\.name|\.pro|\.ie|\.hk|\.com\.hk|\.org\.hk|\.net\.hk|\.edu\.hk|\.us|\.tk|\.cd|\.by|\.ad|\.lv|\.eu\.lv|\.bz|\.es|\.jp|\.cl|\.ag|\.mobi|\.eu|\.co\.nz|\.org\.nz|\.net\.nz|\.maori\.nz|\.iwi\.nz|\.io|\.la|\.md|\.sc|\.sg|\.vc|\.tw|\.travel|\.my|\.se|\.tv|\.pt|\.com\.pt|\.edu\.pt|\.asia|\.fi|\.com\.ve|\.net\.ve|\.fi|\.org\.ve|\.web\.ve|\.info\.ve|\.co\.ve|\.tel|\.im|\.gr|\.ru|\.net\.ru|\.org\.ru|\.hr|\.com\.hr)$/, '$1$2');
	    	}
    	};    	
    	return this.domain;
    },
    
    appendArg : function(args) {        
        var tt = args.length/2;        
        for (i = 0; i < tt; i++) {
            var argument = args[i*2];
            var value = args[(i*2)+1];
            if (argument == 'survey_id') {
                this.survey_id = '/(survey)/'+value;
            }
        }        
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
    
	min : function(initial) {
		var dm = document.getElementById('lhc_container');					
		if (!dm.attrIsMin || dm.attrIsMin == false) {
			dm.attrHeight = dm.style.height;
			dm.attrIsMin = true;
			this.addClass(dm,'lhc-min');									
			<?php if ($currentPosition['posv'] == 'b') : ?>			
			if(dm.style.bottom!='' && dm.attrHeight!=''){
				dm.style.bottom = (parseInt(dm.style.bottom)+parseInt(dm.attrHeight)-35)+'px';							
			} else {
				if (initial == undefined) {
					dm.style.bottom = (parseInt(dm.style.bottom) + parseInt(document.getElementById('lhc_iframe_container').offsetHeight)-10)+'px';
				}			
			}
			<?php endif; ?>			
			this.addCookieAttribute('m',1);
			this.storePos(dm);
			<?php if ($currentPosition['posv'] == 'b' && $minimize_action == 'br') : ?>
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
				dm.style.bottom = (parseInt(dm.attrBottomOrigin)-parseInt(document.getElementById('lhc_iframe').style.height)+9)+'px';
				<?php if ($currentPosition['pos'] == 'r') : ?>
				dm.style.right = dm.attrRightOrigin;	
				<?php else : ?>
				dm.style.left = dm.attrLeftOrigin;	
				<?php endif;?>
			} else if (dm.style.bottom!=''){		
				dm.style.bottom = (parseInt(dm.style.bottom)-parseInt(document.getElementById('lhc_iframe').style.height)+9)+'px';
			}
			<?php endif;?>		
			this.removeCookieAttr('m');
			this.removeClass(dm,'lhc-min');
			var inst = this;		
			this.storePos(dm);
		};
		
		if (typeof initial !== 'undefined' && initial === true) {
            this.addClass(dm,'lhc-min-append');
            var that = this;
            setTimeout(function(){
                that.removeClass(dm,'lhc-min-append');
            },700);           
		}
	},
	
    hide : function() {
              
        if (!lh_inst.cookieData.hash || lh_inst.hasSurvey == false || lh_inst.surveyShown == true) {

            <?php if ((int)erLhcoreClassModelChatConfig::fetch('on_close_exit_chat')->current_value == 1) : ?>  
            this.explicitClose = true;            
            <?php endif;?>
                
            var th = document.getElementsByTagName('head')[0];
            var s = document.createElement('script');
            s.setAttribute('type','text/javascript');
            s.setAttribute('src','<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurlsite()?>'+this.lang+'/chat/chatwidgetclosed'+this.getAppendCookieArguments());
            th.appendChild(s);
            this.toggleStatusWidget(false);
            this.removeById('lhc_container');
            this.removeCookieAttr('hash');
            this.removeCookieAttr('pos');
            this.removeCookieAttr('m');
                    
            <?php if ($check_operator_messages == 'true' && $disable_pro_active == false) : ?>
            this.startNewMessageCheck();
            <?php endif; ?>
            this.timeoutStatusWidgetOpen = 0;
            this.surveyShown = true;
        } else {           
            this.showSurvey(); 
        }
    },

    showSurvey : function() {
        if (lh_inst.cookieData.hash && lh_inst.hasSurvey == true && lh_inst.surveyShown == false){
            var locationCurrent = encodeURIComponent(window.location.href.substring(window.location.protocol.length));    
            this.surveyShown = true;
            document.getElementById('lhc_iframe').src = "<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurlsite()?>"+this.lang+"/survey/fillwidget<?php $leaveamessage == true ? print '/(leaveamessage)/true' : ''?><?php $department !== false ? print '/(department)/'.$department : ''?><?php $theme !== false ? print '/(theme)/'.$theme->id : ''?><?php $operator !== false ? print '/(operator)/'.$operator : ''?><?php $priority !== false ? print '/(priority)/'.$priority : ''?><?php $uarguments !== false ? print '/(ua)/'.$uarguments : '' ?>"+this.getAppendCookieArguments()+'?URLReferer='+locationCurrent+this.parseOptions()+this.parseStorageArguments()+'&dt='+encodeURIComponent(document.title);
        }
    },
    
    getAppendCookieArguments : function() {
		    var hashAppend = this.cookieData.hash ? '/(hash)/'+this.cookieData.hash : '';
		    var vidAppend = this.cookieDataPers.vid ? '/(vid)/'+this.cookieDataPers.vid : '';
		    var hashResume = this.cookieData.hash_resume ? '/(hash_resume)/'+this.cookieData.hash_resume : '';
		    var soundOption = this.cookieData.s ? '/(sound)/'+this.cookieData.s : '';
		    var explicitClose = this.explicitClose ? '/(eclose)/t' : '';
		    		    		    
		    if (typeof <?php echo $chatOptionsVariable?> != 'undefined' && typeof <?php echo $chatOptionsVariable?>.attr_paid != 'undefined') {
		          explicitClose = explicitClose + '/(phash)/'+ <?php echo $chatOptionsVariable?>.attr_paid.phash;
		          explicitClose = explicitClose + '/(pvhash)/'+ <?php echo $chatOptionsVariable?>.attr_paid.pvhash;
		    };
		    		    
		    return hashAppend+vidAppend+hashResume+soundOption+explicitClose+this.survey_id;
    },

    openRemoteWindow : function() {
        this.removeById('lhc_container');
        var popupHeight = (typeof <?php echo $chatOptionsVariable?> != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt.popup_height != 'undefined') ? parseInt(<?php echo $chatOptionsVariable?>.opt.popup_height) : 520;
        var popupWidth = (typeof <?php echo $chatOptionsVariable?> != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt.popup_width != 'undefined') ? parseInt(<?php echo $chatOptionsVariable?>.opt.popup_width) : 500;
        var locationCurrent = encodeURIComponent(window.location.href.substring(window.location.protocol.length));        
        window.open(this.urlopen()+this.getAppendCookieArguments()+'/(er)/1'+'?URLReferer='+locationCurrent+this.parseOptions()+this.parseStorageArguments(),this.windowname,"scrollbars=yes,menubar=1,resizable=1,width="+popupWidth+",height="+popupHeight);
        this.removeCookieAttr('hash');
        this.toggleStatusWidget(false);
    },

    parseOptions : function() {
		argumentsQuery = new Array();
        var paramsReturn = '';
		if (typeof <?php echo $chatOptionsVariable?> != 'undefined') {
	    	if (typeof <?php echo $chatOptionsVariable?>.attr != 'undefined') {
	    		if (<?php echo $chatOptionsVariable?>.attr.length > 0){
					for (var index in <?php echo $chatOptionsVariable?>.attr) {
						if (typeof <?php echo $chatOptionsVariable?>.attr[index] != 'undefined' && typeof <?php echo $chatOptionsVariable?>.attr[index].type != 'undefined') {							
							argumentsQuery.push('name[]='+encodeURIComponent(<?php echo $chatOptionsVariable?>.attr[index].name)+'&encattr[]='+(typeof <?php echo $chatOptionsVariable?>.attr[index].encrypted != 'undefined' && <?php echo $chatOptionsVariable?>.attr[index].encrypted == true ? 't' : 'f')+'&value[]='+encodeURIComponent(<?php echo $chatOptionsVariable?>.attr[index].value)+'&type[]='+encodeURIComponent(<?php echo $chatOptionsVariable?>.attr[index].type)+'&size[]='+encodeURIComponent(<?php echo $chatOptionsVariable?>.attr[index].size)+'&req[]='+(typeof <?php echo $chatOptionsVariable?>.attr[index].req != 'undefined' && <?php echo $chatOptionsVariable?>.attr[index].req == true ? 't' : 'f')+'&sh[]='+((typeof <?php echo $chatOptionsVariable?>.attr[index].show != 'undefined' && (<?php echo $chatOptionsVariable?>.attr[index].show == 'on' || <?php echo $chatOptionsVariable?>.attr[index].show == 'off')) ? <?php echo $chatOptionsVariable?>.attr[index].show : 'b'));
						};
					};
	    		};
	    	};

	    	if (typeof <?php echo $chatOptionsVariable?>.attr_prefill != 'undefined') {
	    		if (<?php echo $chatOptionsVariable?>.attr_prefill.length > 0){
					for (var index in <?php echo $chatOptionsVariable?>.attr_prefill) {
						if (typeof <?php echo $chatOptionsVariable?>.attr_prefill[index] != 'undefined' && typeof <?php echo $chatOptionsVariable?>.attr_prefill[index].name != 'undefined') {
							argumentsQuery.push('prefill['+<?php echo $chatOptionsVariable?>.attr_prefill[index].name+']='+encodeURIComponent(<?php echo $chatOptionsVariable?>.attr_prefill[index].value));
							if (typeof <?php echo $chatOptionsVariable?>.attr_prefill[index].hidden != 'undefined') {
								argumentsQuery.push('hattr[]='+encodeURIComponent(<?php echo $chatOptionsVariable?>.attr_prefill[index].name));
							};
						};
					};
	    		};
	    	};

	    	if (typeof <?php echo $chatOptionsVariable?>.attr_prefill_admin != 'undefined') {
	    		if (<?php echo $chatOptionsVariable?>.attr_prefill_admin.length > 0){
					for (var index in <?php echo $chatOptionsVariable?>.attr_prefill_admin) {
						if (typeof <?php echo $chatOptionsVariable?>.attr_prefill_admin[index] != 'undefined') {
							argumentsQuery.push('value_items_admin['+<?php echo $chatOptionsVariable?>.attr_prefill_admin[index].index+']='+encodeURIComponent(<?php echo $chatOptionsVariable?>.attr_prefill_admin[index].value));	

						    if (typeof <?php echo $chatOptionsVariable?>.attr_prefill_admin[index].hidden != 'undefined') {
							     argumentsQuery.push('via_hidden['+<?php echo $chatOptionsVariable?>.attr_prefill_admin[index].index+']='+encodeURIComponent(<?php echo $chatOptionsVariable?>.attr_prefill_admin[index].hidden == true ? 't' : 'f'));
							};

						    if (typeof <?php echo $chatOptionsVariable?>.attr_prefill_admin[index].encrypted != 'undefined') {
							     argumentsQuery.push('via_encrypted['+<?php echo $chatOptionsVariable?>.attr_prefill_admin[index].index+']='+encodeURIComponent(<?php echo $chatOptionsVariable?>.attr_prefill_admin[index].encrypted == true ? 't' : 'f'));
							};
						};
					};
	    		};
	    	};
	    	
	    	if (argumentsQuery.length > 0) {
	    		paramsReturn = '&'+argumentsQuery.join('&');
	    	};
    	};

    	if (this.extensionArgs != '') {
    	    paramsReturn = paramsReturn + this.extensionArgs;
    	}
    	
    	if (this.prefillMessage != '') {
    	   paramsReturn = paramsReturn + '&' + 'prefillMsg=' + encodeURIComponent(this.prefillMessage);
    	}
    	
    	return paramsReturn;
    },

    setDefaultMessage : function(msg) {
        this.prefillMessage = msg;
    },
    
    parseOptionsOnline : function(){
    	argumentsQuery = new Array();

		if (typeof <?php echo $chatOptionsVariable?> != 'undefined') {
	    	
	    	if (typeof <?php echo $chatOptionsVariable?>.attr_online != 'undefined') {
	    		if (<?php echo $chatOptionsVariable?>.attr_online.length > 0){
					for (var index in <?php echo $chatOptionsVariable?>.attr_online) {
						if (typeof <?php echo $chatOptionsVariable?>.attr_online[index] != 'undefined' && typeof <?php echo $chatOptionsVariable?>.attr_online[index].name != 'undefined') {
							argumentsQuery.push('onattr['+<?php echo $chatOptionsVariable?>.attr_online[index].name+']='+encodeURIComponent(<?php echo $chatOptionsVariable?>.attr_online[index].value));
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
    	if (sessionStorage && sessionStorage.getItem('lhc_ref') && sessionStorage.getItem('lhc_ref') != '') {
    		return '&r='+encodeURIComponent(sessionStorage.getItem('lhc_ref'));
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

    showStartWindow : function(url_to_open,delayShow) {
		  
		  if (this.isOnline == false && typeof <?php echo $chatOptionsVariable?> != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt.offline_redirect != 'undefined'){
				window.open(<?php echo $chatOptionsVariable?>.opt.offline_redirect,"_blank");
				return;
		  };	
    	  this.lhc_need_help_hide();

	      // Do not check for new messages
          this.stopCheckNewMessage();

          this.removeById('lhc_container');	
          	  
		  var locationCurrent = encodeURIComponent(window.location.href.substring(window.location.protocol.length));
		  
          if ( url_to_open != undefined ) {
           		this.chatOpenedCallback('internal_invitation');	
                this.initial_iframe_url = url_to_open+this.getAppendCookieArguments()+'?URLReferer='+locationCurrent+this.parseOptions()+this.parseStorageArguments()+'&dt='+encodeURIComponent(document.title);
          } else {
          		this.chatOpenedCallback(this.isOnline == false ? 'internal_offline' : 'internal');	
                this.initial_iframe_url = "<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurlsite()?>"+this.lang+"/chat/chatwidget<?php $leaveamessage == true ? print '/(leaveamessage)/true' : ''?><?= isset($currentPosition['full_height']) && $currentPosition['full_height'] ?  '/(fullheight)/true' : '/(fullheight)/false' ?><?php $department !== false ? print '/(department)/'.$department : ''?><?php $theme !== false ? print '/(theme)/'.$theme->id : ''?><?php $operator !== false ? print '/(operator)/'.$operator : ''?><?php $priority !== false ? print '/(priority)/'.$priority : ''?><?php $uarguments !== false ? print '/(ua)/'.$uarguments : '' ?>"+this.getAppendCookieArguments()+'?URLReferer='+locationCurrent+this.parseOptions()+this.parseStorageArguments()+'&dt='+encodeURIComponent(document.title);
          };

          if (window.innerWidth < 768) {
          	window.open(this.initial_iframe_url,"_blank");
          	return;
          };
          
          lh_inst.surveyShown = false;                             
          lh_inst.timeoutStatusWidgetOpen = 1;
          
          var widgetWidth = (typeof <?php echo $chatOptionsVariable?> != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt.widget_width != 'undefined') ? parseInt(<?php echo $chatOptionsVariable?>.opt.widget_width) : 300;
		  var widgetHeight = (typeof <?php echo $chatOptionsVariable?> != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt.widget_height != 'undefined') ? parseInt(<?php echo $chatOptionsVariable?>.opt.widget_height) : 340;
		  var widgetHeightUnit = 'px';

	      if(this.is_full_height === true) {
			widgetHeight = 100;
			widgetHeightUnit = '%';
		  }

          this.iframe_html = '<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/before_iframe_container.tpl.php')); ?>'+'<div id="lhc_iframe_container" <?= isset($currentPosition['full_height']) && $currentPosition['full_height'] ? 'style="height: calc(100% - 25px);"' : '' ?>><iframe id="lhc_iframe" allowTransparency="true" scrolling="no" class="lhc-loading" frameborder="0" ' +
                       ( this.initial_iframe_url != '' ? ' src="'    + this.initial_iframe_url + '"' : '' ) +
                       ' width="'+widgetWidth+'"' +
                       ' height="'+widgetHeight+'"' +
                       ' style="width: '+widgetWidth+'px; height: '+widgetHeight+widgetHeightUnit+';"></iframe></div>';

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

          var lhc_obj = this;
          
     		this.addClass(document.getElementById('lhc_container'),'lhc-delayed');
     		setTimeout(function(){
     			lhc_obj.removeClass(document.getElementById('lhc_container'),'lhc-delayed');
     			lhc_obj.toggleStatusWidget(true);
     		},(typeof delayShow !== 'undefined') ? 1300 : 290);
                    
          var closeHandler = document.getElementById('lhc_close');
          if (closeHandler !== null){
              closeHandler.onclick = function() { lhc_obj.hide(); lh_inst.chatClosedCallback('user'); return false; };
          };
          
          document.getElementById('lhc_min').onclick = function() { lhc_obj.min(); return false; };
          <?php if (erLhcoreClassModelChatConfig::fetch('disable_popup_restore')->current_value == 0 && ($theme === false || $theme->hide_popup == 0)) : ?>
          document.getElementById('lhc_remote_window').onclick = function() { lhc_obj.openRemoteWindow(); return false; };
		  <?php endif; ?>
		  
		  var domContainer = document.getElementById('lhc_container');
		  var domIframe = 'lhc_iframe';
		  var domContainerId = 'lhc_container';
		  <?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/drag_drop_logic.tpl.php')); ?>		  
		      
		  if (this.cookieData.m) {this.min(true);};
		  
		  if (typeof delayShow === 'undefined') {
		  		this.toggleStatusWidget(true);
		  }
		  
    },

    toggleStatusWidget : function(hide){
      if(document.getElementById('lhc_status_container') != null) {
        if (hide == true){
          this.addClass(document.getElementById('lhc_status_container'),'hide-status');
        } else {
          this.removeClass(document.getElementById('lhc_status_container'),'hide-status');
        }
      }
    },
    
    lh_openchatWindow : function() {    	
        <?php if ($click == 'internal') : ?>
        this.showStartWindow();
        <?php else : ?>
        this.lhc_need_help_hide();
        var locationCurrent = encodeURIComponent(window.location.href.substring(window.location.protocol.length));
         
        var popupHeight = (typeof <?php echo $chatOptionsVariable?> != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt.popup_height != 'undefined') ? parseInt(<?php echo $chatOptionsVariable?>.opt.popup_height) : 520;
        var popupWidth = (typeof <?php echo $chatOptionsVariable?> != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt.popup_width != 'undefined') ? parseInt(<?php echo $chatOptionsVariable?>.opt.popup_width) : 500;
        window.open(this.urlopen()+this.getAppendCookieArguments()+'?URLReferer='+locationCurrent+this.parseOptions()+this.parseStorageArguments(),this.windowname,"scrollbars=yes,menubar=1,resizable=1,width="+popupWidth+",height="+popupHeight);
        this.chatOpenedCallback(this.isOnline == false ? 'external_offline' : 'external');
        <?php endif; ?>
        return false;
    },

    chatOpenedCallback : function(type){
    	if (typeof <?php echo $chatOptionsVariable?> != 'undefined' && typeof <?php echo $chatOptionsVariable?>.callback != 'undefined' && typeof <?php echo $chatOptionsVariable?>.callback.start_chat_cb != 'undefined') {
    		<?php echo $chatOptionsVariable?>.callback.start_chat_cb(type+this.substatus);
    		this.substatus = '';
    	}
    },

    chatClosedCallback : function(type){
      if (typeof <?php echo $chatOptionsVariable?> != 'undefined' && typeof <?php echo $chatOptionsVariable?>.callback != 'undefined' && typeof <?php echo $chatOptionsVariable?>.callback.close_chat_cb != 'undefined') {
        <?php echo $chatOptionsVariable?>.callback.close_chat_cb(type+this.substatus);
        this.substatus = '';
      }
    },

    genericCallback : function(name){
    	if (typeof <?php echo $chatOptionsVariable?> != 'undefined' && typeof <?php echo $chatOptionsVariable?>.callback != 'undefined' && typeof <?php echo $chatOptionsVariable?>.callback[name] != 'undefined') {
    		<?php echo $chatOptionsVariable?>.callback[name](this);    	
    	}
    },
    
    showStatusWidget : function() {
    <?php if ($position == 'original' || $position == '') :
	// You can style bottom HTML whatever you want. ?>
	<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/native_placement.tpl.php')); ?>

	<?php elseif (in_array($position, array_keys($positionArgument))) : ?>
		this.removeById('lhc_status_container');	
		
        var statusTEXT = '<a id="'+(this.isOnline == true ? 'online-icon' : 'offline-icon')+'" class="status-icon" href="#" onclick="return lh_inst.lh_openchatWindow()" >'+(this.isOnline ? <?php if ($theme !== false && $theme->online_text !== '') : print json_encode($theme->online_text); else : ?><?php echo json_encode(htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Live help is online...'),ENT_QUOTES))?><?php endif?> : <?php if ($theme !== false && $theme->offline_text != '') : print json_encode($theme->offline_text); else : ?><?php echo json_encode(htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Live help is offline...'),ENT_QUOTES))?><?php endif?>)+'</a>';

        if (!this.cssStatusWasAdded) {
          	this.cssStatusWasAdded = true;
          	<?php 
          	$iconsStatuses = array(
          	    'online_image_url' => ($theme !== false && $theme->online_image_url !== false && strpos($theme->online_image_url, 'http') !== false),
          	    'offline_image_url' => ($theme !== false && $theme->offline_image_url !== false && strpos($theme->offline_image_url, 'http') !== false)
          	);          	          	
          	?>
        	var raw_css = "#lhc_status_container.hide-status{display:none!important;}#lhc_status_container * {direction:<?php (erConfigClassLhConfig::getInstance()->getOverrideValue('site','dir_language') == 'ltr' || erConfigClassLhConfig::getInstance()->getOverrideValue('site','dir_language') == '') ? print 'ltr;text-align:left;' : print 'rtl;text-align:right;'; ?>;font-family:arial;font-size:12px;box-sizing: content-box;zoom:1;margin:0;padding:0}\n#lhc_status_container .status-icon{text-decoration:none;font-size:12px;font-weight:bold;color:<?php $theme !== false ? print '#'.$theme->text_color : print '#000' ?>;display:block;padding:<?php echo $currentPosition['padding_text']?>;background:url('<?php if ($iconsStatuses['online_image_url'] == false) : ?><?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php endif; ?><?php if ($theme !== false && $theme->online_image_url !== false) : print $theme->online_image_url; else : ?><?php echo erLhcoreClassDesign::design('images/icons/user_green_chat.png');?><?php endif;?>') no-repeat <?php echo $currentPosition['background_position']?> center}\n#lhc_status_container:hover{<?php echo $currentPosition['widget_hover']?>}\n#lhc_status_container #offline-icon{background-image:url('<?php if ($iconsStatuses['offline_image_url'] == false) : ?><?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php endif; ?><?php if ($theme !== false && $theme->offline_image_url !== false) : print $theme->offline_image_url; else : ?><?php echo erLhcoreClassDesign::design('images/icons/user_gray_chat.png');?><?php endif;?>')}\n#lhc_status_container{box-sizing: content-box;<?php echo $currentPosition['widget_radius']?>-webkit-box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);<?php echo $currentPosition['border_widget']?>;-moz-box-shadow:<?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);padding:5px 0px 0px 5px;width:190px;font-family:arial;font-size:12px;transition: 1s;position:fixed;<?php echo $currentPosition['position']?>;background-color:#<?php $theme !== false ? print $theme->onl_bcolor : print 'f6f6f6' ?>;z-index:9989;}<?php if ($noresponse == false) : ?>@media only screen and (max-width : 640px) {#lhc_need_help_container{display:none;}#lhc_status_container{position:relative;top:0;right:0;bottom:0;left:0;width:auto;border-radius:2px;box-shadow:none;border:1px solid #<?php $theme !== false ? print $theme->bor_bcolor : print 'e3e3e3' ?>;margin-bottom:5px;}}\n<?php endif;?>";
        	this.addCss(raw_css<?php ($theme !== false && $theme->custom_status_css !== '') ? print '+\''.str_replace(array("\n","\r"), '', $theme->custom_status_css).'\'' : '' ?>);
		};
	
		<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/we_here.tpl.php')); ?>	
        
        var htmlStatus = '<div id="lhc_status_container">'+subStatus+statusTEXT+'</div>';

        var fragment = this.appendHTML(htmlStatus);
        
        document.body.insertBefore(fragment, document.body.childNodes[0]);        
    <?php endif; ?>
    	if (typeof <?php echo $chatOptionsVariable?> != 'undefined' && typeof <?php echo $chatOptionsVariable?>.callback != 'undefined' && typeof <?php echo $chatOptionsVariable?>.callback.show_widget_cb != 'undefined') {
    		<?php echo $chatOptionsVariable?>.callback.show_widget_cb(this);    		
    	};
    },
    
    timeoutInstance : null,

    stopCheckNewMessage : function() {
        clearTimeout(this.timeoutInstance);
    },

    tag : '',
    
    addTag : function(tag) {
        this.tag = this.tag != '' ? this.tag + ',' + tag : '&tag='+tag; 
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
            s.setAttribute('src','<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurlsite()?>'+lh_inst.lang+'/chat/chatcheckoperatormessage<?php $priority !== false ? print '/(priority)/'.$priority : ''?><?php $theme !== false ? print '/(theme)/'.$theme->id : ''?><?php $operator !== false ? print '/(operator)/'.$operator : ''?><?php $department !== false ? print '/(department)/'.$department : ''?><?php $identifier !== false ? print '/(identifier)/'.htmlspecialchars($identifier) : ''?><?php $uarguments !== false ? print '/(ua)/'.$uarguments : '' ?><?= isset($currentPosition['full_height']) && $currentPosition['full_height'] ?  '/(fullheight)/true' : '/(fullheight)/false' ?>/(vid)/'+vid + lh_inst.survey_id + '/(uactiv)/'+lh_inst.userActive+'/(wopen)/'+lh_inst.timeoutStatusWidgetOpen+'?l='+locationCurrent+inst.tag+'&dt='+encodeURIComponent(document.title));
            th.appendChild(s);
            lh_inst.startNewMessageCheck();        
        }, <?php echo (int)(erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['check_for_operator_msg']*1000); ?> );
    },

    getTzOffset : function(){
	    Date.prototype.stdTimezoneOffset = function() {
		    var jan = new Date(this.getFullYear(), 0, 1);
		    var jul = new Date(this.getFullYear(), 6, 1);
		    return Math.max(jan.getTimezoneOffset(), jul.getTimezoneOffset());
		};
	
		Date.prototype.dst = function() {
		    return this.getTimezoneOffset() < this.stdTimezoneOffset();
		};
		
		var today = new Date();
		var timeZoneOffset = 0;
		
		if (today.dst()) { 
			timeZoneOffset = today.getTimezoneOffset();
		} else {
			timeZoneOffset = today.getTimezoneOffset()-60;
		};
		
		return (timeZoneOffset/60)*-1;
    },
    
    startNewMessageCheckSingle : function() {
    	var vid = this.cookieDataPers.vid;
        lh_inst.removeById('lhc_operator_message');
        var th = document.getElementsByTagName('head')[0];
        var s = document.createElement('script');
        var locationCurrent = encodeURIComponent(window.location.href.substring(window.location.protocol.length));
        var tzOffset = this.getTzOffset();
        
        s.setAttribute('id','lhc_operator_message');
        s.setAttribute('type','text/javascript');
        s.setAttribute('src','<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurlsite()?>'+this.lang+'/chat/chatcheckoperatormessage<?php $priority !== false ? print '/(priority)/'.$priority : ''?><?php $theme !== false ? print '/(theme)/'.$theme->id : ''?><?php $operator !== false ? print '/(operator)/'.$operator : ''?><?php $department !== false ? print '/(department)/'.$department : ''?><?php $identifier !== false ? print '/(identifier)/'.htmlspecialchars($identifier) : ''?><?php $uarguments !== false ? print '/(ua)/'.$uarguments : '' ?><?= isset($currentPosition['full_height']) && $currentPosition['full_height'] ?  '/(fullheight)/true' : '/(fullheight)/false' ?>/(tz)/' + tzOffset + this.survey_id + '/(count_page)/1/(vid)/'+vid+'/(uactiv)/'+lh_inst.userActive+'/(wopen)/'+lh_inst.timeoutStatusWidgetOpen+'?l='+locationCurrent+this.tag+this.parseStorageArguments()+this.parseOptionsOnline()+'&dt='+encodeURIComponent(document.title));
        th.appendChild(s);
    },

    logPageView : function() {
    	var vid = this.cookieDataPers.vid;       
        var th = document.getElementsByTagName('head')[0];
        var s = document.createElement('script');
        var locationCurrent = encodeURIComponent(window.location.href.substring(window.location.protocol.length));        
        var tzOffset = this.getTzOffset(); 
        s.setAttribute('id','lhc_log_pageview');
        s.setAttribute('type','text/javascript');
        s.setAttribute('src','<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurlsite()?>'+this.lang+'/chat/logpageview<?php $department !== false ? print '/(department)/'.$department : ''?><?php $identifier !== false ? print '/(identifier)/'.htmlspecialchars($identifier) : ''?><?php $uarguments !== false ? print '/(ua)/'.$uarguments : '' ?>/(tz)/'+tzOffset+'/(vid)/' + vid + this.survey_id + '/(uactiv)/'+lh_inst.userActive+'/(wopen)/'+lh_inst.timeoutStatusWidgetOpen+'?l='+locationCurrent+this.parseStorageArguments()+this.parseOptionsOnline()+'&dt='+encodeURIComponent(document.title));
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
    	try {
	    	lhc_Cookies('lhc_per',this.JSON.stringify(this.cookieDataPers),{expires:16070400<?php $trackDomain != '' || $disableHTML5Storage == 1 ? ($trackDomain != '' ? print ",domain:'.{$trackDomain}'" : print ",domain:this.getCookieDomain()") : ''?>});
	    } catch(err) { };
    },

    storeSesCookie : function(){
    	<?php if ($trackDomain == '' && $disableHTML5Storage == 0) : ?>
    	if (localStorage) {
    		try {
    			localStorage.setItem('lhc_ses',this.JSON.stringify(this.cookieData));
    		} catch(err) { // Fallback to cookie
    			lhc_Cookies('lhc_ses',this.JSON.stringify(this.cookieData),{<?php $trackDomain != '' || $disableHTML5Storage == 1 ? ($trackDomain != '' ? print "domain:'.{$trackDomain}'" : print "domain:this.getCookieDomain()") : ''?>});
    		};
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
    	if (sessionStorage && !sessionStorage.getItem('lhc_ref')) {
    		try {
    			sessionStorage.setItem('lhc_ref',ref);
    		} catch(err) {};
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
						         xhr.open( "POST", '<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurlsite()?>'+lh_inst.lang+'/file/storescreenshot'+inst.getAppendCookieArguments(), true);
							     xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
							     xhr.send( "data=" + encodeURIComponent( canvas.toDataURL() ) );			         
						  }
					});
			   } catch(err) {
			  	
			   }
    	};    			
    },
    
    finishScreenSharing : function(){
    	this.removeById('lhc_status_mirror');
		this.removeCookieAttr('shr');
		this.removeCookieAttr('shrm');
		this.isSharing = false;
		
		var vid = this.cookieDataPers.vid;       
        var th = document.getElementsByTagName('head')[0];
        var s = document.createElement('script');
        var locationCurrent = encodeURIComponent(window.location.href.substring(window.location.protocol.length));        
        var tzOffset = this.getTzOffset(); 
        s.setAttribute('id','lhc_finish_shr');
        s.setAttribute('type','text/javascript');
        s.setAttribute('src','<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurlsite()?>'+lh_inst.lang+'/cobrowse/finishsession/(sharemode)/'+lh_inst.sharemode+lh_inst.getAppendCookieArguments());
        th.appendChild(s);
        this.cobrowser = null;
    },
    
    cobrowse : null,
    
    startCoBrowse : function(chatHash,sharemode){
    	var inst = this;    	
    	if (this.isSharing == false && (this.cookieData.shr || <?php echo (int)erLhcoreClassModelChatConfig::fetch('sharing_auto_allow')->current_value?> == 1 || confirm(<?php echo json_encode(htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Allow operator to see your page content?'),ENT_QUOTES))?>)))
    	{
    		this.sharehash = chatHash || this.cookieData.hash || this.cookieData.shr;    		
    		this.sharemode = sharemode || this.cookieData.shrm || 'chat';
    		this.addCookieAttribute('shr',this.sharehash);
    		this.addCookieAttribute('shrm',this.sharemode);
    		
	    	if (typeof TreeMirror == "undefined") {    					   		
			   		var th = document.getElementsByTagName('head')[0];
			        var s = document.createElement('script');
			        s.setAttribute('type','text/javascript');
			        s.setAttribute('src','<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::designJS('js/cobrowse/compiled/cobrowse.visitor.min.js');?>');
			        th.appendChild(s);
			        s.onreadystatechange = s.onload = function(){
			        	inst.startCoBrowse(inst.sharehash,this.sharemode);
			        };		        
	    	} else {
		    	try {	 
		    		this.isSharing = true;
		    		this.addCookieAttribute('shr',this.sharehash);
		    		this.addCookieAttribute('shrm',this.sharemode);
		    		<?php include(erLhcoreClassDesign::designtpl('lhcobrowse/userinit.tpl.php')); ?>
			   } catch(err) {
			  		console.log(err);
			   }
	    	};
    	}
    },
    
    lhc_need_help_hide :function() {
    	this.removeById('lhc_need_help_container');
    	<?php $needHelpTimeout = $theme !== false ? $theme->show_need_help_timeout : erLhcoreClassModelChatConfig::fetch('need_help_tip_timeout')->current_value; ?>
    	<?php if ($needHelpTimeout > 0) : ?>    	
    	this.addCookieAttributePersistent('lhc_hnh','<?php echo (($needHelpTimeout * 3600) + time())?>');
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
    		try {
    			localStorage.setItem(attr,value);
    		} catch(err) {
    			if (!this.cookieDataPers[attr] || this.cookieDataPers[attr] != value){
			    	this.cookieDataPers[attr] = value;
			    	this.storePersistenCookie();	    	
		    	};    		
    		};
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
    
    initLanguage : function() {
   		var langUser = this.getPersistentAttribute('lng');   		
    	this.lang = (langUser != null && langUser != '' && langUser != undefined && this.langDefault != langUser) ? langUser : this.lang;   
    },
    
    resetTimeoutActivity : function() {
    
        var wasInactive = this.userActive == 0;        
        this.userActive = 1;
        
        if (wasInactive == true) {
            this.syncUserStatus(1);
        }
        
        clearTimeout(this.timeoutActivity);
        var _that = this;
        this.timeoutActivity = setTimeout(function(){
            _that.userActive = 0;
            _that.syncUserStatus(1);
        }, 300*1000);        
    },
    
    timeoutActivity : null,
    userActive : 1,
    timeoutStatuscheck : null,
    timeoutStatusWidgetOpen : 0,
    
    syncUserStatus : function(sender) {
    	var hashAppend = this.cookieData.hash ? '/(hash)/'+this.cookieData.hash : '';
		var hashResume = this.cookieData.hash_resume ? '/(hash_resume)/'+this.cookieData.hash_resume : '';
        this.removeById('lhc_check_status');
        var th = document.getElementsByTagName('head')[0];
        var s = document.createElement('script');
        s.setAttribute('id','lhc_check_status');
        s.setAttribute('type','text/javascript');
        s.setAttribute('src','<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurlsite()?>'+this.lang+'/chat/chatcheckstatus<?php $department !== false ? print '/(department)/'.$department : ''?><?php $uarguments !== false ? print '/(ua)/'.$uarguments : '' ?><?php $disable_online_tracking === true ? print '/(dot)/true' : ''?>/(status)/' + this.isOnline + this.survey_id + (this.cookieDataPers.vid ? '/(vid)/'+this.cookieDataPers.vid : '')+ hashAppend + hashResume + '/(uactiv)/'+this.userActive+'/(wopen)/'+this.timeoutStatusWidgetOpen + '/(uaction)/'+sender);
        th.appendChild(s);
    },
    
    checkStatusChat : function() {
    	<?php if ((int)erLhcoreClassModelChatConfig::fetch('checkstatus_timeout')->current_value > 0) : ?>       
    	clearTimeout(this.timeoutStatuscheck);
    	var _that = this;
        this.timeoutStatuscheck = setTimeout(function() {
            _that.syncUserStatus(0);
            _that.checkStatusChat();        
        },<?php echo ((int)erLhcoreClassModelChatConfig::fetch('checkstatus_timeout')->current_value)*1000; ?>);
        <?php endif;?>  
    },
      
    refreshCustomFields : function() {
        var xhr = new XMLHttpRequest();
        xhr.open( "POST", '<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurlsite()?>'+lh_inst.lang+'/chat/refreshcustomfields'+this.getAppendCookieArguments() , true);
	    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	    xhr.send(this.parseOptions());	
    },
      
    attatchActivityListeners : function() {
        <?php if ((int)erLhcoreClassModelChatConfig::fetch('track_activity')->current_value > 0) : ?>  
        var resetTimeout = function() {
            lh_inst.resetTimeoutActivity();
        };    
        
        <?php if ((int)erLhcoreClassModelChatConfig::fetch('track_mouse_activity')->current_value == 1) : ?> 
        this.addEvent(window,'mousemove',resetTimeout);     
        this.addEvent(document,'mousemove',resetTimeout);
        <?php endif;?>
            
        this.addEvent(window,'mousedown',resetTimeout);
        this.addEvent(window,'click',resetTimeout);
        this.addEvent(window,'scroll',resetTimeout);        
        this.addEvent(window,'keypress',resetTimeout);
        this.addEvent(window,'load',resetTimeout);    
        this.addEvent(document,'scroll',resetTimeout);        
        this.addEvent(document,'touchstart',resetTimeout);
        this.addEvent(document,'touchend',resetTimeout);
        this.resetTimeoutActivity();
        <?php endif;?> 
    },  
       
    handleMessage : function(e) {
        if (typeof e.data !== 'string') { return; }
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
    	} else if (action == 'lhc_ch') {
    		var parts = e.data.split(':');
    		if (parts[1] != '' && parts[2] != '') {
    			lh_inst.addCookieAttribute(parts[1],parts[2]);
    		}
    	} else if (action == 'lhc_open_restore') {    		
    		lh_inst.lh_openchatWindow();    		
    	} else if (action == 'lhc_continue_chat') {    		
    		lh_inst.showStartWindow();    		
    	} else if (action == 'lhc_cfrefresh') {    		
    		lh_inst.refreshCustomFields();    		
    	} else if (action == 'lhc_screenshot') {
    		lh_inst.makeScreenshot();
    	} else if (action == 'lhc_disable_survey') {
    		lh_inst.surveyShown = true;
    	} else if (action == 'lhc_chat_closed_explicit') {    	  
    	    lh_inst.explicitClose = true;
    		lh_inst.hide();
    	} else if (action == 'lhc_chat_closed') {
    	    var parts = e.data.split(':');
    	    parts.shift();
    	    if (parts.length > 0){
    	       lh_inst.appendArg(parts);
    	    }    	    
    		lh_inst.showSurvey();
    	} else if (action == 'lhc_cobrowse') {
    		lh_inst.startCoBrowse(e.data.split(':')[1],'chat');    	
    	} else if (action == 'lhc_cobrowse_online') {    		    		
    		lh_inst.startCoBrowse(e.data.split(':')[1],'onlineuser');    			
    	} else if (action == 'lhc_chat_redirect') {
    		document.location = e.data.split(':')[1].replace(new RegExp('__SPLIT__','g'),':');
    	} else if (action == 'lhc_cobrowse_cmd') {
    		if (lh_inst.cobrowser !== null){
    		lh_inst.cobrowser.handleMessage(e.data.split(':'));
    		};
    	} else if (action == 'lhc_lang') {
    		var lang = e.data.split(':')[1];
    		if (lang != undefined) {    				
    			lh_inst.addCookieAttributePersistent('lng',lang);
    			lh_inst.lang = lang;
    		} else {
    			lh_inst.addCookieAttributePersistent('lng','');
    			lh_inst.lang = '';
    		}
    	} else if (action == 'lh_callback') {
    		var functionName = e.data.split(':')[1];
    		lh_inst.genericCallback(functionName);    	
    	} else if (action == 'lhc_close') {
    		lh_inst.hide();
                lh_inst.chatClosedCallback('message')
    	} <?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/handlemessage_multiinclude.tpl.php')); ?>
    }
};

<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/lhc_chat_multiinclude.tpl.php')); ?>	

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

<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/lhc_chat_after_cookie_multiinclude.tpl.php')); ?>

lh_inst.initSessionStorage();
lh_inst.initLanguage();

<?php if ($referrer != '') : ?>
lh_inst.storeReferrer(<?php echo json_encode($referrer)?>);
<?php endif; ?>


<?php if (!($isOnlineHelp == false && $hide_offline == 'true')) : ?>
	
	lh_inst.showStatusWidget();
		
	if (lh_inst.cookieData.hash) {
		lh_inst.stopCheckNewMessage();
		lh_inst.substatus = '_reopen';	
		lh_inst.toggleStatusWidget(true);
	    lh_inst.showStartWindow(undefined,true);
	    <?php if (($track_online_users == true || $trackOnline == true) && $disable_online_tracking == false) : ?>
	    lh_inst.logPageView();
	    lh_inst.online_tracked = true;
	    <?php endif;?>
	}
	
	<?php if ($check_operator_messages == 'true' && $disable_pro_active == false && $disable_online_tracking == false) : ?>
	if (!lh_inst.cookieData.hash) {
		lh_inst.startNewMessageCheck();
		lh_inst.online_tracked = true;
	}
	<?php endif; ?>
	
	<?php if ($disable_pro_active == false && $track_online_users == true && $disable_online_tracking == false) : ?>
	if (!lh_inst.cookieData.hash) {
		lh_inst.startNewMessageCheckSingle();
		lh_inst.online_tracked = true;
	}
	<?php endif;?>
	
	<?php if ($trackOnline == true && $disable_online_tracking == false) : ?>
	if (lh_inst.online_tracked == false) {
		lh_inst.logPageView();
	};
	<?php endif;?>
		
	if (lh_inst.cookieData.shr) {
		lh_inst.startCoBrowse(lh_inst.cookieData.shr);
	};
	
<?php elseif ($track_online_users == true) : ?>
	lh_inst.logPageView();
<?php endif;?>

lh_inst.checkStatusChat();
lh_inst.attatchActivityListeners();

<?php
endif; // hide if offline
exit; ?>