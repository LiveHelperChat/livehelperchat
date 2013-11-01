<?php

$positionArgument = array (
		'bottom_left' => array (
				'radius' => 'right',
				'position' => 'bottom:0;left:0;',
				'position_body' => 'bottom:0;left:0;',
				'shadow' => '2px -2px 5px',
				'moz_radius' => 'topright',
				'widget_hover' => '',
				'padding_text' => '10px 10px 10px 35px',
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
				'padding_text' => '10px 10px 10px 35px',
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
				'widget_hover' => 'right:0;transition: 1s;',
				'moz_radius' => 'topleft',
				'padding_text' => '10px 10px 10px 35px',
				'border_widget' => 'border:1px solid #e3e3e3;border-right:0;',
				'background_position' => '0',
				'chrome_radius' => 'top-left',
				'widget_radius' => '-webkit-border-top-left-radius: 20px;-moz-border-radius-topleft: 20px;border-top-left-radius: 20px;      -webkit-border-bottom-left-radius: 20px;-moz-border-radius-bottomleft: 20px;border-bottom-left-radius: 20px;'
		),
		'middle_left' => array (
				'radius' => 'left',
				'position' => "top:{$top_pos}{$units};left:-155px;",
				'position_body' => "top:{$top_pos}{$units};left:0px;",
				'shadow' => '0px 0px 10px',
				'padding_text' => '10px 35px 10px 9px',
				'widget_hover' => 'left:0;transition: 1s;',
				'border_widget' => 'border:1px solid #e3e3e3;border-left:0;',
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

var lhc_FAQ = function() {
	var self = this;

	function addCss(css_content) {
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
   };

   function appendHTML(htmlStr) {
        var frag = document.createDocumentFragment(),
            temp = document.createElement('div');
        temp.innerHTML = htmlStr;
        while (temp.firstChild) {
            frag.appendChild(temp.firstChild);
        };
        return frag;
    };

	this.removeById = function(EId)
    {
        return(EObj=document.getElementById(EId))?EObj.parentNode.removeChild(EObj):false;
    };

    this.hide = function() {
        self.removeById('lhc_container_faq');
    };

	this.showVotingForm = function() {

   		  self.removeById('lhc_container_faq');

   		  this.initial_iframe_url = "//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('faq/faqwidget')?>"+'?URLReferer='+escape(document.location)+'&URLModule='+LHCFAQOptions.url;

   		  this.iframe_html = '<iframe id="lhcfaq_iframe" allowTransparency="true" scrolling="no" class="lhc-loading" frameborder="0" ' +
                       ( this.initial_iframe_url != '' ? ' src="'    + this.initial_iframe_url + '"' : '' ) +
                       ' width="520"' +
                       ' height="350"' +
                       ' style="width: 490px; height: 350px;"></iframe>';

          this.iframe_html = '<div id="lhc_container_faq">' +
                              '<div id="lhc_faq_header"><span id="lhc_faq_title"><a title="Powered by Live Helper Chat" href="http://livehelperchat.com" target="_blank"><img src="//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::design('images/icons/lhc.png');?>" alt="Live Helper Chat" /></a></span><a href="#" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>" id="lhc_faq_close"><img src="//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::design('images/icons/cancel.png');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>" /></a></div>' +
                              this.iframe_html + '</div>';

          raw_css = "#lhc_container_faq * {direction:<?php (erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language') == 'ltr' || erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language') == '') ? print 'ltr;text-align:left;' : print 'rtl;text-align:right;'; ?>;font-family:arial\;line-height:100%\;font-size:12px\;box-sizing: content-box\;-moz-box-sizing:content-box;padding:0;margin:0;}\n#lhc_container_faq img {border:0;}\n#lhc_faq_title{float:left;}\n#lhc_faq_header{position:relative;z-index:9999;height:15px;overflow:hidden;-webkit-border-<?php echo $currentPosition['chrome_radius']?>-radius: 10px;-moz-border-radius-<?php echo $currentPosition['moz_radius']?>: 10px;border-<?php echo $currentPosition['chrome_radius']?>-radius: 10px;background-color:#FFF;text-align:right;clear:both;border-bottom:1px solid #CCC;padding:5px;}\n#lhc_faq_close{padding:2px;float:right;}\n#lhc_faq_close:hover{background:#e5e5e5;}\n#lhc_container_faq {height:350px;overflow: hidden;transition-property: height;transition-duration: 0.4s;-webkit-transition: height 0.4s ease-in-out;transition: height 0.4s;background-color:#FFF\;width:490px;\nz-index:9999;\n position: fixed;<?php echo $currentPosition['position_body']?>;-webkit-box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);-moz-box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);border:1px solid #CCC;-webkit-border-<?php echo $currentPosition['chrome_radius']?>-radius: 10px;-moz-border-radius-<?php echo $currentPosition['moz_radius']?>: 10px;border-<?php echo $currentPosition['chrome_radius']?>-radius: 10px; }\n#lhc_container_faq iframe{transition-property: height;transition-duration: 0.4s;-webkit-transition: height 0.4s ease-in-out;transition: height 0.4s;}\n#lhc_container_faq iframe.lhc-loading{\nbackground: #FFF url(//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::design('images/general/loading.gif');?>) no-repeat center center; }";

          addCss(raw_css);

          var fragment = appendHTML(this.iframe_html);
          document.body.insertBefore(fragment, document.body.childNodes[0]);

          document.getElementById('lhc_faq_close').onclick = function() { self.hide(); return false; };
    };

    function showStatusWidget() {
       var statusTEXT = '<a id="faq-icon" class="status-icon" href="#" onclick="return lh_inst.lh_openchatWindow()" >'+LHCFAQOptions.status_text+'</a>';
       var raw_css = "#lhc_faq_container * {direction:<?php (erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language') == 'ltr' || erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language') == '') ? print 'ltr;text-align:left;' : print 'rtl;text-align:right;'; ?>;font-family:arial;line-height:100%;font-size:12px;box-sizing: content-box;zoom:1;margin:0;padding:0;}\n#lhc_faq_container .status-icon{text-decoration:none;font-size:12px;font-weight:bold;color:#000;display:block;padding:<?php echo $currentPosition['padding_text']?>;background:url('//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::design('images/icons/help.png');?>') no-repeat <?php echo $currentPosition['background_position']?> center}\n#lhc_faq_container:hover{<?php echo $currentPosition['widget_hover']?>}\n#lhc_faq_container{box-sizing: content-box;<?php echo $currentPosition['widget_radius']?>-webkit-box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);-moz-box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);box-shadow: <?php echo $currentPosition['shadow']?> rgba(50, 50, 50, 0.17);<?php echo $currentPosition['border_widget']?>;padding:5px 0px 3px 5px;width:190px;font-family:arial;font-size:12px;transition: 1s;position:fixed;<?php echo $currentPosition['position']?>;background-color:#f6f6f6;z-index:9998;}\n";
       addCss(raw_css);
       var htmlStatus = '<div id="lhc_faq_container">'+statusTEXT+'</div>';
       var fragment = appendHTML(htmlStatus);
       document.body.insertBefore(fragment, document.body.childNodes[0]);
       document.getElementById('faq-icon').onclick = function() { self.showVotingForm(); return false; };
   };

   this.handleMessage = function(e) {
    	var action = e.data.split(':')[0];
    	if (action == 'lhc_sizing_faq') {
    		var height = e.data.split(':')[1];
    		var elementObject = document.getElementById('lhcfaq_iframe');
    		var iframeContainer = document.getElementById('lhc_container_faq');
    		elementObject.height = height;
    		elementObject.style.height = height+'px';
    		iframeContainer.className = iframeContainer.className;
    		iframeContainer.style.height = (parseInt(height)+26)+'px';
    	};
   };

   showStatusWidget();
};

var lhcFaq = new lhc_FAQ();

if ( window.attachEvent ) {
	// IE
	window.attachEvent("onmessage",function(e){lhcFaq.handleMessage(e);});
};

if ( document.attachEvent ) {
	// IE
	document.attachEvent("onmessage",function(e){lhcFaq.handleMessage(e);});
};

if ( window.addEventListener ){
	// FF
	window.addEventListener("message",function(e){lhcFaq.handleMessage(e);}, false);
};


