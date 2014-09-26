<h1 class="attr-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','HTML code');?></h1>

<div class="row">
    <div class="columns large-6">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Popup width');?></label>
	    <div class="row">
	      <div class="large-8 columns">
	        <input type="text" id="id_size_text" value="450" />
	      </div>
	      <div class="large-4 columns">
	      	<select id="UnitsTop">
	            <option value="pixels">Pixels</option>
	            <option value="percents">Percents</option>
	        </select>
	      </div>
	    </div>
	</div>
    <div class="columns large-6">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Popup height, used only if iframe is used');?></label>	    
	    <input type="text" id="id_size_height" value="450" />	     
	</div>
	<div class="columns large-12">
    	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Identifier, this can be used as filter for pro active chat invitations and is use full having different messages for different domains. Only string without spaces or special characters.');?></label>
    	<input type="text" id="id_site_identifier" maxlength="50" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Leave empty if it is not important to you');?>" value="" />
	</div>
	
	<div class="columns large-6">
		<label for="id_show_overlay"><input type="checkbox" id="id_show_overlay" value="on" checked="checked"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/htmlcode','Show overlay');?></label>
	</div>
	
	<div class="columns large-6">
		<label for="id_canreopen"><input type="checkbox" id="id_canreopen" value="on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/htmlcode','Show different offers for the same user');?></label>
	</div>
	<div class="columns large-12">
    	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Offer timeout in days, after how many days show offers for the same visitor again, leave empty for session');?></label>
    	<input type="text" id="offer_timeout" maxlength="50" value="" />
	</div>
</div>



<div class="row">
    <div class="columns large-6">               
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','For what domain you are generating embed code?');?></label>
    	<input type="text" id="id_embed_domain" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','example.com');?>" value="" />
    	
    	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Choose prefered http mode');?></label>
		<select id="HttpMode">         
		      <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Based on site (default)');?></option>
		      <option value="http:">http:</option>
		      <option value="https:">https:</option>      
		</select>
    </div>  
    <div class="columns large-6 end">
	  	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Choose a language');?></label>
        <select id="LocaleID">
            <?php foreach ($locales as $locale ) : ?>
            <option value="<?php echo $locale?>/"><?php echo $locale?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<p class="explain"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/htmlcode','Copy the code from the text area to the page header or footer');?></p>
<textarea style="width:100%;height:180px;font-size:12px;" id="HMLTContent" ></textarea>

<script type="text/javascript">
var default_site_access = '<?php echo erConfigClassLhConfig::getInstance()->getSetting( 'site', 'default_site_access' ); ?>/';

function generateEmbedCode(){
    var siteAccess = $('#LocaleID').val() == default_site_access ? '' : $('#LocaleID').val();
	var size = '/(size)/'+($('#id_size_text').val() == '' ? 400 : $('#id_size_text').val());
	var topposition = '/(units)/'+$('#UnitsTop').val();
	var id_identifier = $('#id_site_identifier').val() != '' ? '/(identifier)/'+$('#id_site_identifier').val() : '';
	var id_timeout = $('#offer_timeout').val() != '' ? '/(timeout)/'+$('#offer_timeout').val() : '';
	var id_size_height = $('#id_size_height').val() != '' ? '/(height)/'+$('#id_size_height').val() : '';
	var id_show_overlay = $('#id_show_overlay').is(':checked') ? '/(showoverlay)/true' : '';
	var id_canreopen = $('#id_canreopen').is(':checked') ? '/(canreopen)/true' : '';
	var id_embed_domain = $('#id_embed_domain').val() != '' ? 'domain:\''+$('#id_embed_domain').val()+'\'' : '';
	
    var script = '<script type="text/javascript">'+"\nvar LHCBROWSEOFFEROptions = {"+id_embed_domain+"};\n"+
    
      '(function() {'+"\n"+
        'var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;'+"\n"+
        'var refferer = (document.referrer) ? encodeURIComponent(document.referrer.substr(document.referrer.indexOf(\'://\')+1)) : \'\';'+"\n"+
        'var location  = (document.location) ? encodeURIComponent(window.location.href.substring(window.location.protocol.length)) : \'\';'+"\n"+
        'po.src = \''+$('#HttpMode').val()+'//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurldirect()?>'+siteAccess+'browseoffer/getstatus'+size+id_size_height+topposition+id_timeout+id_show_overlay+id_identifier+id_canreopen+'?r=\'+refferer+\'&l=\'+location;'+"\n"+
        'var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);'+"\n"+
      '})();'+"\n"+
    '</scr'+'ipt>';
    $('#HMLTContent').text(script);
};
$('#LocaleID,#id_embed_domain,#UnitsTop,#id_size_text,#HttpMode,#id_site_identifier,#id_size_height,#id_show_overlay,#id_canreopen,#offer_timeout').change(function(){
    generateEmbedCode();
});
generateEmbedCode();

</script>