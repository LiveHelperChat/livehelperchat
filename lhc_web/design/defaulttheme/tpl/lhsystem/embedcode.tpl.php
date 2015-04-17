<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','HTML code');?></h1>

<div role="tabpanel">

	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#general" aria-controls="general" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','General');?></a></li>
		<li role="presentation"><a href="#design" aria-controls="design" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Design');?></a></li>
		<?php include(erLhcoreClassDesign::designtpl('lhsystem/embed_tab_multiinclude.tpl.php'));?>
	</ul>

	<!-- Tab panes -->
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="general">
			<div class="row">
				<div class="col-md-6">
					<label><input type="checkbox" id="id_hide_then_offline" value="on"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Hide status when offline');?></label>
				</div>
				<div class="col-md-6">
					<label><input type="checkbox" id="id_show_leave_form" value="on"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Show a leave a message form when there are no online operators');?></label>
				</div>

				<div class="col-md-6">

					<div class="form-group">
						<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Choose a language');?></label> <select id="LocaleID" class="form-control">
            <?php foreach ($locales as $locale ) : ?>
            <option value="<?php echo $locale?>/"><?php echo $locale?></option>
            <?php endforeach; ?>
        </select>
					</div>


				</div>
				<div class="col-md-6 end">
					<div class="form-group">
						<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Choose prefered http mode');?></label> <select id="HttpMode" class="form-control">
							<option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Based on site (default)');?></option>
							<option value="http:">http:</option>
							<option value="https:">https:</option>
						</select>
					</div>
				</div>

				<div class="col-md-6">
					<div class="form-group">
						<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Department')?></label> <select id="DepartmentID" multiple="multiple" size="5" class="form-control">
							<option value="0"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Any');?></option>
			<?php foreach (erLhcoreClassModelDepartament::getList($departmentParams) as $departament) : ?>
			    <option value="<?php echo $departament->id?>"><?php echo htmlspecialchars($departament->name)?></option>
			<?php endforeach; ?>
		</select>
					</div>
				</div>

				<div class="col-md-6">
					<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Operator ID')?></label> <input type="text" id="id_operator" class="form-control" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','To what operator chat should be assigned automatically?')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','To what operator chat should be assigned automatically?')?>" value="" />
				</div>

			</div>
		</div>

		<div role="tabpanel" class="tab-pane" id="design">
			<div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Theme')?></label> <select id="ThemeID" class="form-control">
					<option value="0"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Default');?></option>
        				<?php foreach (erLhAbstractModelWidgetTheme::getList(array('limit' => 1000)) as $theme) : ?>
        				   <option value="<?php echo $theme->id?>"><?php echo htmlspecialchars($theme->name)?></option>
        				<?php endforeach; ?>
        	    </select>
			</div>
		</div>

		<?php include(erLhcoreClassDesign::designtpl('lhsystem/embed_tab_content_multiinclude.tpl.php'));?>

	</div>
</div>


<p class="explain"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Copy the code from the text area to the page where you want your status to appear');?></p>
<textarea style="width: 100%; height: 180px; font-size: 12px;" id="HMLTContent"><?php echo htmlspecialchars('<script type="text/javascript" src="//'.$_SERVER['HTTP_HOST'].erLhcoreClassDesign::baseurl('chat/getstatus').'"></script>')?></textarea>

<script type="text/javascript">
var default_site_access = '<?php echo erConfigClassLhConfig::getInstance()->getSetting( 'site', 'default_site_access' ); ?>/';
function generateEmbedCode() {
    var siteAccess = $('#LocaleID').val() == default_site_access ? '' : $('#LocaleID').val();
    var id_hide_then_offline = $('#id_hide_then_offline').is(':checked') ? '/(hide_offline)/true' : '';
    var id_show_leave_form = $('#id_show_leave_form').is(':checked') ? '/(leaveamessage)/true' : '';
    var id_department = $('#DepartmentID').val() && $('#DepartmentID').val().length > 0 && $('#DepartmentID').val().join('/') != '0' ? '/(department)/'+$('#DepartmentID').val().join('/') : '';
    var id_theme = $('#ThemeID').val() > 0 ? '/(theme)/'+$('#ThemeID').val() : '';
    var id_operator = $('#id_operator').val() > 0 ? '/(operator)/'+$('#id_operator').val() : '';
    
    var id_tag =  <?php include(erLhcoreClassDesign::designtpl('lhsystem/embedcode_title.tpl.php'));?>+"\n"+
        '<div id="lhc_status_container_page" ></div>'+"\n\n"+<?php include(erLhcoreClassDesign::designtpl('lhsystem/embedcode_title_after.tpl.php'));?>+"\n";

    var uaArguments = '';
        
    <?php include(erLhcoreClassDesign::designtpl('lhsystem/embedcode_custom_generation_multiinclude.tpl.php'));?>

    if (uaArguments != '') {
    	uaArguments = '/(ua)'+uaArguments;
    }
    
    <?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/options_variable_page.tpl.php')); ?>
    
    var script = '<script type="text/javascript">'+"\n"+"var <?php echo $chatOptionsVariablePage?> = {};\n"+
      '<?php echo $chatOptionsVariablePage?>.opt = {};\n'+
      '(function() {'+"\n"+
        'var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;'+"\n"+
        'po.src = \''+$('#HttpMode').val()+'//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurldirect()?>'+siteAccess+'chat/getstatusembed<?php isset($userArgument) ? print $userArgument : ''?>'+uaArguments+id_hide_then_offline+id_theme+id_operator+id_show_leave_form+id_department+'\';'+"\n"+
        'var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);'+"\n"+
      '})();'+"\n"+
    '</scr'+'ipt>';

    $('#HMLTContent').text(id_tag+script);
};

$('#LocaleID,#id_show_leave_form,#DepartmentID,#HttpMode,#ThemeID,#id_operator,#id_hide_then_offline').change(function(){
    generateEmbedCode();
});

generateEmbedCode();

</script>