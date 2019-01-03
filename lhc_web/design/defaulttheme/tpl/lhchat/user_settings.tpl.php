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
<ul class="list-inline user-settings-list float-right">
<?php endif;?>
	
	<?php include_once(erLhcoreClassDesign::designtpl('lhchat/user_settings_sound.tpl.php'));?>
		
	<?php if ($canChangeVisibilityMode == true) : ?>
	    <li class="list-inline-item nav-item"><a href="#" class="nav-link"><i id="vi-in-user" class="material-icons ng-cloak" ng-click="lhc.changeVisibility()" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Change my visibility to visible/invisible');?>" >{{lhc.hideInvisible == true ? 'visibility_off' : 'visibility'}}</i></a></li>
	<?php endif;?>
		
	<?php if ($canChangeOnlineStatus == true) : ?>
		<li class="list-inline-item nav-item"><a href="#" class="nav-link"><i id="online-offline-user" class="material-icons ng-cloak" ng-click="lhc.changeOnline()" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Change my status to online/offline');?>" >{{lhc.hideOnline == true ? 'flash_off' : 'flash_on'}}</i></a></li>
	<?php endif;?>
<?php if (!isset($hideULSetting)) : ?>
</ul>
<?php endif;?>

<?php endif;?>