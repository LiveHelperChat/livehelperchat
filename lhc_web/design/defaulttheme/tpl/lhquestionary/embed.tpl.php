var lhc_QuestionaryPage = function() {
	var self = this;

	this.showVotingForm = function() {
		  var locationCurrent = encodeURIComponent(window.location.href.substring(window.location.protocol.length));
   		  this.initial_iframe_url = "<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('questionary/votingwidget')?>/(mode)/embed<?php $theme !== false ? print '/(theme)/'.$theme : ''?>"+'?URLReferer='+locationCurrent;

   		  this.iframe_html = '<iframe id="lhc_sizing_questionary_page" allowTransparency="true" scrolling="no" frameborder="0" ' +
                       ( this.initial_iframe_url != '' ? ' src="'    + this.initial_iframe_url + '"' : '' ) +
                       ' width="100%"' +
                       ' height="300"' +
                       ' style="width: 100%; height: 300px;"></iframe>';

          document.getElementById('lhc_questionary_embed_container').innerHTML = this.iframe_html;
    };

    this.handleMessage = function(e) {
    	var action = e.data.split(':')[0];
    	if (action == 'lhc_sizing_questionary_page') {
    		var height = e.data.split(':')[1];
    		var elementObject = document.getElementById('lhc_sizing_questionary_page');
    		elementObject.height = height;
    		elementObject.style.height = height+'px';
    	};
    };

    this.showVotingForm();
};

var lhcQuestionaryPage = new lhc_QuestionaryPage();

if ( window.attachEvent ) {
	// IE
	window.attachEvent("onmessage",function(e){lhcQuestionaryPage.handleMessage(e);});
};

if ( document.attachEvent ) {
	// IE
	document.attachEvent("onmessage",function(e){lhcQuestionaryPage.handleMessage(e);});
};

if ( window.addEventListener ){
	// FF
	window.addEventListener("message",function(e){lhcQuestionaryPage.handleMessage(e);}, false);
};