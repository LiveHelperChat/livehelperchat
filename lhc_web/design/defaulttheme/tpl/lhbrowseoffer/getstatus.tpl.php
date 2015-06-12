<?php 
$trackDomain = erLhcoreClassModelChatConfig::fetch('track_domain')->current_value;
$disableHTML5Storage = (int)erLhcoreClassModelChatConfig::fetch('disable_html5_storage')->current_value;
?>

/*! Cookies.js - 0.4.0; Copyright (c) 2014, Scott Hamper; http://www.opensource.org/licenses/MIT */
(function(e){"use strict";var b=function(a,d,c){return 1===arguments.length?b.get(a):b.set(a,d,c)};b._document=document;b._navigator=navigator;b.defaults={path:"/"};b.get=function(a){b._cachedDocumentCookie!==b._document.cookie&&b._renewCache();return b._cache[a]};b.set=function(a,d,c){c=b._getExtendedOptions(c);c.expires=b._getExpiresDate(d===e?-1:c.expires);b._document.cookie=b._generateCookieString(a,d,c);return b};b.expire=function(a,d){return b.set(a,e,d)};b._getExtendedOptions=function(a){return{path:a&& a.path||b.defaults.path,domain:a&&a.domain||b.defaults.domain,expires:a&&a.expires||b.defaults.expires,secure:a&&a.secure!==e?a.secure:b.defaults.secure}};b._isValidDate=function(a){return"[object Date]"===Object.prototype.toString.call(a)&&!isNaN(a.getTime())};b._getExpiresDate=function(a,d){d=d||new Date;switch(typeof a){case "number":a=new Date(d.getTime()+1E3*a);break;case "string":a=new Date(a)}if(a&&!b._isValidDate(a))throw Error("`expires` parameter cannot be converted to a valid Date instance"); return a};b._generateCookieString=function(a,b,c){a=a.replace(/[^#$&+\^`|]/g,encodeURIComponent);a=a.replace(/\(/g,"%28").replace(/\)/g,"%29");b=(b+"").replace(/[^!#$&-+\--:<-\[\]-~]/g,encodeURIComponent);c=c||{};a=a+"="+b+(c.path?";path="+c.path:"");a+=c.domain?";domain="+c.domain:"";a+=c.expires?";expires="+c.expires.toUTCString():"";return a+=c.secure?";secure":""};b._getCookieObjectFromString=function(a){var d={};a=a?a.split("; "):[];for(var c=0;c<a.length;c++){var f=b._getKeyValuePairFromCookieString(a[c]); d[f.key]===e&&(d[f.key]=f.value)}return d};b._getKeyValuePairFromCookieString=function(a){var b=a.indexOf("="),b=0>b?a.length:b;try {return{key:decodeURIComponent(a.substr(0,b)),value:decodeURIComponent(a.substr(b+1))}} catch(e) {return{key:a.substr(0,b),value:a.substr(b+1)}}};b._renewCache=function(){b._cache=b._getCookieObjectFromString(b._document.cookie);b._cachedDocumentCookie=b._document.cookie};b._areEnabled=function(){var a="1"===b.set("cookies_lhc.js",1).get("cookies_lhc.js");b.expire("cookies_lhc.js");return a};b.enabled=b._areEnabled();window.lhc_Cookies=b})();

lhc_Cookies.defaults = {path:"/",secure: <?php erLhcoreClassModelChatConfig::fetch('use_secure_cookie')->current_value == 1 ? print 'true' : print 'false' ?>};

<?php include(erLhcoreClassDesign::designtpl('lhbrowseoffer/getstatus/options_variable.tpl.php')); ?>

var lhc_BrowseOffer = {
	JSON : {
            parse: window.JSON && (window.JSON.parse || window.JSON.decode) || String.prototype.evalJSON && function(str){return String(str).evalJSON();} || $.parseJSON || $.evalJSON,
            stringify:  Object.toJSON || window.JSON && (window.JSON.stringify || window.JSON.encode) || $.toJSON
    },
    canOpenOffer : true,
    offset_data : {},    
	cookieData : {},
	cookieDataPers : {'of':[]},
	domain : false, 
	
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
     
        if (<?php echo $browseofferOptionsVariable;?>.closeCallback) {
        	<?php echo $browseofferOptionsVariable;?>.closeCallback(<?php echo $invite->callback_content?>);
        }            
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
                              '<div id="lhc_browseoffer_header"><a href="#" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>" id="lhc_browseoffer_close">&#xd7;</a></div>' +
                              this.iframe_html + '</div>';

         raw_css = "#lhcbrowseoffer_content{padding:5px;}#lhcbrowseoffer_content iframe,#lhcbrowseoffer_content video{width:100%;}\n#lhc_browseoffer-bg{position: fixed;height: 100%;width: 100%;background: #000;background: rgba(0, 0, 0, 0.45);z-index: 99;display: none;top: 0;left: 0;};#lhc_container_browseoffer * {direction:<?php (erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language') == 'ltr' || erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language') == '') ? print 'ltr;text-align:left;' : print 'rtl;text-align:right;'; ?>;font-family:arial\;line-height:100%\;font-size:12px\;box-sizing: content-box\;-moz-box-sizing:content-box;padding:0;margin:0;}\n#lhc_container_browseoffer img {border:0;}\n#lhc_browseoffer_title{float:left;}\n#lhc_browseoffer_header{position:relative;z-index:9990;height:24px;overflow:hidden;background-color:#FFF;text-align:right;clear:both;padding:2px;}\n#lhc_browseoffer_close,#lhc-copyright-link{color: #ccc;text-decoration:none;font-family:arial;line-height:0.5;font-size:42px;font-weight: bold;padding:2px;float:right;width:24px;height:24px;}\n#lhc-copyright-link{font-size:12px}\n#lhc_browseoffer_close:hover{color:#575555}\n#lhc_container_browseoffer {box-shadow: 0 0 10px rgba(0, 0, 0, 0.4);max-width:95%;max-height:95%;width:<?php echo $size,$units?>;top:-100%;margin-top:-100%;left:50%;-webkit-transition: top 1s ease-in-out;-moz-transition: top 1s ease-in-out;  -o-transition: top 1s ease-in-out;  transition: top 1s ease-in-out;overflow: hidden;margin-left:-100%;background-color:#FFF\;\nz-index:9990;\n position: fixed;border:1px solid #CCC;-moz-user-select:none; }\n#lhc_container_browseoffer iframe{transition-property: height;transition-duration: 0.4s;-webkit-transition: height 0.4s ease-in-out;transition: height 0.4s;}\n";

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
         
         if (<?php echo $browseofferOptionsVariable;?>.openCallback) {
        	<?php echo $browseofferOptionsVariable;?>.openCallback(<?php echo $invite->callback_content?>);
         }          
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
     
   getCookieDomain : function(domain) {    
    	 if (this.domain !== false) {
    	 	return this.domain;
    	 } else {    
	    	if (typeof <?php echo $browseofferOptionsVariable;?> != 'undefined' && typeof <?php echo $browseofferOptionsVariable;?>.domain != 'undefined') {
	    		this.domain = '.'+<?php echo $browseofferOptionsVariable;?>.domain;
	    	} else {    	
	    		this.domain = '.'+document.location.hostname.replace(/^(?:[a-z0-9\-\.]+\.)??([a-z0-9\-]+)?(\.com|\.net|\.org|\.biz|\.ws|\.in|\.me|\.co\.uk|\.co|\.org\.uk|\.ltd\.uk|\.plc\.uk|\.me\.uk|\.edu|\.mil|\.br\.com|\.cn\.com|\.eu\.com|\.hu\.com|\.no\.com|\.qc\.com|\.sa\.com|\.se\.com|\.se\.net|\.us\.com|\.uy\.com|\.ac|\.co\.ac|\.gv\.ac|\.or\.ac|\.ac\.ac|\.af|\.am|\.as|\.at|\.ac\.at|\.co\.at|\.gv\.at|\.or\.at|\.asn\.au|\.com\.au|\.edu\.au|\.org\.au|\.net\.au|\.id\.au|\.be|\.ac\.be|\.adm\.br|\.adv\.br|\.am\.br|\.arq\.br|\.art\.br|\.bio\.br|\.cng\.br|\.cnt\.br|\.com\.br|\.ecn\.br|\.eng\.br|\.esp\.br|\.etc\.br|\.eti\.br|\.fm\.br|\.fot\.br|\.fst\.br|\.g12\.br|\.gov\.br|\.ind\.br|\.inf\.br|\.jor\.br|\.lel\.br|\.med\.br|\.mil\.br|\.net\.br|\.nom\.br|\.ntr\.br|\.odo\.br|\.org\.br|\.ppg\.br|\.pro\.br|\.psc\.br|\.psi\.br|\.rec\.br|\.slg\.br|\.tmp\.br|\.tur\.br|\.tv\.br|\.vet\.br|\.zlg\.br|\.br|\.ab\.ca|\.bc\.ca|\.mb\.ca|\.nb\.ca|\.nf\.ca|\.ns\.ca|\.nt\.ca|\.on\.ca|\.pe\.ca|\.qc\.ca|\.sk\.ca|\.yk\.ca|\.ca|\.cc|\.ac\.cn|\.com\.cn|\.edu\.cn|\.gov\.cn|\.org\.cn|\.bj\.cn|\.sh\.cn|\.tj\.cn|\.cq\.cn|\.he\.cn|\.nm\.cn|\.ln\.cn|\.jl\.cn|\.hl\.cn|\.js\.cn|\.zj\.cn|\.ah\.cn|\.gd\.cn|\.gx\.cn|\.hi\.cn|\.sc\.cn|\.gz\.cn|\.yn\.cn|\.xz\.cn|\.sn\.cn|\.gs\.cn|\.qh\.cn|\.nx\.cn|\.xj\.cn|\.tw\.cn|\.hk\.cn|\.mo\.cn|\.cn|\.cx|\.cz|\.de|\.dk|\.fo|\.com\.ec|\.tm\.fr|\.com\.fr|\.asso\.fr|\.presse\.fr|\.fr|\.gf|\.gs|\.co\.il|\.net\.il|\.ac\.il|\.k12\.il|\.gov\.il|\.muni\.il|\.ac\.in|\.co\.in|\.org\.in|\.ernet\.in|\.gov\.in|\.net\.in|\.res\.in|\.is|\.it|\.ac\.jp|\.co\.jp|\.go\.jp|\.or\.jp|\.ne\.jp|\.ac\.kr|\.co\.kr|\.go\.kr|\.ne\.kr|\.nm\.kr|\.or\.kr|\.li|\.lt|\.lu|\.asso\.mc|\.tm\.mc|\.com\.mm|\.org\.mm|\.net\.mm|\.edu\.mm|\.gov\.mm|\.ms|\.nl|\.no|\.nu|\.pl|\.ro|\.org\.ro|\.store\.ro|\.tm\.ro|\.firm\.ro|\.www\.ro|\.arts\.ro|\.rec\.ro|\.info\.ro|\.nom\.ro|\.nt\.ro|\.se|\.si|\.com\.sg|\.org\.sg|\.net\.sg|\.gov\.sg|\.sk|\.st|\.tf|\.ac\.th|\.co\.th|\.go\.th|\.mi\.th|\.net\.th|\.or\.th|\.tm|\.to|\.com\.tr|\.edu\.tr|\.gov\.tr|\.k12\.tr|\.net\.tr|\.org\.tr|\.com\.tw|\.org\.tw|\.net\.tw|\.ac\.uk|\.uk\.com|\.uk\.net|\.gb\.com|\.gb\.net|\.vg|\.sh|\.kz|\.ch|\.info|\.ua|\.gov|\.name|\.pro|\.ie|\.hk|\.com\.hk|\.org\.hk|\.net\.hk|\.edu\.hk|\.us|\.tk|\.cd|\.by|\.ad|\.lv|\.eu\.lv|\.bz|\.es|\.jp|\.cl|\.ag|\.mobi|\.eu|\.co\.nz|\.org\.nz|\.net\.nz|\.maori\.nz|\.iwi\.nz|\.io|\.la|\.md|\.sc|\.sg|\.vc|\.tw|\.travel|\.my|\.se|\.tv|\.pt|\.com\.pt|\.edu\.pt|\.asia|\.fi|\.com\.ve|\.net\.ve|\.fi|\.org\.ve|\.web\.ve|\.info\.ve|\.co\.ve|\.tel|\.im|\.gr|\.ru|\.net\.ru|\.org\.ru|\.hr|\.com\.hr)$/, '$1$2');
	    	}
    	};    	
    	return this.domain;
    },
    
   storePersistenCookie : function(){
   		var parameters = {};
   		<?php ($trackDomain != '' || $disableHTML5Storage == 1) ? ($trackDomain != '' ? print 'parameters.domain = \'.'.$trackDomain.'\';' : print 'parameters.domain = this.getCookieDomain();') : ''?>
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
