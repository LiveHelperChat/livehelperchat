<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','HTML code');?></h1>

<div class="row">
    <div class="col-md-6">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Choose a language');?></label>
        <select class="form-control" id="LocaleID">
            <?php foreach ($locales as $locale ) : ?>
            <option value="<?php echo $locale?>/"><?php echo $locale?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
	   <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Choose prefered http mode');?></label>
		    <select class="form-control" id="HttpMode">         
		            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Based on site (default)');?></option>
		            <option value="http:">http:</option>
		            <option value="https:">https:</option>      
		    </select>    	    
    </div>
    <div class="col-md-6">
	   <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Choose what form you want to embed');?></label>
	   <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
										'input_name'     => 'FormID',								
										'display_name'   => 'name',
										'selected_id'    => 0,
	                                    'css_class'      => 'form-control',
										'list_function'  => 'erLhAbstractModelForm::getList',
										'list_function_params'  => array('limit' => '1000000'),
		)); ?> 	    
    </div>
    <div class="col-md-6">
	   <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Identifier');?></label>
	   <input class="form-control" type="text" id="id_identifier" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Identifier')?>" value="" /> 	    
    </div>
    
</div>

<p class="explain"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Copy the code from the textarea to page where you want it to be rendered');?></p>

<textarea class="form-control" style="width:100%;height:180px;font-size:12px;" id="HMLTContent" ></textarea>

<script type="text/javascript">
var default_site_access = '<?php echo erConfigClassLhConfig::getInstance()->getSetting( 'site', 'default_site_access' ); ?>/';

function generateEmbedCode(){
    var siteAccess = $('#LocaleID').val() == default_site_access ? '' : $('#LocaleID').val();
    var formid = $('#id_FormID').val() == default_site_access ? '' : $('#id_FormID').val();
    var identifier = $('#id_identifier').val() == '' ? '' : '?identifier=' + $('#id_identifier').val();

    var id_tag = '<!-- Place this tag where you want the Live Helper Form module to render. -->'+"\n"+'<div id="lhc_form_embed_container" ></div>'+"\n\n<!-- Place this tag after the Live Helper Form module tag. -->\n";
    
    <?php include(erLhcoreClassDesign::designtpl('lhform/getstatus/options_variable.tpl.php')); ?>
    
    var script = '<script type="text/javascript">'+"\nvar <?php echo $formOptionsVariable?> = {};\n"+
      '(function() {'+"\n"+
        'var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;'+"\n"+
        'po.src = \''+$('#HttpMode').val()+'//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurldirect()?>'+siteAccess+'form/embed/'+formid+identifier+"';\n"+
        'var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);'+"\n"+
      '})();'+"\n"+
    '</scr'+'ipt>';
    $('#HMLTContent').text(id_tag+script);
};

$('#LocaleID,#HttpMode,#id_identifier').change(function(){
    generateEmbedCode();
});
generateEmbedCode();
</script>