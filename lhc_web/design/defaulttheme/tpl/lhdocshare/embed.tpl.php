var lhc_<?php echo $doc_id?>_FormEmbed = function() {
	var self = this;
	this.showVotingForm = function() {
   		  this.initial_iframe_url = "<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('docshare/docwidget')?>/<?php echo $doc_id?>";
   		  this.iframe_html = '<iframe id="lhcform_iframe_embed_<?php echo $doc_id?>" allowTransparency="true" scrolling="no" class="loading" frameborder="0" ' +
                       ( this.initial_iframe_url != '' ? ' src="'    + this.initial_iframe_url + '"' : '' ) +
                       ' width="100%"' +
                       ' height="<?php echo $height?>"' +
                       ' style="width: 100%; height: <?php echo $height?>px;"></iframe>';

		  document.getElementById('lhc_form_embed_container_<?php echo $doc_id?>').innerHTML = this.iframe_html;
    };

    this.handleMessage = function(e) {
    	var action = e.data.split(':')[0];
    	if (action == 'lhc_sizing_doc_embed_<?php echo $doc_id?>') {
    		var height = e.data.split(':')[1];    		
    		var elementObject = document.getElementById('lhcform_iframe_embed_<?php echo $doc_id?>');
    		elementObject.height = height;
    		elementObject.style.height = height+'px';
    	};
   };
   this.showVotingForm();
};

var lhc_<?php echo $doc_id?>_FormEmbed = new lhc_<?php echo $doc_id?>_FormEmbed();

if ( window.attachEvent ) {
	// IE
	window.attachEvent("onmessage",function(e){lhc_<?php echo $doc_id?>_FormEmbed.handleMessage(e);});
};

if ( document.attachEvent ) {
	// IE
	document.attachEvent("onmessage",function(e){lhc_<?php echo $doc_id?>_FormEmbed.handleMessage(e);});
};

if ( window.addEventListener ){
	// FF
	window.addEventListener("message",function(e){lhc_<?php echo $doc_id?>_FormEmbed.handleMessage(e);}, false);
};