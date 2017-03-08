<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','HTML code');?></h1>

<div role="tabpanel">

	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#general" aria-controls="general" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','General');?></a></li>
		<li role="presentation"><a href="#design" aria-controls="design" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Design');?></a></li>
		<?php include(erLhcoreClassDesign::designtpl('lhsystem/htmlcode_tab_multiinclude.tpl.php'));?>
	</ul>

	<!-- Tab panes -->
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="general">		
		<div class="row">
			<div class="col-md-6">
    			<div>
    				<label><input type="checkbox" id="id_internal_popup" checked="checked" value="on"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','On a mouse click show the page widget');?></label>
    			</div>        			
			</div>
			<div class="col-md-6">
    			<div>
    				<label><input type="checkbox" id="id_hide_then_offline" value="on"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Hide status when offline');?></label>
    			</div>
			</div>
			<div class="col-md-6">
    			<div>
    				<label><input type="checkbox" id="id_check_operator_message" value="on"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Automatically check for messages from the operator/invitation messages');?> <span data-tooltip data-width="250" class="has-tip" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','It increases server load, because every 15 seconds is checked for operator message or invitation message.');?>">?</span></label>
    			</div>
			</div>
			<div class="col-md-6">
    			<div>
    				<label><input type="checkbox" id="id_show_leave_form" checked="checked" value="on"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Show a leave a message form when there are no online operators');?></label>
    			</div>
			</div>
			<div class="col-md-6">
    			<div>
    			     <label><input type="checkbox" id="id_disable_pro_active_invitations" value="on"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Disable pro active invitations, usefull if you want disable them from site part.');?></label>
    			</div>
			</div>		
			<div class="col-md-6">	
    			<div>
    				<label><input type="checkbox" id="id_disable_online_tracking" value="on"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Disable online tracking, overrides system configuration');?></label>
    			</div>
			</div>			
		</div>	
			<hr>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','For what domain you are generating embed code?');?></label> <input type="text" class="form-control" id="id_embed_domain" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','example.com');?>" value="" />
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
					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Department')?></label> 
							<select id="DepartmentID" multiple="multiple" size="5" class="form-control">
								   <option value="0"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Any');?></option>
                    			<?php foreach (erLhcoreClassModelDepartament::getList($departmentParams) as $departament) : ?>
                    			   <option value="<?php echo $departament->id?>"><?php echo htmlspecialchars($departament->name)?></option>
                    			<?php endforeach; ?>
                    		</select>
						</div>						
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Choose a language');?></label> <select id="LocaleID" class="form-control">
                                <?php foreach ($locales as $locale ) : ?>
                                <option value="<?php echo $locale?>/"><?php echo $locale?></option>
                                <?php endforeach; ?>
                            </select>
						</div>
						
						<div class="form-group">
							<label><input type="checkbox" id="DetectLanguage" value="on"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Try to detect language automatically');?></label>
						</div>
						
						<div class="row">
						  <div class="col-md-6">
						        <div class="form-group">
        							<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Operator ID')?></label> <input type="text" class="form-control" id="id_operator" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','To what operator chat should be assigned automatically?')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','To what operator chat should be assigned automatically?')?>" value="" />
        						</div>
						  </div>
						  <div class="col-md-6">
						        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Survey at the end of chat')?></label> 
						        <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
            		                    'input_name'     => 'Survey',
            		                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','no survey'),
            		                    'selected_id'    => 0,
            		                    'css_class'     => 'form-control',
            		                    'list_function'  => 'erLhAbstractModelSurvey::getList'
            		            )); ?>
						  </div>
						</div>
						
					</div>
				</div>
				
				<?php if (erLhcoreClassModelChatConfig::fetch('product_enabled_module')->current_value == 1) : ?>
				<div class="row">
				    <div class="col-md-6">
    				    <div class="form-group">
        				    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Product')?></label> 
        					<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                                    'input_name'     => 'product_id',                    				
                                    'selected_id'    =>  0,
                    	            'css_class'      => 'form-control',
                    	            'display_name'   => 'name_department',
                    	            'multiple'       => true,
                                    'list_function'  => 'erLhAbstractModelProduct::getList'
                            )); ?>
        				</div>
    				</div>				    
				</div>
				<?php endif;?>
		</div>

		<div role="tabpanel" class="tab-pane" id="design">

			<div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Minimize action, applies only if status widget is at the bottom');?></label> <select id="MinimizeID" class="form-control">
					<option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Keep where it was');?></option>
					<option value="br" selected="selected"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Minimize to bottom of the screen');?></option>
				</select>
			</div>


			<div class="row">
				
				<div class="col-md-6">
    				<div class="form-group">
        				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Position');?></label> <select id="PositionID" class="form-control">
        					<option value="original"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Native placement - it will be shown where the html is embedded');?></option>
        					<option value="bottom_right" selected="selected"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Bottom right corner of the screen');?></option>
        					<option value="bottom_left"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Bottom left corner of the screen');?></option>
        					<option value="middle_right"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Middle right side of the screen');?></option>
        					<option value="middle_left"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Middle left side of the screen');?></option>
        					<option value="api"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Invisible, only JS API will be included');?></option>
        					<option value="full_height_right"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Full height right');?></option>
        					<option value="full_height_left"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Full height left');?></option>
        				</select>
        			</div>
    			</div>
    			
    			<div class="col-md-6">
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

			<div class="form-group">
				<div class="row">
					<div class="col-md-4">
						<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Popup window width')?></label> <input type="text" class="form-control" id="id_popup_width" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Popup window width in pixels')?>" value="500" />
					</div>
					<div class="col-md-2">
						<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','height')?></label> <input type="text" class="form-control" id="id_popup_height" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Popup window height in pixels')?>" value="520" />
					</div>
					<div class="col-md-4">
						<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Widget width')?></label> <input type="text" class="form-control" id="id_widget_width" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Widget width in pixels')?>" value="300" />
					</div>
					<div class="col-md-2">
						<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','height')?></label> <input type="text" class="form-control" id="id_widget_height" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Widget height in pixels')?>" value="340" />
					</div>
				</div>
			</div>
			
			<div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Position from the top, only used if the Middle left or the Middle right side is chosen');?></label>
				<div class="row">
					<div class="col-md-8">
						<input type="text" class="form-control" id="id_top_text" value="350" />
					</div>
					<div class="col-md-4">
						<select id="UnitsTop" class="form-control">
							<option value="pixels"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Pixels');?></option>
							<option value="percents"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Percents');?></option>
						</select>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Identifier, this can be used as filter for pro active chat invitations and is use full having different messages for different domains. Only string without spaces or special characters.');?></label> <input type="text" class="form-control" id="id_site_identifier" maxlength="50" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Leave empty if it is not important to you');?>" value="" />
			</div>

		</div>
		
		<?php include(erLhcoreClassDesign::designtpl('lhsystem/htmlcode_tab_content_multiinclude.tpl.php'));?>
		
	</div>
