<?php

$soundData = erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data;

$soundMessageEnabled = erLhcoreClassModelUserSetting::getSetting('chat_message',(int)($soundData['new_message_sound_admin_enabled']));
$soundNewChatEnabled = erLhcoreClassModelUserSetting::getSetting('new_chat_sound',(int)($soundData['new_chat_sound_enabled']));

$canChangeOnlineStatus = false;
$currentUser = erLhcoreClassUser::instance();
if ( $currentUser->hasAccessTo('lhuser','changeonlinestatus') ) {
	$canChangeOnlineStatus = true;
	if ( !isset($UserData) ) {
		$UserData = $currentUser->getUserData(true);
	}
}

$canChangeVisibilityMode = false;
if ( $currentUser->hasAccessTo('lhuser','changevisibility') ) {
	$canChangeVisibilityMode = true;
	if ( !isset($UserData) ) {
		$UserData = $currentUser->getUserData(true);
	}
}

?>

<?php if ($currentUser->hasAccessTo('lhchat','use') ) : ?>

<?php if (!isset($hideULSetting)) : ?>
<ul class="list-inline user-settings-list pull-right">
<?php endif;?>
	<li><a href="#"><i class="material-icons" onclick="return lhinst.disableChatSoundAdmin($(this))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Enable/Disable sound about new messages from users');?>"><?php $soundMessageEnabled == 0 ? print 'volume_off' : print 'volume_up'?></i></a></li>
	<li><a href="#"><i class="material-icons" onclick="return lhinst.disableNewChatSoundAdmin($(this))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Enable/Disable sound about new pending chats');?>"><?php $soundNewChatEnabled == 0 ? print 'volume_off' : print 'volume_up'?></i></a></li>
		
	<?php if ($canChangeVisibilityMode == true) : ?>
	<li><a href="#"><i class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Change my visibility to visible/invisible');?>" onclick="return lhinst.changeVisibility($(this))"><?php $UserData->invisible_mode == 1 ? print 'visibility_off' : print 'visibility'?></i></a></li>
	<?php endif;?>
		
	<?php if ($canChangeOnlineStatus == true) : ?>
		<li><a href="#"><i class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Change my status to online/offline');?>" onclick="return lhinst.disableUserAsOnline($(this))"><?php $UserData->hide_online == 1 ? print 'flash_off' : print 'flash_on'?></i></a></li>
	<?php endif;?>
<?php if (!isset($hideULSetting)) : ?>
</ul>
<?php endif;?>

<?php endif;?>