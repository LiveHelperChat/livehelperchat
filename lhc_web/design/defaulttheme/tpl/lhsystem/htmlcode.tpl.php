<h1 class="attr-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','HTML code');?></h1>

<div class="row">
    <div class="columns large-6"><label><input type="checkbox" id="id_internal_popup" checked="checked" value="on"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','On a mouse click show the page widget');?></label></div>
    <div class="columns large-6"><label><input type="checkbox" id="id_hide_then_offline" value="on" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Hide status when offline');?></label></div>
</div>
<div class="row">
    <div class="columns large-6"><label><input type="checkbox" id="id_check_operator_message" value="on"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Automatically check for messages from the operator/invitation messages');?> <span data-tooltip data-width="250" class="has-tip"  title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','It increases server load, because every 15 seconds is checked for operator message or invitation message.');?>">?</span></label></div>
    <div class="columns large-6"><label><input type="checkbox" id="id_show_leave_form" checked="checked" value="on"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Show a leave a message form when there are no online operators');?></label></div>
</div>
<div class="row">
    <div class="columns large-6">
    	<label><input type="checkbox" id="id_disable_pro_active_invitations" value="on"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Disable pro active invitations, usefull if you want disable them from site part.');?></label>
    	<label><input type="checkbox" id="id_disable_responsive" value="on"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Disable responsive layout for status widget.');?></label>
    </div>
    <div class="columns large-6">    
    	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Choose prefered http mode');?></label>
	    <select id="HttpMode">         
	            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Based on site (default)');?></option>
	            <option value="http:">http:</option>
	            <option value="https:">https:</option>      
	    </select>    
    </div>
    
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

        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Department')?></label>
        <select id="DepartmentID" multiple="multiple" size="5">
        	<option value="0"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Any');?></option>
			<?php foreach (erLhcoreClassModelDepartament::getList($departmentParams) as $departament) : ?>
			   <option value="<?php echo $departament->id?>"><?php echo htmlspecialchars($departament->name)?></option>
			<?php endforeach; ?>
		</select>

        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Operator ID')?></label>
        <input type="text" id="id_operator" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','To what operator chat should be assigned automatically?')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','To what operator chat should be assigned automatically?')?>" value="" />
		
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Theme')?></label>
        <select id="ThemeID">
        	<option value="0"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Default');?></option>
			<?php foreach (erLhAbstractModelWidgetTheme::getList(array('limit' => 1000)) as $theme) : ?>
			   <option value="<?php echo $theme->id?>"><?php echo htmlspecialchars($theme->name)?></option>
			<?php endforeach; ?>
		</select>
		
        <div class="row">
        	<div class="column large-6">
        		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Popup window size')?></label>
        		<div class="row">
		        	<div class="column large-6">
		        		<input type="text" id="id_popup_width" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Popup window width in pixels')?>" value="500" />
		        	</div>
		        	<div class="column large-6">
		        		<input type="text" id="id_popup_height" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Popup window height in pixels')?>" value="520" />
		        	</div>
		        </div>
        	</div>
        	<div class="column large-6">
	        	<div class="row">
			        	<div class="column large-6">
			        		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Widget width')?></label>
			        		<input type="text" id="id_widget_width" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Widget width in pixels')?>" value="300" />
			        	</div>
			        	<div class="column large-6">
			        		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','height')?></label>
		                    <input type="text" id="id_widget_height" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Widget height in pixels')?>" value="340" />
		                </div>
			     </div>
        	</div>
        </div>

    </div>
    <div class="columns large-6">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Position');?></label>
        <select id="PositionID">
               <option value="original"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Native placement - it will be shown where the html is embedded');?></option>
               <option value="bottom_right" selected="selected"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Bottom right corner of the screen');?></option>
               <option value="bottom_left"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Bottom left corner of the screen');?></option>
               <option value="middle_right"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Middle right side of the screen');?></option>
               <option value="middle_left"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Middle left side of the screen');?></option>
        </select>
                
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Minimize action, applies only if status widget is at the bottom');?></label>
        <select id="MinimizeID">
               <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Keep where it was');?></option>
               <option value="br" selected="selected"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Minimize to bottom of the screen');?></option>                       
        </select>

        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Position from the top, only used if the Middle left or the Middle right side is chosen');?></label>
	    <div class="row">
	      <div class="large-8 columns">
	        <input type="text" id="id_top_text" value="350" />
	      </div>
	      <div class="large-4 columns">
	      	<select id="UnitsTop">
	            <option value="pixels"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Pixels');?></option>
	            <option value="percents"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Percents');?></option>
	        </select>
	      </div>
	    </div>
    </div>
    <div class="columns large-6">       
    
    	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','For what domain you are generating embed code?');?></label>
    	<input type="text" id="id_embed_domain" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','example.com');?>" value="" />
    	
    	
    	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Identifier, this can be used as filter for pro active chat invitations and is use full having different messages for different domains. Only string without spaces or special characters.');?></label>
    	<input type="text" id="id_site_identifier" maxlength="50" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Leave empty if it is not important to you');?>" value="" />
	</div>
