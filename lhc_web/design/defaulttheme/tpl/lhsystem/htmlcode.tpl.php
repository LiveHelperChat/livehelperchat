<h1 class="attr-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','HTML code');?></h1>

<div class="row">
    <div class="columns six"><label><input type="checkbox" id="id_internal_popup" value="on"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','On click show page widget');?></label></div>
    <div class="columns six"><label><input type="checkbox" id="id_hide_then_offline" value="on" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Hide status then offline');?></label></div>
</div>
<div class="row">
    <div class="columns six"><label><input type="checkbox" id="id_check_operator_message" value="on"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Check for messages from operator');?></label></div>
</div>

<br />

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
               <option value="original"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Native placement - will be shown where html is embeded');?></option>
               <option value="bottom_right"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Bottom right corner of the screen');?></option>
        </select>
    </div>
</div>

<p class="explain"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Copy code from textarea to page where you want your status to appear');?></p>
<textarea style="width:100%;height:180px;font-size:12px;" id="HMLTContent" ><?php echo htmlspecialchars('<script type="text/javascript" src="http://'.$_SERVER['HTTP_HOST'].erLhcoreClassDesign::baseurl('chat/getstatus').'"></script>')?></textarea>


<script type="text/javascript">

var default_site_access = '<?php echo erConfigClassLhConfig::getInstance()->getSetting( 'site', 'default_site_access' ); ?>/';

function generateEmbedCode(){
    var siteAccess = $('#LocaleID').val() == default_site_access ? '' : $('#LocaleID').val();
    var id_internal_popup = $('#id_internal_popup').is(':checked') ? '/(click)/internal' : '';    
    var id_hide_then_offline = $('#id_hide_then_offline').is(':checked') ? '/(hide_offline)/true' : '';
    var id_check_operator_message = $('#id_check_operator_message').is(':checked') ? '/(check_operator_messages)/true' : '';
        
    var id_position =  '/(position)/'+$('#PositionID').val();
    var id_tag = '';
    
    if ($('#PositionID').val() == 'original'){
        id_tag = '<!-- Place this tag where you want the Live Helper Status to render. -->'+"\n"+
        '<div id="lhc_status_container" ></div>'+"\n\n<!-- Place this tag after the Live Helper status tag. -->\n";
    };
    
    var script = '<script type="text/javascript">'+"\n"+
      '(function() {'+"\n"+
        'var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;'+"\n"+
        'po.src = \'http://<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurldirect()?>'+siteAccess+'chat/getstatus'+id_internal_popup+id_position+id_hide_then_offline+id_check_operator_message+'\';'+"\n"+
        'var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);'+"\n"+
      '})();'+"\n"+
    '</scr'+'ipt>';
    
    $('#HMLTContent').text(id_tag+script);
};

$('#LocaleID,#id_internal_popup,#id_position_bottom,#PositionID,#id_hide_then_offline,#id_check_operator_message').change(function(){    
    generateEmbedCode();
});

generateEmbedCode();

</script>