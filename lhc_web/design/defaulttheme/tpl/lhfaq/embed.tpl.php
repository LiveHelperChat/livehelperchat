var lhc_FAQEmbed = function() {
	var self = this;
	this.showVotingForm = function() {
   		  this.initial_iframe_url = "<?php echo erLhcoreClassSystem::instance()->baseHTTP?><?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('faq/faqwidget')?>/(mode)/embed"+'?URLReferer='+escape(document.location)+'&URLModule='+LHCFAQOptions.url;
   		  this.iframe_html = '<iframe id="lhcfaq_iframe_embed" allowTransparency="true" scrolling="no" class="loading" frameborder="0" ' +
                       ( this.initial_iframe_url != '' ? ' src="'    + this.initial_iframe_url + '"' : '' ) +
                       ' width="100%"' +
                       ' height="350"' +
                       ' style="width: 100%; height: 350px;"></iframe>';

		  document.getElementById('lhc_faq_embed_container').innerHTML = this.iframe_html;
    };

    this.handleMessage = function(e) {
    	var action = e.data.split(':')[0];
    	if (action == 'lhc_sizing_faq_embed') {
    		var height = e.data.split(':')[1];
    		var elementObject = document.getElementById('lhcfaq_iframe_embed');
    		elementObject.height = height;
    		elementObject.style.height = height+'px';
    	};
   };
   this.showVotingForm();
};

var lhcFaqEmbed = new lhc_FAQEmbed();

if ( window.attachEvent ) {
	// IE
	window.attachEvent("onmessage",function(e){lhcFaqEmbed.handleMessage(e);});
};

if ( document.attachEvent ) {
	// IE
	document.attachEvent("onmessage",function(e){lhcFaqEmbed.handleMessage(e);});
};

if ( window.addEventListener ){
	// FF
	window.addEventListener("message",function(e){lhcFaqEmbed.handleMessage(e);}, false);
};


