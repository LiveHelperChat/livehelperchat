<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','HTML code');?></h1>

<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Choose a language');?></label> <select class="form-control" id="LocaleID">
            <?php foreach ($locales as $locale ) : ?>
            <option value="<?php echo $locale?>/"><?php echo $locale?></option>
            <?php endforeach; ?>
        </select>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Chatbox messages content height');?></label> <input class="form-control" type="text" id="id_chat_height" value="220" />
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Choose prefered http mode');?></label> <select class="form-control" id="HttpMode">
				<option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Based on site (default)');?></option>
				<option value="http:">http:</option>
				<option value="https:">https:</option>
			</select>
		</div>
	</div>
	<div class="col-md-6 end">
		<div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Theme')?></label> <select class="form-control" id="ThemeID">
				<option value="0"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Default');?></option>
			<?php foreach (erLhAbstractModelWidgetTheme::getList(array('limit' => 1000)) as $theme) : ?>
			   <option value="<?php echo $theme->id?>"><?php echo htmlspecialchars($theme->name)?></option>
			<?php endforeach; ?>
		</select>
		</div>
	</div>
</div>

<p class="explain"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Copy the code from the text area to the page where you want it to be rendered');?></p>

<textarea style="width: 100%; height: 180px; font-size: 12px;" id="HMLTContent"></textarea>

<script type="text/javascript">
var default_site_access = '<?php echo erConfigClassLhConfig::getInstance()->getSetting( 'site', 'default_site_access' ); ?>/';

function generateEmbedCode(){
    var siteAccess = $('#LocaleID').val() == default_site_access ? '' : $('#LocaleID').val();
    var chat_height = '/(chat_height)/'+($('#id_chat_height').val() == '' ? 220 : $('#id_chat_height').val());
    var id_theme = $('#ThemeID').val() > 0 ? '/(theme)/'+$('#ThemeID').val() : '';
    
    var id_tag = <?php include(erLhcoreClassDesign::designtpl('lhchatbox/embedcode_title.tpl.php'));?>+"\n"+'<div id="lhc_chatbox_embed_container" ></div>'+"\n\n"+<?php include(erLhcoreClassDesign::designtpl('lhchatbox/embedcode_title_after.tpl.php'));?>+"\n";

    <?php include(erLhcoreClassDesign::designtpl('lhchatbox/getstatus/options_variable_page.tpl.php')); ?>
    
    var script = '<script type="text/javascript">'+"\nvar <?php echo $chatboxOptionsVariablePage?> = {hashchatbox:'empty',identifier:'default'};\n"+
      '(function() {'+"\n"+
        'var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;'+"\n"+
        'po.src = \''+$('#HttpMode').val()+'//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurldirect()?>'+siteAccess+'chatbox/embed'+chat_height+id_theme+"';\n"+
        'var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);'+"\n"+
      '})();'+"\n"+
    '</scr'+'ipt>';
    $('#HMLTContent').text(id_tag+script);
};

$('#LocaleID,#id_chat_height,#HttpMode,#ThemeID').change(function(){
    generateEmbedCode();
});
generateEmbedCode();
</script>