</div>


<p class="explain"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Copy the code from the text area to the page where you want your status to appear');?></p>
<textarea style="width: 100%; height: 200px; font-size: 11px;" class="form-control" id="HMLTContent"><?php echo htmlspecialchars('<script type="text/javascript" src="http://'.$_SERVER['HTTP_HOST'].erLhcoreClassDesign::baseurl('chat/getstatus').'"></script>')?></textarea>

<script type="text/javascript">

var default_site_access = '<?php echo erConfigClassLhConfig::getInstance()->getSetting( 'site', 'default_site_access' ); ?>/';

function generateEmbedCode(){
    var siteAccess = $('#LocaleID').val() == default_site_access ? '' : $('#LocaleID').val();
    var id_internal_popup = $('#id_internal_popup').is(':checked') ? '/(click)/internal' : '';
    var id_disable_online_tracking = $('#id_disable_online_tracking').is(':checked') ? '/(dot)/true' : '';
    
    var id_show_leave_form = $('#id_show_leave_form').is(':checked') ? '/(leaveamessage)/true' : '';
    var id_hide_then_offline = $('#id_hide_then_offline').is(':checked') ? '/(hide_offline)/true' : '';
    var id_check_operator_message = $('#id_check_operator_message').is(':checked') ? '/(check_operator_messages)/true' : '';
    var id_disable_pro_active_invitations = $('#id_disable_pro_active_invitations').is(':checked') ? '/(disable_pro_active)/true' : '';
    var id_department = $('#DepartmentID').val() && $('#DepartmentID').val().length > 0 && $('#DepartmentID').val().join('/') != '0' ? '/(department)/'+$('#DepartmentID').val().join('/') : '';
    var id_product = $('#id_product_id').val() && $('#id_product_id').val().length > 0 && $('#id_product_id').val().join('/') != '0' ? '/(prod)/'+$('#id_product_id').val().join('/') : '';
    
    var id_theme = $('#ThemeID').val() > 0 ? '/(theme)/'+$('#ThemeID').val() : '';
    var id_identifier = $('#id_site_identifier').val() != '' ? '/(identifier)/'+$('#id_site_identifier').val() : '';
    var id_ma = $('#MinimizeID').val() != '' ? '/(ma)/'+$('#MinimizeID').val() : '';
    var id_operator = $('#id_operator').val() > 0 ? '/(operator)/'+$('#id_operator').val() : '';
    var id_survey = $('#id_Survey').val() > 0 ? '/(survey)/'+$('#id_Survey').val() : '';
    

    var id_position =  '/(position)/'+$('#PositionID').val();
   
    var id_tag = '';
    var top = '/(top)/'+($('#id_top_text').val() == '' ? 350 : $('#id_top_text').val());
	var topposition = '/(units)/'+$('#UnitsTop').val();

    if ($('#PositionID').val() == 'original'){
        id_tag = <?php include(erLhcoreClassDesign::designtpl('lhsystem/htmlcode_title.tpl.php'));?>+"\n"+
        '<div id="lhc_status_container" ></div>'+"\n\n"+<?php include(erLhcoreClassDesign::designtpl('lhsystem/htmlcode_title_after.tpl.php'));?>+"\n";
    };
    
    var id_embed_domain = $('#id_embed_domain').val() != '' ? ',domain:\''+$('#id_embed_domain').val()+'\'' : '';

    if (id_embed_domain.indexOf('://') != -1) {
        alert(<?php echo json_encode(htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Please do not enter protocol, only domain name is required'),ENT_QUOTES))?>);
        return;
    };
    
    var uaArguments = '';
    
    <?php include(erLhcoreClassDesign::designtpl('lhsystem/htmlcode_custom_generation_multiinclude.tpl.php'));?>

    if (uaArguments != '') {
    	uaArguments = '/(ua)'+uaArguments;
    }
    
    <?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/options_variable.tpl.php')); ?>

    var id_detect_language = $('#DetectLanguage').is(':checked') ? true : false;

    var langDetectScript = '';

    if (id_detect_language == true) {
    	langDetectScript = "var _l = '';"+
		"var _m = document.getElementsByTagName('meta');"+   
        "var _cl = '';"+
        "for (i=0; i < _m.length; i++) {if ( _m[i].getAttribute('http-equiv') == 'content-language' ) {_cl = _m[i].getAttribute('content');}}"+
        "if (document.documentElement.lang != '') _l = document.documentElement.lang;"+
        "if (_cl != '' && _cl != _l) _l = _cl;"+
        "if (_l == undefined || _l == '') {_l = '" + siteAccess + "';"+
        "} else {_l = _l[0].toLowerCase() + _l[1].toLowerCase(); if ('<?php echo erConfigClassLhConfig::getInstance()->getSetting( 'site', 'default_site_access' )?>' == _l) {_l = ''} else {_l = _l + '/';}}\n";
    }

    var script = '<script type="text/javascript">'+"\n"+"var <?php echo $chatOptionsVariable?> = {};\n"+
      '<?php echo $chatOptionsVariable?>.opt = {widget_height:'+$('#id_widget_height').val()+',widget_width:'+$('#id_widget_width').val()+',popup_height:'+$('#id_popup_height').val()+',popup_width:'+$('#id_popup_width').val()+id_embed_domain+'};\n'+
      '(function() {'+"\n"+langDetectScript+
        'var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;'+"\n"+
        'var referrer = (document.referrer) ? encodeURIComponent(document.referrer.substr(document.referrer.indexOf(\'://\')+1)) : \'\';'+"\n"+
        'var location  = (document.location) ? encodeURIComponent(window.location.href.substring(window.location.protocol.length)) : \'\';'+"\n"+
        'po.src = \''+$('#HttpMode').val()+'//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurldirect()?>'+(id_detect_language == false ? siteAccess : '\'+_l+\'')+'chat/getstatus<?php isset($userArgument) ? print $userArgument : ''?>'+uaArguments+id_internal_popup+id_position+id_ma+id_hide_then_offline+id_disable_online_tracking+id_check_operator_message+top+topposition+id_show_leave_form+id_department+id_operator+id_identifier+id_disable_pro_active_invitations+id_theme+id_product+id_survey+'?r=\'+referrer+\'&l=\'+location;'+"\n"+
        'var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);'+"\n"+
      '})();'+"\n"+
    '</scr'+'ipt>';

    $('#HMLTContent').text(id_tag+script);
};

$('#LocaleID,#id_embed_domain,#DetectLanguage,#id_product_id,#id_disable_online_tracking,#MinimizeID,#id_operator,#DepartmentID,#HttpMode,#ThemeID,#id_Survey,#id_disable_pro_active_invitations,#id_site_identifier,#id_internal_popup,#id_position_bottom,#PositionID,#id_show_leave_form,#id_hide_then_offline,#id_check_operator_message,#UnitsTop,#id_top_text,#id_popup_width,#id_popup_height,#id_widget_width,#id_widget_height').change(function(){
    generateEmbedCode();
});

generateEmbedCode();

</script>