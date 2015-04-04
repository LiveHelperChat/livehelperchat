<?php include(erLhcoreClassDesign::designtpl('lhchatbox/getstatus/options_variable_page.tpl.php')); ?>

var lhc_ChatboxPage = {
	JSON : {
            parse: window.JSON && (window.JSON.parse || window.JSON.decode) || String.prototype.evalJSON && function(str){return String(str).evalJSON();} || $.parseJSON || $.evalJSON,
            stringify:  Object.toJSON || window.JSON && (window.JSON.stringify || window.JSON.encode) || $.toJSON
    },
	cookieData : {},

	showVotingForm : function() {
		  var locationCurrent = encodeURIComponent(window.location.href.substring(window.location.protocol.length));
   		  this.initial_iframe_url = "<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chatbox/chatwidget')?>/(chat_height)/<?php echo $heightchatcontent;?><?php $theme !== false ? print '/(theme)/'.$theme : ''?>/(mode)/embed/(identifier)/"+<?php echo $chatboxOptionsVariablePage;?>.identifier+'/(hashchatbox)/'+<?php echo $chatboxOptionsVariablePage;?>.hashchatbox+this.getAppendCookieArguments()+'?URLReferer='+locationCurrent+this.getAppendRequestArguments();

   		  this.iframe_html = '<iframe id="lhc_sizing_chatbox_page" allowTransparency="true" scrolling="no" frameborder="0" ' +
                       ( this.initial_iframe_url != '' ? ' src="'    + this.initial_iframe_url + '"' : '' ) +
                       ' width="100%"' +
                       ' height="300"' +
                       ' style="width: 100%; height: 300px;"></iframe>';

          document.getElementById('lhc_chatbox_embed_container').innerHTML = this.iframe_html;
    },

    getAppendRequestArguments : function() {
		    var nickOption = (typeof <?php echo $chatboxOptionsVariablePage;?>.nick !== 'undefined') ?  '&nick='+encodeURIComponent(<?php echo $chatboxOptionsVariablePage;?>.nick) : (this.cookieData.nick ? '&nick='+encodeURIComponent(this.cookieData.nick) : '');
		    var disableOption = (typeof <?php echo $chatboxOptionsVariablePage;?>.disable_nick_change !== 'undefined') ?  '&dnc=true' : '';
		    var chatboxName = (typeof <?php echo $chatboxOptionsVariablePage;?>.chatbox_name !== 'undefined') ?  '&chtbx_name='+encodeURIComponent(<?php echo $chatboxOptionsVariablePage;?>.chatbox_name) : '';
		    return nickOption+disableOption+chatboxName;
    },

	getAppendCookieArguments : function() {
		    var soundOption = this.cookieData.s ? '/(sound)/'+this.cookieData.s : '';
		    var nickOption = this.cookieData.n ? '/(nick)/'+this.cookieData.n : '';
		    return soundOption+nickOption;
    },

   handleMessage : function(e) {
    	var action = e.data.split(':')[0];
    	if (action == 'lhc_sizing_chatbox_page') {
    		var height = e.data.split(':')[1];
    		var elementObject = document.getElementById('lhc_sizing_chatbox_page');
    		elementObject.height = height;
    		elementObject.style.height = height+'px';
    	} else if (action == 'lhc_ch') {
    		var parts = e.data.split(':');
    		if (parts[1] != '' && parts[2] != '') {
    			lhc_ChatboxPage.addCookieAttribute(parts[1],parts[2]);
    		}
    	} else if (action == 'lhc_chb') {
    		var parts = e.data.split(':');
    		if (parts[1] != '' && parts[2] != '') {
    			lhc_ChatboxPage.addCookieAttribute(parts[1],parts[2]);
    		}
    	}
    },

    removeCookieAttr : function(attr){
    	if (this.cookieData[attr]) {
    		delete this.cookieData[attr];
    		this.storeSesCookie();
    	}
    },

   storeSesCookie : function(){
    	if (localStorage) {
    		localStorage.setItem('lhc_chb',this.JSON.stringify(this.cookieData));
    	}
   },

   initSessionStorage : function(){
    	if (localStorage && localStorage.getItem('lhc_chb')) {
    		this.cookieData = this.JSON.parse(localStorage.getItem('lhc_chb'));
    	}
   },

   addCookieAttribute : function(attr, value){
    	if (!this.cookieData[attr] || this.cookieData[attr] != value){
	    	this.cookieData[attr] = value;
	    	this.storeSesCookie();
    	}
   }
};

lhc_ChatboxPage.initSessionStorage();
lhc_ChatboxPage.showVotingForm();


if ( window.attachEvent ) {
	// IE
	window.attachEvent("onmessage",function(e){lhc_ChatboxPage.handleMessage(e);});
};

if ( document.attachEvent ) {
	// IE
	document.attachEvent("onmessage",function(e){lhc_ChatboxPage.handleMessage(e);});
};

if ( window.addEventListener ){
	// FF
	window.addEventListener("message",function(e){lhc_ChatboxPage.handleMessage(e);}, false);
};