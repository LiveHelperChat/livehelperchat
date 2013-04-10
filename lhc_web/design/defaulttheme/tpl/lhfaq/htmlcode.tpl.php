<h1 class="attr-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','HTML code');?></h1>

<br />
<div class="row">
    <div class="columns six">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/htmlcode','Status text');?></label>
		<input type="text" id="id_status_text" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','FAQ');?>" />
	</div>
</div>

<div class="row">
    <div class="columns six">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Choose a language');?></label>
        <select id="LocaleID">
            <?php foreach ($locales as $locale ) : ?>
            <option value="<?php echo $locale?>/"><?php echo $locale?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="columns six">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Position');?></label>
        <select id="PositionID">
               <option value="bottom_right"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Bottom right corner of the screen');?></option>
               <option value="bottom_left"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Bottom left corner of the screen');?></option>
               <option value="middle_right"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Middle right side of the screen');?></option>
               <option value="middle_left"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Middle left side of the screen');?></option>
        </select>
    </div>
</div>

<p class="explain"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/htmlcode','Copy code from textarea to page header or footer');?></p>
<textarea style="width:100%;height:180px;font-size:12px;" id="HMLTContent" ></textarea>

<script type="text/javascript">
var default_site_access = '<?php echo erConfigClassLhConfig::getInstance()->getSetting( 'site', 'default_site_access' ); ?>/';

function generateEmbedCode(){
    var siteAccess = $('#LocaleID').val() == default_site_access ? '' : $('#LocaleID').val();
    var id_position =  '/(position)/'+$('#PositionID').val();
	var textStatus = $('#id_status_text').val();

    var script = '<script type="text/javascript">'+"\nvar LHCVotingOptions = {status_text:'"+textStatus+"'};\n"+
      '(function() {'+"\n"+
        'var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;'+"\n"+
        'po.src = \'<?php echo erLhcoreClassSystem::instance()->baseHTTP?><?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurldirect()?>'+siteAccess+'faq/getstatus'+id_position+"';\n"+
        'var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);'+"\n"+
      '})();'+"\n"+
    '</scr'+'ipt>';
    $('#HMLTContent').text(script);
};
$('#LocaleID,#PositionID,#id_status_text').change(function(){
    generateEmbedCode();
});
generateEmbedCode();
<?php
// '?URLReferer=\'+escape(document.location);'
?>
</script>