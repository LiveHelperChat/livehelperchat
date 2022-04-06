<?php

$positionArgument = array (
		'bottom_left' => array (
				'radius' => 'right',
				'position' => 'bottom:0;left:0;',
				'position_body' => 'bottom:0;left:0;',
				'shadow' => '1px -1px 5px',
				'moz_radius' => 'topright',
				'widget_hover' => '',
				'padding_text' => '10px 10px 10px 35px',
				'chrome_radius' => 'top-right',
				'border_widget' => 'border:1px solid #'.($theme !== false ? $theme->bor_bcolor : 'e3e3e3').';border-left:0;border-bottom:0;',
				'background_position' => '0',
				'widget_radius' => '-webkit-border-top-right-radius: 20px;-moz-border-radius-topright: 20px;border-top-right-radius: 20px;',
				'posv' => 'b',
				'pos' => 'l',
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
				'background_position' => 'left',
				'chrome_radius' => 'top-left',
				'border_widget' => 'border:1px solid #'.($theme !== false ? $theme->bor_bcolor : 'e3e3e3').';border-right:0;border-bottom:0;',
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
				'border_widget' => 'border:1px solid #'.($theme !== false ? $theme->bor_bcolor : 'e3e3e3').';border-left:0;',
				'moz_radius' => 'topright',
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

<?php include(erLhcoreClassDesign::designtpl('lhfaq/getstatus/options_variable.tpl.php')); ?>

var lhc_FAQ = {
	JSON : {
            parse: window.JSON && (window.JSON.parse || window.JSON.decode) || String.prototype.evalJSON && function(str){return String(str).evalJSON();} || $.parseJSON || $.evalJSON,
            stringify:  Object.toJSON || window.JSON && (window.JSON.stringify || window.JSON.encode) || $.toJSON
    },
    is_dragging : false,
    offset_data : '',
	cookieData : {},
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
        this.removeById('lhc_container_faq');
    },
    
	addEvent : (function () {
	  if (document.addEventListener) {
	    return function (el, type, fn) {
	      if (el && el.nodeName || el === window) {
	        el.addEventListener(type, fn, false);
	      } else if (el && el.length) {
	        for (var i = 0; i < el.length; i++) {
	          lhc_FAQ.addEvent(el[i], type, fn);
	        }
	      }
	    };
	  } else {
	    return function (el, type, fn) {
	      if (el && el.nodeName || el === window) {
	        el.attachEvent('on' + type, function () { return fn.call(el, window.event); });
	      } else if (el && el.length) {
	        for (var i = 0; i < el.length; i++) {
	          lhc_FAQ.addEvent(el[i], type, fn);
	        }
	      }
	    };
	  }
	})(),
	
	showVotingForm : function() {

   		  this.removeById('lhc_container_faq');

   		  var locationCurrent = encodeURIComponent(window.location.href.substring(window.location.protocol.length));
   		  this.initial_iframe_url = "<?php echo erLhcoreClassSystem::getHost()?><?php echo erLhcoreClassDesign::baseurl('faq/faqwidget')?><?php $theme !== false ? print '/(theme)/'.$theme->id : ''?>"+'?URLReferer='+locationCurrent+'&URLModule='+encodeURIComponent(<?php echo $faqOptionsVariable;?>.url)+'&identifier='+encodeURIComponent(<?php echo $faqOptionsVariable;?>.identifier);

   		  if (window.innerWidth < 1024) {
          		window.open(this.initial_iframe_url,"_blank");
          		return;
          };
          
   		  this.iframe_html = '<iframe id="lhcfaq_iframe" allowTransparency="true" scrolling="no" class="lhc-loading" frameborder="0" ' +
                       ( this.initial_iframe_url != '' ? ' src="'    + this.initial_iframe_url + '"' : '' ) +
                       ' width="520"' +
                       ' height="350"' +
                       ' style="width: 490px; height: 350px;"></iframe>';

          this.iframe_html = '<div id="lhc_container_faq">' +
                              '<div id="lhc_faq_header"><?php include(erLhcoreClassDesign::designtpl('lhchat/widget_brand/faq.tpl.php')); ?><a href="#" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>" id="lhc_faq_close"><img src="<?php if ($theme !== false && $theme->close_image_url != '') : ?><?php echo $theme->close_image_url;?><?php else : ?><?php echo erLhcoreClassSystem::getHost()?><?php echo erLhcoreClassDesign::design('images/icons/cancel.png');?><?php endif;?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>" /></a></div><div id="lhc_iframe_container">' +
                              this.iframe_html + '</div></div>';

          raw_css = "#lhc_container_faq * {direction:<?php (erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language') == 'ltr' || erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language') == '') ? print 'ltr;text-align:left;' : print 'rtl;text-align:right;'; ?>;font-family:arial\;line-height:100%\;font-size:12px\;box-sizing: content-box\;-moz-box-sizing:content-box;padding:0;margin:0;}\n#lhc_container_faq img {border:0;}\n#lhc_faq_title{float:left;}\n#lhc_faq_header{position:relative;z-index:9990;height:<?php ($theme !== false && $theme->header_height > 0) ? print $theme->header_height : print '15' ?>px;overflow:hidden;background-color:#<?php $theme !== false ? print $theme->header_background : print '525252' ?>;text-align:right;clear:both;padding:<?php ($theme !== false && $theme->header_padding > 0) ? print $theme->header_padding : print '5' ?>px;}\n#lhc_faq_close{padding:2px;float:right;}\n#lhc_faq_close:hover{opacity:0.4;}\n#lhc_container_faq {-moz-user-select:none; -khtml-user-drag:element;cursor: move;cursor: -moz-grab;cursor: -webkit-grab; overflow: hidden;transition-property: height;transition-duration: 0.4s;-webkit-transition: height 0.4s ease-in-out;transition: height 0.4s;background-color:#FFF\;width:490px;\nz-index:9990;\n position: fixed;<?php echo $currentPosition['position_body']?>;-webkit-box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);-moz-box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);-webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px; }\n#lhc_container_faq iframe{position:relative;display:block;transition-property: height;transition-duration: 0.4s;-webkit-transition: height 0.4s ease-in-out;transition: height 0.4s;}\n#lhc_container_faq iframe.lhc-loading{\nbackground: #FFF url(<?php echo erLhcoreClassSystem::getHost()?><?php echo erLhcoreClassDesign::design('images/general/loading.gif');?>) no-repeat center center; }\n#lhc_container_faq #lhc_iframe_container{border:<?php ($theme !== false && $theme->widget_border_width > 0) ? print $theme->widget_border_width : print '1' ?>px solid #<?php $theme !== false ? print $theme->widget_border_color : print 'cccccc' ?>;border-top: 0;border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;overflow: hidden;}\n@media only screen and (max-width : 640px) {#lhc_container_faq{margin-bottom:5px;position:relative;right:0;bottom:0;top:0;width:100%;}#lhc_container_faq iframe{width:100% !important}}";

          if (!this.cssWasAdded) {
          		this.cssWasAdded = true;
          		this.addCss(raw_css<?php ($theme !== false && $theme->custom_container_css !== '') ? print '+\''.str_replace(array("\n","\r"), '', $theme->custom_container_css).'\'' : '' ?>);
		  };

          var fragment = this.appendHTML(this.iframe_html);
          document.body.insertBefore(fragment, document.body.childNodes[0]);
		
		  var lhc_obj = this;			
          document.getElementById('lhc_faq_close').onclick = function() { lhc_obj.hide(); return false; };
           
          var domContainer = document.getElementById('lhc_container_faq');
          var domIframe = 'lhcfaq_iframe';
          var domContainerId = 'lhc_container_faq';
		  <?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/drag_drop_logic.tpl.php')); ?>	
          
          
    },

    showStatusWidget : function() {
       var statusTEXT = '<a id="faq-icon" class="status-icon" href="#" >'+<?php echo $faqOptionsVariable;?>.status_text+'</a>';
       var raw_css = "#lhc_faq_container * {direction:<?php (erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language') == 'ltr' || erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language') == '') ? print 'ltr;text-align:left;' : print 'rtl;text-align:right;'; ?>;font-family:arial;line-height:100%;font-size:12px;box-sizing: content-box;zoom:1;margin:0;padding:0;}\n#lhc_faq_container .status-icon{text-decoration:none;font-size:12px;font-weight:bold;color:#<?php $theme !== false ? print $theme->text_color : print '000' ?>;display:block;padding:<?php echo $currentPosition['padding_text']?>;background:url('<?php echo erLhcoreClassSystem::getHost()?><?php echo erLhcoreClassDesign::design('images/icons/help.png');?>') no-repeat <?php echo $currentPosition['background_position']?> center}\n#lhc_faq_container:hover{<?php echo $currentPosition['widget_hover']?>}\n#lhc_faq_container{box-sizing: content-box;<?php echo $currentPosition['widget_radius']?>-webkit-box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);-moz-box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);<?php echo $currentPosition['border_widget']?>;padding:5px 0px 3px 5px;width:190px;font-family:arial;font-size:12px;transition: 1s;position:fixed;<?php echo $currentPosition['position']?>;background-color:#<?php $theme !== false ? print $theme->onl_bcolor : print 'f6f6f6' ?>;z-index:9989;}\n<?php if ($noresponse == false) : ?>@media only screen and (max-width : 640px) {#lhc_faq_container{position:relative;top:0;right:0;bottom:0;left:0;width:auto;border-radius:2px;box-shadow:none;border:1px solid #e3e3e3;margin-bottom:5px;}}\n<?php endif;?>";
       this.addCss(raw_css<?php ($theme !== false && $theme->custom_status_css_front !== '') ? print '+\''.str_replace(array("\n","\r"), '', $theme->custom_status_css_front).'\'' : '' ?>);
       var htmlStatus = '<div id="lhc_faq_container">'+statusTEXT+'</div>';
       var fragment = this.appendHTML(htmlStatus);
       document.body.insertBefore(fragment, document.body.childNodes[0]);
       var inst = this;
       document.getElementById('faq-icon').onclick = function() { inst.showVotingForm(); return false; };
   },

   handleMessage : function(e) {
        if (typeof e.data !== 'string') { return; }
    	var action = e.data.split(':')[0];
    	if (action == 'lhc_sizing_faq') {
    		var height = e.data.split(':')[1];
    		var elementObject = document.getElementById('lhcfaq_iframe');
    		var iframeContainer = document.getElementById('lhc_container_faq');
    		elementObject.height = height;
    		elementObject.style.height = height+'px';
    		iframeContainer.className = iframeContainer.className;    		
    	};
   },
   
   addCookieAttribute : function() {
   
   }

};

lhc_FAQ.showStatusWidget();

if ( window.attachEvent ) {
	// IE
	window.attachEvent("onmessage",function(e){lhc_FAQ.handleMessage(e);});
};

if ( document.attachEvent ) {
	// IE
	document.attachEvent("onmessage",function(e){lhc_FAQ.handleMessage(e);});
};

if ( window.addEventListener ){
	// FF
	window.addEventListener("message",function(e){lhc_FAQ.handleMessage(e);}, false);
};


