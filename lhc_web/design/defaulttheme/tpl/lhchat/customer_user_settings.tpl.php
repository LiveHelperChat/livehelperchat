<?php 

$soundData = erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data;
$soundMessageEnabled = erLhcoreClassModelUserSetting::getSetting('chat_message',(int)($soundData['new_message_sound_user_enabled'])); ?>

	<div class="d-flex flex-row">
			<div class="btn-group dropup pt-1 disable-select">
				<i class="material-icons settings text-muted" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">settings</i>
				<div class="dropdown-menu shadow bg-white rounded">
					<div class="d-flex flex-row">
						 <?php include(erLhcoreClassDesign::designtpl('lhchat/customer_user_settings_multiinclude.tpl.php'));?>
						 <?php include(erLhcoreClassDesign::designtpl('lhchat/customer_user_settings/option_sound.tpl.php'));?>
						 <?php if (isset($chat)) : ?>
							 <?php include(erLhcoreClassDesign::designtpl('lhchat/customer_user_settings/notifications.tpl.php'));?>
							 <?php if ((int)erLhcoreClassModelChatConfig::fetch('disable_print')->current_value == 0) : ?>
								  <?php include(erLhcoreClassDesign::designtpl('lhchat/customer_user_settings/option_print.tpl.php'));?>
							 <?php endif;?>
							 <?php if ((int)erLhcoreClassModelChatConfig::fetch('disable_send')->current_value == 0) : ?>
								  <?php include(erLhcoreClassDesign::designtpl('lhchat/customer_user_settings/option_transcript.tpl.php'));?>
							 <?php endif;?>
							 <?php include(erLhcoreClassDesign::designtpl('lhchat/customer_user_settings/user_file_upload.tpl.php'));?>
							 <?php include(erLhcoreClassDesign::designtpl('lhchat/customer_user_settings/smile.tpl.php'));?>
							 <?php if ((int)erLhcoreClassModelChatConfig::fetch('hide_button_dropdown')->current_value == 0 && isset($chat_widget_mode) && $chat_widget_mode == true) : ?>
								  <?php include(erLhcoreClassDesign::designtpl('lhchat/customer_user_settings/option_close.tpl.php'));?>
							 <?php endif;?>
						 <?php endif; ?>
						 <?php include(erLhcoreClassDesign::designtpl('lhchat/customer_user_settings/option_last_multiinclude.tpl.php'));?>
					</div>
				</div>
			</div>

		<div class="mx-auto pb-1 w-100">
			<textarea class="form-control form-control-sm live-chat-message pb-1" rows="1" cols="120" aria-required="true" required name="ChatMessage"  aria-label="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Type your message here...'); ?>"  placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Type your message here...'); ?>" id="CSChatMessage"></textarea>
		</div>
		<div class="disable-select">
			<?php include(erLhcoreClassDesign::designtpl('lhchat/part/above_text_area_user.tpl.php')); ?>
		</div>
  </div>


<?php

$chatUploadEnabled = false;

if (isset($chat)) {
    $chatVariables = $chat->chat_variables_array;
    if (isset($chatVariables['lhc_fu']) && $chatVariables['lhc_fu'] == 1) {
        $chatUploadEnabled = true;
    }
}

if ( (isset($chat) && isset($fileData['active_user_upload']) && $fileData['active_user_upload'] == true) || $chatUploadEnabled == true) : ?>
<script>
lhinst.addFileUserUpload({ft_msg:'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('files/files','Not an accepted file type')?>',fs_msg:'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('files/files','Filesize is too big')?>',hash:'<?php echo $chat->hash?>',chat_id:'<?php echo $chat->id?>',fs:<?php echo $fileData['fs_max']*1024?>,ft_us:/(\.|\/)(<?php echo $fileData['ft_us']?>)$/i});
</script>
<?php endif;?>