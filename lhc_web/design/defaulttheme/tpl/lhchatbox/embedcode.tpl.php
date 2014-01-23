<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','HTML code');?></h1>

<div class="row">
    <div class="columns large-6">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Choose a language');?></label>
        <select id="LocaleID">
            <?php foreach ($locales as $locale ) : ?>
            <option value="<?php echo $locale?>/"><?php echo $locale?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="columns large-6">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Chatbox messages content height');?></label>
	    <input type="text" id="id_chat_height" value="220" />
	</div>
	<div class="columns large-6 end">
	   		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Choose prefered http mode');?></label>
		    <select id="HttpMode">         
		            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Based on site (default)');?></option>
		            <option value="http:">http:</option>
		            <option value="https:">https:</option>      
		    </select>    	    
    </div>
</div>

<p class="explain"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Copy the code from the text area to the page where you want it to be rendered');?></p>

<textarea style="width:100%;height:180px;font-size:12px;" id="HMLTContent" ></textarea>

<script type="text/javascript">
var default_site_access = '<?php echo erConfigClassLhConfig::getInstance()->getSetting( 'site', 'default_site_access' ); ?>/';

function generateEmbedCode(){
    var siteAccess = $('#LocaleID').val() == default_site_access ? '' : $('#LocaleID').val();
    var chat_height = '/(chat_height)/'+($('#id_chat_height').val() == '' ? 220 : $('#id_chat_height').val());

    var id_tag = '<!-- Place this tag where you want the Live Helper Chatbox module to render. -->'+"\n"+'<div id="lhc_chatbox_embed_container" ></div>'+"\n\n<!-- Place this tag after the Live Helper Chatbox module tag. -->\n";

    var script = '<script type="text/javascript">'+"\nvar LHCChatboxOptionsEmbed = {hashchatbox:'empty',identifier:'default'};\n"+
      '(function() {'+"\n"+
        'var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;'+"\n"+
        'po.src = \''+$('#HttpMode').val()+'//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurldirect()?>'+siteAccess+'chatbox/embed'+chat_height+"';\n"+
        'var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);'+"\n"+
      '})();'+"\n"+
    '</scr'+'ipt>';
    $('#HMLTContent').text(id_tag+script);
};

$('#LocaleID,#id_chat_height,#HttpMode').change(function(){
    generateEmbedCode();
});
generateEmbedCode();
</script>