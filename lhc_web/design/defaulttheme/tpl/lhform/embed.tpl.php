<?php include(erLhcoreClassDesign::designtpl('lhform/getstatus/options_variable.tpl.php')); ?>

var lhc_FormEmbed = function() {
	var self = this;
	this.showVotingForm = function() {
   		  this.initial_iframe_url = "<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('form/formwidget')?>/<?php echo $form_id,$identifier?>";
   		  this.iframe_html = '<iframe id="lhcform_iframe_embed" allowTransparency="true" scrolling="no" class="loading" frameborder="0" ' +
                       ( this.initial_iframe_url != '' ? ' src="'    + this.initial_iframe_url + '"' : '' ) +
                       ' width="100%"' +
                       ' height="350"' +
                       ' style="width: 100%; height: 350px;"></iframe>';

		  document.getElementById('lhc_form_embed_container').innerHTML = this.iframe_html;
    };

    this.handleMessage = function(e) {
    	var action = e.data.split(':')[0];
    	if (action == 'lhc_sizing_form_embed') {
    		var height = e.data.split(':')[1];
    		var elementObject = document.getElementById('lhcform_iframe_embed');
    		elementObject.height = height;
    		elementObject.style.height = height+'px';
    	};
   };
   this.showVotingForm();
};

var lhcFormEmbed = new lhc_FormEmbed();

if ( window.attachEvent ) {
	// IE
	window.attachEvent("onmessage",function(e){lhcFormEmbed.handleMessage(e);});
};

if ( document.attachEvent ) {
	// IE
	document.attachEvent("onmessage",function(e){lhcFormEmbed.handleMessage(e);});
};

if ( window.addEventListener ){
	// FF
	window.addEventListener("message",function(e){lhcFormEmbed.handleMessage(e);}, false);
};