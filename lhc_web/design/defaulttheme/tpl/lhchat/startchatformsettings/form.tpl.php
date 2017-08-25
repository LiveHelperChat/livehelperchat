<?php if (isset($startchatoption['show_department']) && $startchatoption['show_department'] == true) : ?>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Name');?></label>		
    <input type="text" class="form-control" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Name');?>" name="name" value="<?php echo htmlspecialchars($start_chat_item->name);?>" />
</div>

<div class="form-group">
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Department');?></label>		
<?php 
$params = array (
		'input_name'     => 'DepartmentID',			
		'display_name'   => 'name',
        'css_class'      => 'form-control',
		'selected_id'    => $start_chat_item->department_id,
		'list_function'  => 'erLhcoreClassModelDepartament::getList',
		'list_function_params'  => array('limit' => '1000000')
);
echo erLhcoreClassRenderHelper::renderCombobox( $params ); ?>
</div>
<?php endif; ?>

<label><input type="checkbox" name="ForceLeaveMessage" value="on" <?php (isset($start_chat_data['force_leave_a_message']) && $start_chat_data['force_leave_a_message'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Enable leave a message functionality automatically if there are no online operators');?></label>

<div role="tabpanel">
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#panel1" aria-controls="panel1" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Online form settings');?></a></li>
		<li role="presentation"><a href="#panel2" aria-controls="panel12" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Offline form settings');?></a></li>
		<li role="presentation"><a href="#panel3" aria-controls="panel13" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Additional form settings');?></a></li>
		<li role="presentation"><a href="#customfields" aria-controls="customfields" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Custom fields');?></a></li>
	</ul>

	<!-- Tab panes -->
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="panel1">
			<div class="row">
				<div class="col-md-6">
					<fieldset>
						<legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Name');?></legend>

						<label><input type="checkbox" value="on" name="NameVisibleInPopup" <?php (isset($start_chat_data['name_visible_in_popup']) && $start_chat_data['name_visible_in_popup'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the popup');?></label><br /> <label><input type="checkbox" value="on" name="NameVisibleInPageWidget" <?php (isset($start_chat_data['name_visible_in_page_widget']) && $start_chat_data['name_visible_in_page_widget'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the page widget');?></label><br /> <label><input type="checkbox" value="on" name="NameHidden"
							<?php (isset($start_chat_data['name_hidden']) && $start_chat_data['name_hidden'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is invisible but prefilled data is collected');?></label><br />

						<div class="form-group">
							<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is');?></label> <select class="form-control" name="NameRequireOption">
								<option value="required" <?php (isset($start_chat_data['name_require_option']) && $start_chat_data['name_require_option'] == 'required') ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Required');?></option>
								<option value="optional" <?php (isset($start_chat_data['name_require_option']) && $start_chat_data['name_require_option'] == 'optional') ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Optional');?></option>
							</select>
						</div>

					</fieldset>
				</div>
				<div class="col-md-6">
					<fieldset>
						<legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','E-mail');?></legend>

						<label><input type="checkbox" value="on" name="EmailVisibleInPopup" <?php (isset($start_chat_data['email_visible_in_popup']) && $start_chat_data['email_visible_in_popup'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the popup');?></label><br /> <label><input type="checkbox" value="on" name="EmailVisibleInPageWidget" <?php (isset($start_chat_data['email_visible_in_page_widget']) && $start_chat_data['email_visible_in_page_widget'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the page widget');?></label><br /> <label><input type="checkbox" value="on" name="EmailHidden"
							<?php (isset($start_chat_data['email_hidden']) && $start_chat_data['email_hidden'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is invisible but prefilled data is collected');?></label><br />
						<div class="form-group">
							<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is');?></label> <select class="form-control" name="EmailRequireOption">
								<option value="required" <?php (isset($start_chat_data['email_require_option']) && $start_chat_data['email_require_option'] == 'required') ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Required');?></option>
								<option value="optional" <?php (isset($start_chat_data['email_require_option']) && $start_chat_data['email_require_option'] == 'optional') ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Optional');?></option>
							</select>
						</div>
					</fieldset>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<fieldset>
						<legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Message');?></legend>

						<label><input type="checkbox" value="on" name="MessageVisibleInPopup" <?php (isset($start_chat_data['message_visible_in_popup']) && $start_chat_data['message_visible_in_popup'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the popup');?></label>
						<br /> <label><input type="checkbox" value="on" name="MessageVisibleInPageWidget" <?php (isset($start_chat_data['message_visible_in_page_widget']) && $start_chat_data['message_visible_in_page_widget'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the page widget');?></label>
						<br /> <label><input type="checkbox" value="on" name="MessageHidden" <?php (isset($start_chat_data['message_hidden']) && $start_chat_data['message_hidden'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is invisible but prefilled data is collected');?></label>
						<br /> <label><input type="checkbox" value="on" name="MessageAutoStartOnKeyPress" <?php (isset($start_chat_data['message_auto_start_key_press']) && $start_chat_data['message_auto_start_key_press'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Automatically start chat then user starts typing. Only message field has to be required');?></label><br />
						<br /> <label><input type="checkbox" value="on" name="MessageAutoStart" <?php (isset($start_chat_data['message_auto_start']) && $start_chat_data['message_auto_start'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Start chat process in the background as soon user submits form. Only message field has to be required');?></label><br />
						
						<div class="form-group">
							<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is');?></label> 
							<select class="form-control" name="MessageRequireOption">
								<option value="required" <?php (isset($start_chat_data['message_require_option']) && $start_chat_data['message_require_option'] == 'required') ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Required');?></option>
								<option value="optional" <?php (isset($start_chat_data['message_require_option']) && $start_chat_data['message_require_option'] == 'optional') ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Optional');?></option>
							</select>
						</div>

					</fieldset>
				</div>
				<div class="col-md-6">
					<fieldset>
						<legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Phone');?></legend>

						<label><input type="checkbox" value="on" name="PhoneVisibleInPopup" <?php (isset($start_chat_data['phone_visible_in_popup']) && $start_chat_data['phone_visible_in_popup'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the popup');?></label><br /> <label><input type="checkbox" value="on" name="PhoneVisibleInPageWidget" <?php (isset($start_chat_data['phone_visible_in_page_widget']) && $start_chat_data['phone_visible_in_page_widget'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the page widget');?></label><br /> <label><input type="checkbox" value="on" name="PhoneHidden"
							<?php (isset($start_chat_data['phone_hidden']) && $start_chat_data['phone_hidden'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is invisible but prefilled data is collected');?></label><br />
						<div class="form-group">
							<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is');?></label> <select class="form-control" name="PhoneRequireOption">
								<option value="required" <?php (isset($start_chat_data['phone_require_option']) && $start_chat_data['phone_require_option'] == 'required') ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Required');?></option>
								<option value="optional" <?php (isset($start_chat_data['phone_require_option']) && $start_chat_data['phone_require_option'] == 'optional') ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Optional');?></option>
							</select>
						</div>

					</fieldset>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<fieldset>
						<legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Terms of service acceptance checkbox');?></legend>
						<label><input type="checkbox" value="on" name="TOSVisibleInPopup" <?php (isset($start_chat_data['tos_visible_in_popup']) && $start_chat_data['tos_visible_in_popup'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the popup');?></label><br /> 
						<label><input type="checkbox" value="on" name="TOSVisibleInPageWidget" <?php (isset($start_chat_data['tos_visible_in_page_widget']) && $start_chat_data['tos_visible_in_page_widget'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the page widget');?></label><br />
						<label><input type="checkbox" value="on" name="TOSCheckByDefaultOnline" <?php (isset($start_chat_data['tos_checked_online']) && $start_chat_data['tos_checked_online'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Checked by default');?></label><br />
					</fieldset>
				</div>
			</div>


		</div>
		<div role="tabpanel" class="tab-pane" id="panel2">
			<div class="row">
				<div class="col-md-6">
					<fieldset>
						<legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Name');?></legend>

						<label><input type="checkbox" value="on" name="OfflineNameVisibleInPopup" <?php (isset($start_chat_data['offline_name_visible_in_popup']) && $start_chat_data['offline_name_visible_in_popup'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the popup');?></label><br /> <label><input type="checkbox" value="on" name="OfflineNameVisibleInPageWidget" <?php (isset($start_chat_data['offline_name_visible_in_page_widget']) && $start_chat_data['offline_name_visible_in_page_widget'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the page widget');?></label><br /> <label><input type="checkbox" value="on" name="OfflineNameHidden"
							<?php (isset($start_chat_data['offline_name_hidden']) && $start_chat_data['offline_name_hidden'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is invisible but prefilled data is collected');?></label><br />

						<div class="form-group">
							<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is');?></label> <select class="form-control" name="OfflineNameRequireOption">
								<option value="required" <?php (isset($start_chat_data['offline_name_require_option']) && $start_chat_data['offline_name_require_option'] == 'required') ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Required');?></option>
								<option value="optional" <?php (isset($start_chat_data['offline_name_require_option']) && $start_chat_data['offline_name_require_option'] == 'optional') ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Optional');?></option>
							</select>
						</div>

					</fieldset>
				</div>
				<div class="col-md-6">
					<fieldset>
						<legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','E-mail');?></legend>
						<label><input type="checkbox" value="on" name="OfflineEmailHidden" <?php (isset($start_chat_data['offline_email_hidden']) && $start_chat_data['offline_email_hidden'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is invisible but prefilled data is collected');?></label>
						<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','E-mail is always required');?></p>
					</fieldset>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<fieldset>
						<legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Message');?></legend>

						<label><input type="checkbox" value="on" name="OfflineMessageVisibleInPopup" <?php (isset($start_chat_data['offline_message_visible_in_popup']) && $start_chat_data['offline_message_visible_in_popup'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the popup');?></label><br /> <label><input type="checkbox" value="on" name="OfflineMessageVisibleInPageWidget" <?php (isset($start_chat_data['offline_message_visible_in_page_widget']) && $start_chat_data['offline_message_visible_in_page_widget'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the page widget');?></label><br /> <label><input type="checkbox" value="on"
							name="OfflineMessageHidden" <?php (isset($start_chat_data['offline_message_hidden']) && $start_chat_data['offline_message_hidden'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is invisible but prefilled data is collected');?></label><br /> <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is');?></label> <select class="form-control" name="OfflineMessageRequireOption">
							<option value="required" <?php (isset($start_chat_data['offline_message_require_option']) && $start_chat_data['offline_message_require_option'] == 'required') ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Required');?></option>
							<option value="optional" <?php (isset($start_chat_data['offline_message_require_option']) && $start_chat_data['offline_message_require_option'] == 'optional') ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Optional');?></option>
						</select>

					</fieldset>
				</div>
				<div class="col-md-6">
					<fieldset>
						<legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Phone');?></legend>

						<label><input type="checkbox" value="on" name="OfflinePhoneVisibleInPopup" <?php (isset($start_chat_data['offline_phone_visible_in_popup']) && $start_chat_data['offline_phone_visible_in_popup'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the popup');?></label><br /> <label><input type="checkbox" value="on" name="OfflinePhoneVisibleInPageWidget" <?php (isset($start_chat_data['offline_phone_visible_in_page_widget']) && $start_chat_data['offline_phone_visible_in_page_widget'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the page widget');?></label><br /> <label><input type="checkbox" value="on" name="OfflinePhoneHidden"
							<?php (isset($start_chat_data['offline_phone_hidden']) && $start_chat_data['offline_phone_hidden'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is invisible but prefilled data is collected');?></label><br /> 
							
							<div class="form-group">
							<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is');?></label> <select class="form-control" name="OfflinePhoneRequireOption">
							<option value="required" <?php (isset($start_chat_data['offline_phone_require_option']) && $start_chat_data['offline_phone_require_option'] == 'required') ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Required');?></option>
							<option value="optional" <?php (isset($start_chat_data['offline_phone_require_option']) && $start_chat_data['offline_phone_require_option'] == 'optional') ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Optional');?></option>
						</select>
                        </div>

					</fieldset>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<fieldset>
						<legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Terms of service acceptance checkbox');?></legend>
						<label><input type="checkbox" value="on" name="OfflineTOSVisibleInPopup" <?php (isset($start_chat_data['offline_tos_visible_in_popup']) && $start_chat_data['offline_tos_visible_in_popup'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the popup');?></label><br /> <label><input type="checkbox" value="on" name="OfflineTOSVisibleInPageWidget" <?php (isset($start_chat_data['offline_tos_visible_in_page_widget']) && $start_chat_data['offline_tos_visible_in_page_widget'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the page widget');?></label><br />
						<label><input type="checkbox" value="on" name="TOSCheckByDefaultOffline" <?php (isset($start_chat_data['tos_checked_offline']) && $start_chat_data['tos_checked_offline'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Checked by default');?></label><br />
					</fieldset>
				</div>
				<div class="col-md-6">
					<fieldset>
						<legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Allow to attatch a file');?></legend>
						<label><input type="checkbox" value="on" name="OfflineFileVisibleInPopup" <?php (isset($start_chat_data['offline_file_visible_in_popup']) && $start_chat_data['offline_file_visible_in_popup'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the popup');?></label><br /> <label><input type="checkbox" value="on" name="OfflineFileVisibleInPageWidget" <?php (isset($start_chat_data['offline_file_visible_in_page_widget']) && $start_chat_data['offline_file_visible_in_page_widget'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the page widget');?></label><br />
					</fieldset>
				</div>
			</div>
		</div>
		<div role="tabpanel" class="tab-pane" id="panel3">
			<label><input type="checkbox" value="on" name="ShowOperatorProfile" <?php (isset($start_chat_data['show_operator_profile']) && $start_chat_data['show_operator_profile'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Show operator profile above input fields');?></label>
			
			<br />
			<label><input type="checkbox" value="on" name="RemoveOperatorSpace" <?php (isset($start_chat_data['remove_operator_space']) && $start_chat_data['remove_operator_space'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Remove space after operator profile');?></label>
			
			<br />
			<label><input type="checkbox" value="on" name="HideMessageLabel" <?php (isset($start_chat_data['hide_message_label']) && $start_chat_data['hide_message_label'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Hide message label');?></label>
			
			<br />
			<label><input type="checkbox" value="on" name="ShowMessagesBox" <?php (isset($start_chat_data['show_messages_box']) && $start_chat_data['show_messages_box'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Show messages box above input fields, usefull for UX combinations.');?></label>
			
			<div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Initial user message height in pixels');?></label> <input class="form-control" type="text" name="UserMessageHeight" value="<?php (isset($start_chat_data['user_msg_height'])) ? print htmlspecialchars($start_chat_data['user_msg_height']) : ''?>" />
			</div>

            <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Department settings')?></h4>

            <label><input type="checkbox" value="on" name="RequiresPrefilledDepartment" <?php (isset($start_chat_data['requires_dep']) && $start_chat_data['requires_dep'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Requires pre-filled department');?></label>
            <br/>
            <label><input type="checkbox" value="on" name="RequireLockForPassedDepartment" <?php (isset($start_chat_data['requires_dep_lock']) && $start_chat_data['requires_dep_lock'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','User can not change passed department.');?></label>

            <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Encryption')?></h4>
            <div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Encryption key, min length 40');?></label> 
				<input class="form-control" type="text" name="CustomFieldsEncryption" value="<?php (isset($start_chat_data['custom_fields_encryption'])) ? print htmlspecialchars($start_chat_data['custom_fields_encryption']) : ''?>" />
			</div>

			<div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Additional encryption key, min length 40');?></label> 
				<input class="form-control" type="text" name="CustomFieldsEncryptionHMac" value="<?php (isset($start_chat_data['custom_fields_encryption_hmac'])) ? print htmlspecialchars($start_chat_data['custom_fields_encryption_hmac']) : ''?>" />
			</div>

		</div>
		
		<div role="tabpanel" class="tab-pane" id="customfields">
            <?php include(erLhcoreClassDesign::designtpl('lhchat/startchatformsettings/custom_fields.tpl.php'));?>
		</div>		
		
	</div>
</div>