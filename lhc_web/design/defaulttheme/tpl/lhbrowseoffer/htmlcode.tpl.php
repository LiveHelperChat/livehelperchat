<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','HTML code');?></h1>

<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Popup width');?></label>
			<div class="row">
				<div class="col-md-8">
					<input class="form-control" type="text" id="id_size_text" value="450" />
				</div>
				<div class="col-md-4">
					<select class="form-control" id="UnitsTop">
						<option value="pixels">Pixels</option>
						<option value="percents">Percents</option>
					</select>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Popup height, used only if iframe is used');?></label> <input type="text" class="form-control" id="id_size_height" value="450" />
		</div>
	</div>
	<div class="col-md-12">
		<div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Identifier, this can be used as filter for pro active chat invitations and is use full having different messages for different domains. Only string without spaces or special characters.');?></label> <input type="text" class="form-control" id="id_site_identifier" maxlength="50" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Leave empty if it is not important to you');?>" value="" />
		</div>
	</div>

	<div class="col-md-6">
		<div class="form-group">
			<label for="id_show_overlay"><input type="checkbox" id="id_show_overlay" value="on" checked="checked"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/htmlcode','Show overlay');?></label>
		</div>
	</div>

	<div class="col-md-6">
		<div class="form-group">
			<label for="id_canreopen"><input type="checkbox" id="id_canreopen" value="on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/htmlcode','Show different offers for the same user');?></label>
		</div>
	</div>
	<div class="col-md-12">
		<div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Offer timeout in days, after how many days show offers for the same visitor again, leave empty for session');?></label> <input type="text" class="form-control" id="offer_timeout" maxlength="50" value="" />
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','For what domain you are generating embed code?');?></label> 
			<input class="form-control" type="text" id="id_embed_domain" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','example.com');?>" value="" /> 
		</div>	
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
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Choose a language');?></label> <select class="form-control" id="LocaleID">
            <?php foreach ($locales as $locale ) : ?>
            <option value="<?php echo $locale?>/"><?php echo $locale?></option>
            <?php endforeach; ?>
        </select>
		</div>
	</div>
</div>

<p class="explain"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/htmlcode','Copy the code from the text area to the page header or footer');?></p>
<textarea style="width: 100%; height: 180px; font-size: 12px;" id="HMLTContent"></textarea>

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

	<?php include(erLhcoreClassDesign::designtpl('lhbrowseoffer/getstatus/options_variable.tpl.php')); ?>
	
    var script = '<script type="text/javascript">'+"\nvar <?php echo $browseofferOptionsVariable;?> = {"+id_embed_domain+"};\n"+
    
      '(function() {'+"\n"+
        'var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;'+"\n"+
        'var referrer = (document.referrer) ? encodeURIComponent(document.referrer.substr(document.referrer.indexOf(\'://\')+1)) : \'\';'+"\n"+
        'var location  = (document.location) ? encodeURIComponent(window.location.href.substring(window.location.protocol.length)) : \'\';'+"\n"+
        'po.src = \''+$('#HttpMode').val()+'//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurldirect()?>'+siteAccess+'browseoffer/getstatus'+size+id_size_height+topposition+id_timeout+id_show_overlay+id_identifier+id_canreopen+'?r=\'+referrer+\'&l=\'+location;'+"\n"+
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