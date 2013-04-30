var lhc_ChatboxPage = function() {
	var self = this;

	this.showVotingForm = function() {

   		  this.initial_iframe_url = "<?php echo erLhcoreClassSystem::instance()->baseHTTP?><?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chatbox/chatwidget')?>/(chat_height)/<?php echo $heightchatcontent;?>/(mode)/embed/(identifier)/"+LHCChatboxOptionsEmbed.identifier+'/(hashchatbox)/'+LHCChatboxOptionsEmbed.hashchatbox+'?URLReferer='+escape(document.location);

   		  this.iframe_html = '<iframe id="lhc_sizing_chatbox_page" allowTransparency="true" scrolling="no" frameborder="0" ' +
                       ( this.initial_iframe_url != '' ? ' src="'    + this.initial_iframe_url + '"' : '' ) +
                       ' width="100%"' +
                       ' height="300"' +
                       ' style="width: 100%; height: 300px;"></iframe>';

          document.getElementById('lhc_chatbox_embed_container').innerHTML = this.iframe_html;
    };

    this.handleMessage = function(e) {
    	var action = e.data.split(':')[0];
    	if (action == 'lhc_sizing_chatbox_page') {
    		var height = e.data.split(':')[1];
    		var elementObject = document.getElementById('lhc_sizing_chatbox_page');
    		elementObject.height = height;
    		elementObject.style.height = height+'px';
    	};
    };

    this.showVotingForm();
};

var lhcChatboxPage = new lhc_ChatboxPage();

if ( window.attachEvent ) {
	// IE
	window.attachEvent("onmessage",function(e){lhcChatboxPage.handleMessage(e);});
};

if ( document.attachEvent ) {
	// IE
	document.attachEvent("onmessage",function(e){lhcChatboxPage.handleMessage(e);});
};

if ( window.addEventListener ){
	// FF
	window.addEventListener("message",function(e){lhcChatboxPage.handleMessage(e);}, false);
};