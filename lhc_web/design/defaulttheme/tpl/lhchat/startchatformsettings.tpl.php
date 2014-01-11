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

<div class="section-container auto" data-section data-options="deep_linking: true">
  <section class="active">
    <p class="title" data-section-title><a href="#panel1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Online form settings');?></a></p>
    <div class="content" data-section-content data-slug="panel1">
		<div>	    
			<div class="row">
			    <div class="columns large-6">
			        <fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Name');?></legend>
			
			            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the popup');?><input type="checkbox" value="on" name="NameVisibleInPopup" <?php (isset($start_chat_data['name_visible_in_popup']) && $start_chat_data['name_visible_in_popup'] == true) ? print 'checked="checked"' : ''?> /></label>
			            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the page widget');?><input type="checkbox" value="on" name="NameVisibleInPageWidget" <?php (isset($start_chat_data['name_visible_in_page_widget']) && $start_chat_data['name_visible_in_page_widget'] == true) ? print 'checked="checked"' : ''?> /></label>
			            <br />
			
			            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is');?></label>
			            <select name="NameRequireOption">
			                <option value="required" <?php (isset($start_chat_data['name_require_option']) && $start_chat_data['name_require_option'] == 'required') ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Required');?></option>
			                <option value="optional" <?php (isset($start_chat_data['name_require_option']) && $start_chat_data['name_require_option'] == 'optional') ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Optional');?></option>
			            </select>
			
			        </fieldset>
			    </div>
			    <div class="columns large-6">
			        <fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','E-mail');?></legend>
			
			            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the popup');?><input type="checkbox" value="on" name="EmailVisibleInPopup" <?php (isset($start_chat_data['email_visible_in_popup']) && $start_chat_data['email_visible_in_popup'] == true) ? print 'checked="checked"' : ''?> /></label>
			            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the page widget');?><input type="checkbox" value="on" name="EmailVisibleInPageWidget" <?php (isset($start_chat_data['email_visible_in_page_widget']) && $start_chat_data['email_visible_in_page_widget'] == true) ? print 'checked="checked"' : ''?> /></label>
			            <br />
			
			            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is');?></label>
			            <select name="EmailRequireOption">
			                <option value="required" <?php (isset($start_chat_data['email_require_option']) && $start_chat_data['email_require_option'] == 'required') ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Required');?></option>
			                <option value="optional" <?php (isset($start_chat_data['email_require_option']) && $start_chat_data['email_require_option'] == 'optional') ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Optional');?></option>
			            </select>
			
			        </fieldset>
			    </div>
			</div>			
			<div class="row">
	    <div class="columns large-6">
	        <fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Message');?></legend>
	
	            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the popup');?><input type="checkbox" value="on" name="MessageVisibleInPopup" <?php (isset($start_chat_data['message_visible_in_popup']) && $start_chat_data['message_visible_in_popup'] == true) ? print 'checked="checked"' : ''?> /></label>
	            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the page widget');?><input type="checkbox" value="on" name="MessageVisibleInPageWidget" <?php (isset($start_chat_data['message_visible_in_page_widget']) && $start_chat_data['message_visible_in_page_widget'] == true) ? print 'checked="checked"' : ''?> /></label>
	            <br />
	
	            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is');?></label>
	            <select name="MessageRequireOption">
	                <option value="required" <?php (isset($start_chat_data['message_require_option']) && $start_chat_data['message_require_option'] == 'required') ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Required');?></option>
	                <option value="optional" <?php (isset($start_chat_data['message_require_option']) && $start_chat_data['message_require_option'] == 'optional') ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Optional');?></option>
	            </select>
	
	        </fieldset>
	    </div>
	    <div class="columns large-6">
	        <fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Phone');?></legend>
	
	            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the popup');?><input type="checkbox" value="on" name="PhoneVisibleInPopup" <?php (isset($start_chat_data['phone_visible_in_popup']) && $start_chat_data['phone_visible_in_popup'] == true) ? print 'checked="checked"' : ''?> /></label>
	            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the page widget');?><input type="checkbox" value="on" name="PhoneVisibleInPageWidget" <?php (isset($start_chat_data['phone_visible_in_page_widget']) && $start_chat_data['phone_visible_in_page_widget'] == true) ? print 'checked="checked"' : ''?> /></label>
	            <br />
	
	            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is');?></label>
	            <select name="PhoneRequireOption">
	                <option value="required" <?php (isset($start_chat_data['phone_require_option']) && $start_chat_data['phone_require_option'] == 'required') ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Required');?></option>
	                <option value="optional" <?php (isset($start_chat_data['phone_require_option']) && $start_chat_data['phone_require_option'] == 'optional') ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Optional');?></option>
	            </select>
	
	        </fieldset>
	    </div>
	</div>
		</div>
	</div>
	</section>
	<section>
	    <p class="title" data-section-title><a href="#panel2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Offline form settings');?></a></p>
	    <div class="content" data-section-content data-slug="panel2">
				<div class="row">
			    <div class="columns large-6">
			        <fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Name');?></legend>
			
			            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the popup');?><input type="checkbox" value="on" name="OfflineNameVisibleInPopup" <?php (isset($start_chat_data['offline_name_visible_in_popup']) && $start_chat_data['offline_name_visible_in_popup'] == true) ? print 'checked="checked"' : ''?> /></label>
			            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the page widget');?><input type="checkbox" value="on" name="OfflineNameVisibleInPageWidget" <?php (isset($start_chat_data['offline_name_visible_in_page_widget']) && $start_chat_data['offline_name_visible_in_page_widget'] == true) ? print 'checked="checked"' : ''?> /></label>
			            <br />
			
			            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is');?></label>
			            <select name="OfflineNameRequireOption">
			                <option value="required" <?php (isset($start_chat_data['offline_name_require_option']) && $start_chat_data['offline_name_require_option'] == 'required') ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Required');?></option>
			                <option value="optional" <?php (isset($start_chat_data['offline_name_require_option']) && $start_chat_data['offline_name_require_option'] == 'optional') ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Optional');?></option>
			            </select>
			
			        </fieldset>
			    </div>
			    <div class="columns large-6">
			        <fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','E-mail');?></legend>
			           <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','E-mail is always required');?></p>
			        </fieldset>
			    </div>
			</div>
			<div class="row">
		    <div class="columns large-6">
		        <fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Message');?></legend>
		
		            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the popup');?><input type="checkbox" value="on" name="OfflineMessageVisibleInPopup" <?php (isset($start_chat_data['offline_message_visible_in_popup']) && $start_chat_data['offline_message_visible_in_popup'] == true) ? print 'checked="checked"' : ''?> /></label>
		            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the page widget');?><input type="checkbox" value="on" name="OfflineMessageVisibleInPageWidget" <?php (isset($start_chat_data['offline_message_visible_in_page_widget']) && $start_chat_data['offline_message_visible_in_page_widget'] == true) ? print 'checked="checked"' : ''?> /></label>
		            <br />
		
		            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is');?></label>
		            <select name="OfflineMessageRequireOption">
		                <option value="required" <?php (isset($start_chat_data['offline_message_require_option']) && $start_chat_data['offline_message_require_option'] == 'required') ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Required');?></option>
		                <option value="optional" <?php (isset($start_chat_data['offline_message_require_option']) && $start_chat_data['offline_message_require_option'] == 'optional') ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Optional');?></option>
		            </select>
		
		        </fieldset>
		    </div>
		    <div class="columns large-6">
		        <fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Phone');?></legend>
		
		            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the popup');?><input type="checkbox" value="on" name="OfflinePhoneVisibleInPopup" <?php (isset($start_chat_data['offline_phone_visible_in_popup']) && $start_chat_data['offline_phone_visible_in_popup'] == true) ? print 'checked="checked"' : ''?> /></label>
		            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is visible in the page widget');?><input type="checkbox" value="on" name="OfflinePhoneVisibleInPageWidget" <?php (isset($start_chat_data['offline_phone_visible_in_page_widget']) && $start_chat_data['offline_phone_visible_in_page_widget'] == true) ? print 'checked="checked"' : ''?> /></label>
		            <br />
		
		            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','This field is');?></label>
		            <select name="OfflinePhoneRequireOption">
		                <option value="required" <?php (isset($start_chat_data['offline_phone_require_option']) && $start_chat_data['offline_phone_require_option'] == 'required') ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Required');?></option>
		                <option value="optional" <?php (isset($start_chat_data['offline_phone_require_option']) && $start_chat_data['offline_phone_require_option'] == 'optional') ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Optional');?></option>
		            </select>
		
		        </fieldset>
		    </div>
		</div>
		
			
						
		
	    </div>
	</section>
</div>


<ul class="button-group radius">
<li><input type="submit" class="small button" name="SaveConfig" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Save');?>"/></li>
<li><input type="submit" class="small button" name="UpdateConfig" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Update');?>"/></li>
<li><input type="submit" class="small button" name="CancelConfig" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Cancel');?>"/></li>
</ul>

</form>