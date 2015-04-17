<h1 class="attr-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','HTML code');?></h1>

<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/htmlcode','Status text');?></label> <input type="text" class="form-control" id="id_status_text" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Help us to grow');?>" />
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label for="id_show_widget_on_open"><input type="checkbox" id="id_show_widget_on_open" value="on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/htmlcode','Expand the widget automatically for new users');?></label>

		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Position from the top, only used if the Middle left or the Middle right side is chosen');?></label>
			<div class="row">
				<div class="col-md-8 columns">
					<input type="text" class="form-control" id="id_top_text" value="400" />
				</div>
				<div class="col-md-4 columns">
					<select id="UnitsTop" class="form-control">
						<option value="pixels">Pixels</option>
						<option value="percents">Percents</option>
					</select>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Size');?></label>
			<div class="row">
				<div class="col-md-6">
					<input type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Width')?>" id="id_width_text" value="300" />
				</div>
				<div class="col-md-6">
					<input type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Height')?>" id="id_height_text" value="300" />
				</div>
			</div>
		</div>
	</div>
</div>

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
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Position');?></label> <select id="PositionID" class="form-control">
				<option value="bottom_right"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Bottom right corner of the screen');?></option>
				<option value="bottom_left"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Bottom left corner of the screen');?></option>
				<option value="middle_right"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Middle right side of the screen');?></option>
				<option value="middle_left"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Middle left side of the screen');?></option>
			</select>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label><input type="checkbox" id="id_disable_responsive" value="on"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Disable responsive layout for status widget.');?></label>
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

<p class="explain"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/htmlcode','Copy the code from the text area to the footer, before the closing &lt;/body&gt; tag');?></p>
<textarea style="width: 100%; height: 180px; font-size: 12px;" id="HMLTContent"></textarea>

<script type="text/javascript">
var default_site_access = '<?php echo erConfigClassLhConfig::getInstance()->getSetting( 'site', 'default_site_access' ); ?>/';

function generateEmbedCode(){
    var siteAccess = $('#LocaleID').val() == default_site_access ? '' : $('#LocaleID').val();
    var id_position =  '/(position)/'+$('#PositionID').val();
    var id_show_widget_on_open = $('#id_show_widget_on_open').is(':checked') ? '/(expand)/true' : '';
	var textStatus = $('#id_status_text').val();
	var top = '/(top)/'+($('#id_top_text').val() == '' ? 400 : $('#id_top_text').val());
	var topposition = '/(units)/'+$('#UnitsTop').val();
	var widthwidget = '/(width)/'+($('#id_width_text').val() == '' ? 300 : $('#id_width_text').val());
	var heightwidget = '/(height)/'+($('#id_height_text').val() == '' ? 300 : $('#id_height_text').val());
	var id_disable_responsive = $('#id_disable_responsive').is(':checked') ? '/(noresponse)/true' : '';
	var id_theme = $('#ThemeID').val() > 0 ? '/(theme)/'+$('#ThemeID').val() : '';
	
	<?php include(erLhcoreClassDesign::designtpl('lhquestionary/getstatus/options_variable.tpl.php')); ?>
	
    var script = '<script type="text/javascript">'+"\nvar <?php echo $questionaryOptionsVariable?> = {status_text:'"+textStatus+"'};\n"+
      '(function() {'+"\n"+
        'var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;'+"\n"+
        'po.src = \''+$('#HttpMode').val()+'//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurldirect()?>'+siteAccess+'questionary/getstatus'+id_position+id_disable_responsive+id_show_widget_on_open+top+topposition+widthwidget+id_theme+heightwidget+"';\n"+
        'var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);'+"\n"+
      '})();'+"\n"+
    '</scr'+'ipt>';
    $('#HMLTContent').text(script);
};
$('#LocaleID,#PositionID,#id_show_widget_on_open,#id_disable_responsive,#id_status_text,#UnitsTop,#id_top_text,#id_width_text,#id_height_text,#HttpMode,#ThemeID').change(function(){
    generateEmbedCode();
});
generateEmbedCode();
</script>