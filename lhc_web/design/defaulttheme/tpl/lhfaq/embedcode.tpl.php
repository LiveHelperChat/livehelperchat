<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','HTML code');?></h1>

<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Choose a language');?></label> <select id="LocaleID" class="form-control">
            <?php foreach ($locales as $locale ) : ?>
            <option value="<?php echo $locale?>/"><?php echo $locale?></option>
            <?php endforeach; ?>
        </select>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Choose prefered http mode');?></label> <select id="HttpMode" class="form-control">
				<option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Based on site (default)');?></option>
				<option value="http:">http:</option>
				<option value="https:">https:</option>
			</select>
		</div>
	</div>
	<div class="col-md-6 end">
		<div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Theme')?></label> <select id="ThemeID" class="form-control">
				<option value="0"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Default');?></option>
			<?php foreach (erLhAbstractModelWidgetTheme::getList(array('limit' => 1000)) as $theme) : ?>
			   <option value="<?php echo $theme->id?>"><?php echo htmlspecialchars($theme->name)?></option>
			<?php endforeach; ?>
		</select>
		</div>
	</div>
</div>

<p class="explain"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Copy the code from the textarea to page where you want it to be rendered');?></p>

<textarea style="width: 100%; height: 180px; font-size: 12px;" id="HMLTContent"></textarea>

<script type="text/javascript">
var default_site_access = '<?php echo erConfigClassLhConfig::getInstance()->getSetting( 'site', 'default_site_access' ); ?>/';

function generateEmbedCode(){
    var siteAccess = $('#LocaleID').val() == default_site_access ? '' : $('#LocaleID').val();
    var id_theme = $('#ThemeID').val() > 0 ? '/(theme)/'+$('#ThemeID').val() : '';
    
    var id_tag = <?php include(erLhcoreClassDesign::designtpl('lhfaq/embedcode_title.tpl.php'));?>+"\n"+'<div id="lhc_faq_embed_container" ></div>'+"\n\n"+<?php include(erLhcoreClassDesign::designtpl('lhfaq/embedcode_title_after.tpl.php'));?>+"\n";
    
    <?php include(erLhcoreClassDesign::designtpl('lhfaq/getstatus/options_variable.tpl.php')); ?>
    
    var script = '<script type="text/javascript">'+"\nvar <?php echo $faqOptionsVariable;?> = {url:'replace_me_with_dynamic_url',identifier:''};\n"+
      '(function() {'+"\n"+
        'var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;'+"\n"+
        'po.src = \''+$('#HttpMode').val()+'//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurldirect()?>'+siteAccess+'faq/embed'+id_theme+"';\n"+
        'var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);'+"\n"+
      '})();'+"\n"+
    '</scr'+'ipt>';
    $('#HMLTContent').text(id_tag+script);
};

$('#LocaleID,#HttpMode,#ThemeID').change(function(){
    generateEmbedCode();
});
generateEmbedCode();
</script>