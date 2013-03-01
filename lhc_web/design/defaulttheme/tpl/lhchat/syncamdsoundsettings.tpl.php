<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Synchronisation and sound settings');?></h1>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>	
<?php endif; ?>

<form action="" method="post" />
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','How many seconds user considered for being as online');?></label>
<input type="text" name="OnlineTimeout" value="<?php echo $settings_instance->getSetting('chat','online_timeout')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Value in seconds');?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Sync for new chats, interval in seconds');?></label>
<input type="text" name="SyncBackOffice" value="<?php echo $settings_instance->getSetting('chat','back_office_sinterval')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Value in seconds');?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Sync for new user message, interval in seconds');?></label>
<input type="text" name="SyncForUserMessagesEvery" value="<?php echo $settings_instance->getSetting('chat','chat_message_sinterval')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Value in seconds');?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Check messages from operators, interval in seconds');?></label>
<input type="text" name="SyncForOperatorMessagesEvery" value="<?php echo $settings_instance->getSetting('chat','check_for_operator_msg')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Value in seconds');?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Play new pending chat sound on new chat request');?> <input type="checkbox" name="PlayOnRequest" value="on" <?php ($settings_instance->getSetting('chat','new_chat_sound_enabled') == true) ? print 'checked="checked"' : '' ?> /></label>
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Play sound on new message for back office user');?> <input type="checkbox" name="PlayOnMessageBackOffice" value="on" <?php ($settings_instance->getSetting('chat','new_message_sound_admin_enabled') == true) ? print 'checked="checked"' : '' ?> /></label>
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Play sound on new message for front end user');?> <input type="checkbox" name="PlayOnMessageFrontOffice" value="on" <?php ($settings_instance->getSetting('chat','new_message_sound_user_enabled') == true) ? print 'checked="checked"' : '' ?> /></label>
<br />

<ul class="button-group radius">
<li><input type="submit" class="small button" name="SaveConfig" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Save');?>"/></li>
<li><input type="submit" class="small button" name="UpdateConfig" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Update');?>"/></li>
<li><input type="submit" class="small button" name="CancelConfig" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Cancel');?>"/></li>
</ul>

</form>