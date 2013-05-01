<?php

$isOnlineHelp = erLhcoreClassChat::isOnline();

// Perhaps user do not want to show live help then it's offline
if ( !($isOnlineHelp == false && $hide_offline == 'true') ) : ?>
var lh_inst_page  = {

    hide : function() {
        var th = document.getElementsByTagName('head')[0];
        var s = document.createElement('script');
        s.setAttribute('type','text/javascript');
        s.setAttribute('src','<?php echo erLhcoreClassSystem::instance()->baseHTTP?><?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/chatwidgetclosed')?>');
        th.appendChild(s);
    },

    parseOptions : function() {
		argumentsQuery = new Array();

		if (typeof LHCChatOptionsPage != 'undefined') {
	    	if (typeof LHCChatOptionsPage.attr != 'undefined') {
	    		if (LHCChatOptionsPage.attr.length > 0){
					for (var index in LHCChatOptionsPage.attr) {
						argumentsQuery.push('name[]='+encodeURIComponent(LHCChatOptionsPage.attr[index].name)+'&value[]='+encodeURIComponent(LHCChatOptionsPage.attr[index].value)+'&type[]='+encodeURIComponent(LHCChatOptionsPage.attr[index].type)+'&size[]='+encodeURIComponent(LHCChatOptionsPage.attr[index].size));
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

    showStartWindow : function(url_to_open) {

         this.initial_iframe_url = "<?php echo erLhcoreClassSystem::instance()->baseHTTP?><?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/chatwidget')?>/(mode)/embed"+'?URLReferer='+escape(document.location)+this.parseOptions();

         this.iframe_html = '<iframe id="fdbk_iframe_page" allowTransparency="true" scrolling="no" class="loading" frameborder="0" ' +
                       ( this.initial_iframe_url != '' ? ' src="'    + this.initial_iframe_url + '"' : '' ) +
                       ' width="100%"' +
                       ' height="340"' +
                       ' style="width: 100%; height: 340x;"></iframe>';

		  document.getElementById('lhc_status_container_page').innerHTML = this.iframe_html;
    },

    handleMessage : function(e) {
    	var action = e.data.split(':')[0];
    	if (action == 'lhc_sizing_chat_page') {
    		var height = e.data.split(':')[1];
    		var elementObject = document.getElementById('fdbk_iframe_page');
    		elementObject.height = height;
    		elementObject.style.height = height+'px';
    	};
    }
};

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