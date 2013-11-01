<?php

$soundMessageEnabled = erLhcoreClassModelUserSetting::getSetting('chat_message',(int)(erConfigClassLhConfig::getInstance()->getSetting('chat','new_message_sound_admin_enabled')));
$soundNewChatEnabled = erLhcoreClassModelUserSetting::getSetting('new_chat_sound',(int)(erConfigClassLhConfig::getInstance()->getSetting('chat','new_chat_sound_enabled')));

$canChangeOnlineStatus = false;
$currentUser = erLhcoreClassUser::instance();
if ( $currentUser->hasAccessTo('lhuser','changeonlinestatus') ) {
	$canChangeOnlineStatus = true;
	if ( !isset($UserData) ) {
		$UserData = $currentUser->getUserData(true);
	}
}
?>
<?php if (!isset($hideULSetting)) : ?>
<ul class="no-bullet inline-list user-settings-list hide-for-small">
<?php endif;?>
	<li><a href="#"><i class="icon-sound<?php $soundMessageEnabled == 0 ? print ' icon-mute' : ''?>" onclick="return lhinst.disableChatSoundAdmin($(this))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Enable/Disable sound about new messages from users');?>"></i></a></li>
	<li><a href="#"><i class="icon-sound<?php $soundNewChatEnabled == 0 ? print ' icon-mute' : ''?>" onclick="return lhinst.disableNewChatSoundAdmin($(this))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Enable/Disable sound about new pending chats');?>"></i></a></li>
	<?php if ($canChangeOnlineStatus == true) : ?>
		<li><a href="#"><i class="icon-user<?php $UserData->hide_online == 1 ? print ' user-online-disabled' : ''?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Change my status to online/offline');?>" onclick="return lhinst.disableUserAsOnline($(this))"></i></a></li>
	<?php endif;?>
<?php if (!isset($hideULSetting)) : ?>
</ul>
<?php endif;?>