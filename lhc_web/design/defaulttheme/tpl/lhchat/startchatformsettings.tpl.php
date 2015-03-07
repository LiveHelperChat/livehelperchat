<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Start a chat form settings');?></h1>

<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','At least one field has to be visible and required in the popup and page widget');?></p>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post">

<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

<label><input type="checkbox" name="ForceLeaveMessage" value="on" <?php (isset($start_chat_data['force_leave_a_message']) && $start_chat_data['force_leave_a_message'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Enable leave a message functionality automatically if there are no online operators');?></label>

<div role="tabpanel">
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#panel1" aria-controls="panel1" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Online form settings');?></a></li>
		<li role="presentation"><a href="#panel2" aria-controls="panel12" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Offline form settings');?></a></li>
		<li role="presentation"><a href="#panel3" aria-controls="panel13" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Additional form settings');?></a></li>
	</ul>

	<!-- Tab panes -->
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="panel1">
		      <div class="row">
			    <div class="col-md-6">
			        <fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Name');?></legend>
			
			            <label><input type="checkbox" value="on" name="NameVisibleInPopup" <?php (isset($start_chat_data['name_visible_in_popup']) && $start_chat_data['name_visible_in_popup'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the popup');?></label><br/>
			            <label><input type="checkbox" value="on" name="NameVisibleInPageWidget" <?php (isset($start_chat_data['name_visible_in_page_widget']) && $start_chat_data['name_visible_in_page_widget'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the page widget');?></label><br/>
			            <label><input type="checkbox" value="on" name="NameHidden" <?php (isset($start_chat_data['name_hidden']) && $start_chat_data['name_hidden'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is invisible but prefilled data is collected');?></label><br/>
			
			            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is');?></label>
			            <select class="form-control" name="NameRequireOption">
			                <option value="required" <?php (isset($start_chat_data['name_require_option']) && $start_chat_data['name_require_option'] == 'required') ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Required');?></option>
			                <option value="optional" <?php (isset($start_chat_data['name_require_option']) && $start_chat_data['name_require_option'] == 'optional') ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Optional');?></option>
			            </select>
			
			        </fieldset>
			    </div>
			    <div class="col-md-6">
			        <fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','E-mail');?></legend>
			
			            <label><input type="checkbox" value="on" name="EmailVisibleInPopup" <?php (isset($start_chat_data['email_visible_in_popup']) && $start_chat_data['email_visible_in_popup'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the popup');?></label><br/>
			            <label><input type="checkbox" value="on" name="EmailVisibleInPageWidget" <?php (isset($start_chat_data['email_visible_in_page_widget']) && $start_chat_data['email_visible_in_page_widget'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the page widget');?></label><br/>
			            <label><input type="checkbox" value="on" name="EmailHidden" <?php (isset($start_chat_data['email_hidden']) && $start_chat_data['email_hidden'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is invisible but prefilled data is collected');?></label><br/>
			
			            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is');?></label>
			            <select class="form-control" name="EmailRequireOption">
			                <option value="required" <?php (isset($start_chat_data['email_require_option']) && $start_chat_data['email_require_option'] == 'required') ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Required');?></option>
			                <option value="optional" <?php (isset($start_chat_data['email_require_option']) && $start_chat_data['email_require_option'] == 'optional') ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Optional');?></option>
			            </select>
			
			        </fieldset>
			    </div>
			</div>			
			<div class="row">
			    <div class="col-md-6">
			        <fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Message');?></legend>
			
			            <label><input type="checkbox" value="on" name="MessageVisibleInPopup" <?php (isset($start_chat_data['message_visible_in_popup']) && $start_chat_data['message_visible_in_popup'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the popup');?></label><br/>
			            <label><input type="checkbox" value="on" name="MessageVisibleInPageWidget" <?php (isset($start_chat_data['message_visible_in_page_widget']) && $start_chat_data['message_visible_in_page_widget'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the page widget');?></label><br/>
			            <label><input type="checkbox" value="on" name="MessageHidden" <?php (isset($start_chat_data['message_hidden']) && $start_chat_data['message_hidden'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is invisible but prefilled data is collected');?></label><br/>
			
			            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is');?></label>
			            <select class="form-control" name="MessageRequireOption">
			                <option value="required" <?php (isset($start_chat_data['message_require_option']) && $start_chat_data['message_require_option'] == 'required') ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Required');?></option>
			                <option value="optional" <?php (isset($start_chat_data['message_require_option']) && $start_chat_data['message_require_option'] == 'optional') ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Optional');?></option>
			            </select>
			
			        </fieldset>
			    </div>
			    <div class="col-md-6">
			        <fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Phone');?></legend>
			
			            <label><input type="checkbox" value="on" name="PhoneVisibleInPopup" <?php (isset($start_chat_data['phone_visible_in_popup']) && $start_chat_data['phone_visible_in_popup'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the popup');?></label><br/>
			            <label><input type="checkbox" value="on" name="PhoneVisibleInPageWidget" <?php (isset($start_chat_data['phone_visible_in_page_widget']) && $start_chat_data['phone_visible_in_page_widget'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the page widget');?></label><br/>
			            <label><input type="checkbox" value="on" name="PhoneHidden" <?php (isset($start_chat_data['phone_hidden']) && $start_chat_data['phone_hidden'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is invisible but prefilled data is collected');?></label><br/>
			
			            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is');?></label>
			            <select class="form-control" name="PhoneRequireOption">
			                <option value="required" <?php (isset($start_chat_data['phone_require_option']) && $start_chat_data['phone_require_option'] == 'required') ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Required');?></option>
			                <option value="optional" <?php (isset($start_chat_data['phone_require_option']) && $start_chat_data['phone_require_option'] == 'optional') ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Optional');?></option>
			            </select>
			
			        </fieldset>
			    </div>
			</div>
			
			<div class="row">
    			    <div class="col-md-6">
    			        <fieldset>
    			            <legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Terms of service acceptance checkbox');?></legend>
    			            <label><input type="checkbox" value="on" name="TOSVisibleInPopup" <?php (isset($start_chat_data['tos_visible_in_popup']) && $start_chat_data['tos_visible_in_popup'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the popup');?></label><br/>
    			            <label><input type="checkbox" value="on" name="TOSVisibleInPageWidget" <?php (isset($start_chat_data['tos_visible_in_page_widget']) && $start_chat_data['tos_visible_in_page_widget'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the page widget');?></label><br/>
    			        </fieldset>
    			    </div>
    		</div>
		
			
		</div>
		<div role="tabpanel" class="tab-pane" id="panel2">
		<div class="row">
			    <div class="col-md-6">
			        <fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Name');?></legend>
			
			            <label><input type="checkbox" value="on" name="OfflineNameVisibleInPopup" <?php (isset($start_chat_data['offline_name_visible_in_popup']) && $start_chat_data['offline_name_visible_in_popup'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the popup');?></label><br/>
			            <label><input type="checkbox" value="on" name="OfflineNameVisibleInPageWidget" <?php (isset($start_chat_data['offline_name_visible_in_page_widget']) && $start_chat_data['offline_name_visible_in_page_widget'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the page widget');?></label><br/>
						<label><input type="checkbox" value="on" name="OfflineNameHidden" <?php (isset($start_chat_data['offline_name_hidden']) && $start_chat_data['offline_name_hidden'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is invisible but prefilled data is collected');?></label><br/>
			            			            
			            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is');?></label>
			            <select class="form-control" name="OfflineNameRequireOption">
			                <option value="required" <?php (isset($start_chat_data['offline_name_require_option']) && $start_chat_data['offline_name_require_option'] == 'required') ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Required');?></option>
			                <option value="optional" <?php (isset($start_chat_data['offline_name_require_option']) && $start_chat_data['offline_name_require_option'] == 'optional') ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Optional');?></option>
			            </select>
			
			        </fieldset>
			    </div>
			    <div class="col-md-6">
			        <fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','E-mail');?></legend>
			           <label><input type="checkbox" value="on" name="OfflineEmailHidden" <?php (isset($start_chat_data['offline_email_hidden']) && $start_chat_data['offline_email_hidden'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is invisible but prefilled data is collected');?></label>
			           <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','E-mail is always required');?></p>
			        </fieldset>
			    </div>
			</div>
			<div class="row">
			    <div class="col-md-6">
			        <fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Message');?></legend>
			
			            <label><input type="checkbox" value="on" name="OfflineMessageVisibleInPopup" <?php (isset($start_chat_data['offline_message_visible_in_popup']) && $start_chat_data['offline_message_visible_in_popup'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the popup');?></label><br/>
			            <label><input type="checkbox" value="on" name="OfflineMessageVisibleInPageWidget" <?php (isset($start_chat_data['offline_message_visible_in_page_widget']) && $start_chat_data['offline_message_visible_in_page_widget'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the page widget');?></label><br/>
			            <label><input type="checkbox" value="on" name="OfflineMessageHidden" <?php (isset($start_chat_data['offline_message_hidden']) && $start_chat_data['offline_message_hidden'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is invisible but prefilled data is collected');?></label><br/>
			
			            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is');?></label>
			            <select class="form-control" name="OfflineMessageRequireOption">
			                <option value="required" <?php (isset($start_chat_data['offline_message_require_option']) && $start_chat_data['offline_message_require_option'] == 'required') ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Required');?></option>
			                <option value="optional" <?php (isset($start_chat_data['offline_message_require_option']) && $start_chat_data['offline_message_require_option'] == 'optional') ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Optional');?></option>
			            </select>
			
			        </fieldset>
			    </div>
			    <div class="col-md-6">
			        <fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Phone');?></legend>
			
			            <label><input type="checkbox" value="on" name="OfflinePhoneVisibleInPopup" <?php (isset($start_chat_data['offline_phone_visible_in_popup']) && $start_chat_data['offline_phone_visible_in_popup'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the popup');?></label><br/>
			            <label><input type="checkbox" value="on" name="OfflinePhoneVisibleInPageWidget" <?php (isset($start_chat_data['offline_phone_visible_in_page_widget']) && $start_chat_data['offline_phone_visible_in_page_widget'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the page widget');?></label><br/>
			            <label><input type="checkbox" value="on" name="OfflinePhoneHidden" <?php (isset($start_chat_data['offline_phone_hidden']) && $start_chat_data['offline_phone_hidden'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is invisible but prefilled data is collected');?></label><br/>
			
			            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is');?></label>
			            <select class="form-control" name="OfflinePhoneRequireOption">
			                <option value="required" <?php (isset($start_chat_data['offline_phone_require_option']) && $start_chat_data['offline_phone_require_option'] == 'required') ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Required');?></option>
			                <option value="optional" <?php (isset($start_chat_data['offline_phone_require_option']) && $start_chat_data['offline_phone_require_option'] == 'optional') ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Optional');?></option>
			            </select>
			
			        </fieldset>
			    </div>
			</div>
		
			<div class="row">
			    <div class="col-md-6">
			        <fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Terms of service acceptance checkbox');?></legend>
			            <label><input type="checkbox" value="on" name="OfflineTOSVisibleInPopup" <?php (isset($start_chat_data['offline_tos_visible_in_popup']) && $start_chat_data['offline_tos_visible_in_popup'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the popup');?></label><br/>
			            <label><input type="checkbox" value="on" name="OfflineTOSVisibleInPageWidget" <?php (isset($start_chat_data['offline_tos_visible_in_page_widget']) && $start_chat_data['offline_tos_visible_in_page_widget'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the page widget');?></label><br/>
			        </fieldset>
			    </div>
			    <div class="col-md-6">
			        <fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Allow to attatch a file');?></legend>
			            <label><input type="checkbox" value="on" name="OfflineFileVisibleInPopup" <?php (isset($start_chat_data['offline_file_visible_in_popup']) && $start_chat_data['offline_file_visible_in_popup'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the popup');?></label><br/>
			            <label><input type="checkbox" value="on" name="OfflineFileVisibleInPageWidget" <?php (isset($start_chat_data['offline_file_visible_in_page_widget']) && $start_chat_data['offline_file_visible_in_page_widget'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the page widget');?></label><br/>
			        </fieldset>
			    </div>
			</div>
			
		</div>
		<div role="tabpanel" class="tab-pane" id="panel3">
		
		    <label><input type="checkbox" value="on" name="ShowOperatorProfile" <?php (isset($start_chat_data['show_operator_profile']) && $start_chat_data['show_operator_profile'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Show operator profile above input fields');?></label><br/>
	    	<label><input type="checkbox" value="on" name="RemoveOperatorSpace" <?php (isset($start_chat_data['remove_operator_space']) && $start_chat_data['remove_operator_space'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Remove space after operator profile');?></label><br/>
	    	<label><input type="checkbox" value="on" name="HideMessageLabel" <?php (isset($start_chat_data['hide_message_label']) && $start_chat_data['hide_message_label'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Hide message label');?></label>
	    	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Initial user message height in pixels');?></label>
	    	<input class="form-control" type="text" name="UserMessageHeight" value="<?php (isset($start_chat_data['user_msg_height'])) ? print htmlspecialchars($start_chat_data['user_msg_height']) : ''?>" />
	    	
		</div>
	</div>
</div>

<div class="btn-group" role="group" aria-label="...">
    <input type="submit" class="btn btn-default" name="SaveConfig" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Save');?>"/>
    <input type="submit" class="btn btn-default" name="UpdateConfig" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Update');?>"/>
    <input type="submit" class="btn btn-default" name="CancelConfig" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Cancel');?>"/>
</div>

</form>