<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','HTML code');?></h1>

<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/htmlcode','Status text');?></label> <input class="form-control" type="text" id="id_status_text" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Chatbox');?>" />
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Chatbox messages content height');?></label> <input class="form-control" type="text" id="id_chat_height" value="220" />
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Position from the top, is only used if the Middle left or the Middle right side is chosen');?></label>
			<div class="row">
				<div class="col-md-8">
					<input class="form-control" type="text" id="id_top_text" value="300" />
				</div>
				<div class="col-md-4">
					<select class="form-control" id="UnitsTop">
						<option value="pixels"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Pixels');?></option>
						<option value="percents"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Percents');?></option>
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
					<input class="form-control" type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Width')?>" id="id_width_text" value="300" />
				</div>
				<div class="col-md-6">
					<input class="form-control" type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Height')?>" id="id_height_text" value="300" />
				</div>
			</div>
		</div>
	</div>
</div>


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
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Position');?></label> <select class="form-control" id="PositionID">
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

<label><input type="checkbox" id="DisableMiminize" value="on"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Disable minimize icon');?></label>
<br>
<label><input type="checkbox" id="ShowContent" value="on"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Show chatbox content instead of widget, users will be able only minimize, not close it.');?></label>
<br>
<label><input type="checkbox" id="ShowContentMinimized" value="on"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Show chatbox content minimized first time if content is shown.');?></label>
<br>


<p class="explain"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Copy the code from the text area to the footer, before the closing &lt;/body&gt; tag');?></p>
<textarea style="width: 100%; height: 180px; font-size: 12px;" id="HMLTContent"></textarea>

<script type="text/javascript">
var default_site_access = '<?php echo erConfigClassLhConfig::getInstance()->getSetting( 'site', 'default_site_access' ); ?>/';

function generateEmbedCode(){
    var siteAccess = $('#LocaleID').val() == default_site_access ? '' : $('#LocaleID').val();
    var id_position =  '/(position)/'+$('#PositionID').val();
	var textStatus = $('#id_status_text').val();
	var top = '/(top)/'+($('#id_top_text').val() == '' ? 300 : $('#id_top_text').val());
	var topposition = '/(units)/'+$('#UnitsTop').val();
	var widthwidget = '/(width)/'+($('#id_width_text').val() == '' ? 300 : $('#id_width_text').val());
	var heightwidget = '/(height)/'+($('#id_height_text').val() == '' ? 300 : $('#id_height_text').val());
	var chat_height = '/(chat_height)/'+($('#id_chat_height').val() == '' ? 220 : $('#id_chat_height').val());	
	var show_content = ($('#ShowContent').is(':checked') ? '/(sc)/true' : '');
	var show_min = ($('#ShowContentMinimized').is(':checked')? '/(scm)/true' : '');
	var dis_min = ($('#DisableMiminize').is(':checked')? '/(dmn)/true' : '');
	var id_disable_responsive = $('#id_disable_responsive').is(':checked') ? '/(noresponse)/true' : '';
	var id_theme = $('#ThemeID').val() > 0 ? '/(theme)/'+$('#ThemeID').val() : '';

	<?php include(erLhcoreClassDesign::designtpl('lhchatbox/getstatus/options_variable.tpl.php')); ?>
	
    var script = '<script type="text/javascript">'+"\nvar <?php echo $chatboxOptionsVariable?> = {hashchatbox:'empty',identifier:'default',status_text:'"+textStatus+"'};\n"+
      '(function() {'+"\n"+
        'var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;'+"\n"+
        'po.src = \''+$('#HttpMode').val()+'//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurldirect()?>'+siteAccess+'chatbox/getstatus'+id_position+top+id_disable_responsive+topposition+widthwidget+id_theme+heightwidget+chat_height+show_content+show_min+dis_min+"';\n"+
        'var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);'+"\n"+
      '})();'+"\n"+
    '</scr'+'ipt>';
    $('#HMLTContent').text(script);
};
$('#LocaleID,#PositionID,#HttpMode,#id_status_text,#UnitsTop,#id_top_text,#id_width_text,#id_height_text,#ThemeID,#id_disable_responsive,#id_chat_height,#ShowContent,#ShowContentMinimized,#DisableMiminize').change(function(){
    generateEmbedCode();
});
generateEmbedCode();
</script>