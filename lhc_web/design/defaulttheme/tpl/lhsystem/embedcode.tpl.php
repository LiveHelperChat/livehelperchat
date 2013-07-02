<h1 class="attr-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','HTML code');?></h1>

<div class="row">
    <div class="columns large-6"><label><input type="checkbox" id="id_hide_then_offline" value="on" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Hide status when offline');?></label></div>
    <div class="columns large-6"><label><input type="checkbox" id="id_show_leave_form" value="on"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Show leave a message form when there are no online operators');?></label></div>
</div>
<br />

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
	    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Department')?></label>
	    <select id="DepartmentID">
	        	<option value="0"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Any');?></option>
			<?php foreach (erLhcoreClassDepartament::getDepartaments() as $departament) : ?>
			   <option value="<?php echo $departament['id']?>"><?php echo htmlspecialchars($departament['name'])?></option>
			<?php endforeach; ?>
		</select>
	</div>

</div>



<p class="explain"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Copy the code from the textarea to the page where you want your status to appear');?></p>
<textarea style="width:100%;height:180px;font-size:12px;" id="HMLTContent" ><?php echo htmlspecialchars('<script type="text/javascript" src="http://'.$_SERVER['HTTP_HOST'].erLhcoreClassDesign::baseurl('chat/getstatus').'"></script>')?></textarea>

<script type="text/javascript">
var default_site_access = '<?php echo erConfigClassLhConfig::getInstance()->getSetting( 'site', 'default_site_access' ); ?>/';
function generateEmbedCode() {
    var siteAccess = $('#LocaleID').val() == default_site_access ? '' : $('#LocaleID').val();
    var id_hide_then_offline = $('#id_hide_then_offline').is(':checked') ? '/(hide_offline)/true' : '';
    var id_show_leave_form = $('#id_show_leave_form').is(':checked') ? '/(leaveamessage)/true' : '';
    var id_department = $('#DepartmentID').val() > 0 ? '/(department)/'+$('#DepartmentID').val() : '';

    var id_tag = '<!-- Place this tag where you want the Live Helper Plugin to render. -->'+"\n"+
        '<div id="lhc_status_container_page" ></div>'+"\n\n<!-- Place this tag after the Live Helper Plugin tag. -->\n";

    var script = '<script type="text/javascript">'+"\n"+"var LHCChatOptionsPage = {};\n"+
      'LHCChatOptionsPage.opt = {};\n'+
      '(function() {'+"\n"+
        'var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;'+"\n"+
        'po.src = \'<?php echo erLhcoreClassSystem::instance()->baseHTTP?><?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurldirect()?>'+siteAccess+'chat/getstatusembed'+id_hide_then_offline+id_show_leave_form+id_department+'\';'+"\n"+
        'var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);'+"\n"+
      '})();'+"\n"+
    '</scr'+'ipt>';

    $('#HMLTContent').text(id_tag+script);
};

$('#LocaleID,#id_show_leave_form,#DepartmentID').change(function(){
    generateEmbedCode();
});

generateEmbedCode();

</script>