</div>



<p class="explain"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Copy the code from the text area to the page where you want your status to appear');?></p>
<textarea style="width:100%;height:180px;font-size:12px;" id="HMLTContent" ><?php echo htmlspecialchars('<script type="text/javascript" src="http://'.$_SERVER['HTTP_HOST'].erLhcoreClassDesign::baseurl('chat/getstatus').'"></script>')?></textarea>

<script type="text/javascript">

var default_site_access = '<?php echo erConfigClassLhConfig::getInstance()->getSetting( 'site', 'default_site_access' ); ?>/';

function generateEmbedCode(){
    var siteAccess = $('#LocaleID').val() == default_site_access ? '' : $('#LocaleID').val();
    var id_internal_popup = $('#id_internal_popup').is(':checked') ? '/(click)/internal' : '';
    var id_show_leave_form = $('#id_show_leave_form').is(':checked') ? '/(leaveamessage)/true' : '';
    var id_hide_then_offline = $('#id_hide_then_offline').is(':checked') ? '/(hide_offline)/true' : '';
    var id_disable_responsive = $('#id_disable_responsive').is(':checked') ? '/(noresponse)/true' : '';
    var id_check_operator_message = $('#id_check_operator_message').is(':checked') ? '/(check_operator_messages)/true' : '';
    var id_disable_pro_active_invitations = $('#id_disable_pro_active_invitations').is(':checked') ? '/(disable_pro_active)/true' : '';
    var id_department = $('#DepartmentID').val() && $('#DepartmentID').val().length > 0 && $('#DepartmentID').val().join('/') != '0' ? '/(department)/'+$('#DepartmentID').val().join('/') : '';

    
    var id_theme = $('#ThemeID').val() > 0 ? '/(theme)/'+$('#ThemeID').val() : '';
    var id_identifier = $('#id_site_identifier').val() != '' ? '/(identifier)/'+$('#id_site_identifier').val() : '';
    var id_ma = $('#MinimizeID').val() != '' ? '/(ma)/'+$('#MinimizeID').val() : '';
    var id_operator = $('#id_operator').val() > 0 ? '/(operator)/'+$('#id_operator').val() : '';

    var id_position =  '/(position)/'+$('#PositionID').val();
   
    var id_tag = '';
    var top = '/(top)/'+($('#id_top_text').val() == '' ? 350 : $('#id_top_text').val());
	var topposition = '/(units)/'+$('#UnitsTop').val();

    if ($('#PositionID').val() == 'original'){
        id_tag = '<!-- Place this tag where you want the Live Helper Status to render. -->'+"\n"+
        '<div id="lhc_status_container" ></div>'+"\n\n<!-- Place this tag after the Live Helper status tag. -->\n";
    };
    
    var id_embed_domain = $('#id_embed_domain').val() != '' ? ',domain:\''+$('#id_embed_domain').val()+'\'' : '';
 
    var script = '<script type="text/javascript">'+"\n"+"var LHCChatOptions = {};\n"+
      'LHCChatOptions.opt = {widget_height:'+$('#id_widget_height').val()+',widget_width:'+$('#id_widget_width').val()+',popup_height:'+$('#id_popup_height').val()+',popup_width:'+$('#id_popup_width').val()+'\''+id_embed_domain+'};\n'+
      '(function() {'+"\n"+
        'var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;'+"\n"+
        'var refferer = (document.referrer) ? encodeURIComponent(document.referrer.substr(document.referrer.indexOf(\'://\')+1)) : \'\';'+"\n"+
        'var location  = (document.location) ? encodeURIComponent(window.location.href.substring(window.location.protocol.length)) : \'\';'+"\n"+
        'po.src = \''+$('#HttpMode').val()+'//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurldirect()?>'+siteAccess+'chat/getstatus'+id_internal_popup+id_position+id_ma+id_hide_then_offline+id_check_operator_message+top+topposition+id_show_leave_form+id_department+id_operator+id_identifier+id_disable_pro_active_invitations+id_theme+id_disable_responsive+'?r=\'+refferer+\'&l=\'+location;'+"\n"+
        'var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);'+"\n"+
      '})();'+"\n"+
    '</scr'+'ipt>';

    $('#HMLTContent').text(id_tag+script);
};

$('#LocaleID,#id_embed_domain,#MinimizeID,#id_operator,#DepartmentID,#HttpMode,#ThemeID,#id_disable_pro_active_invitations,#id_site_identifier,#id_internal_popup,#id_disable_responsive,#id_position_bottom,#PositionID,#id_show_leave_form,#id_hide_then_offline,#id_check_operator_message,#UnitsTop,#id_top_text,#id_popup_width,#id_popup_height,#id_widget_width,#id_widget_height').change(function(){
    generateEmbedCode();
});

generateEmbedCode();

</script>