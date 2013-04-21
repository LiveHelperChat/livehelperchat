<?php
$soundMessageEnabled = erLhcoreClassModelUserSetting::getSetting('chat_message',(int)(erConfigClassLhConfig::getInstance()->getSetting('chat','new_message_sound_admin_enabled')));
$soundNewChatEnabled = erLhcoreClassModelUserSetting::getSetting('new_chat_sound',(int)(erConfigClassLhConfig::getInstance()->getSetting('chat','new_chat_sound_enabled')));
?>

<ul class="no-bullet inline-list user-settings-list hide-for-small">
	<li><a href="#" class="sound-ico<?php $soundMessageEnabled == 0 ? print ' sound-disabled' : ''?>" onclick="return lhinst.disableChatSoundAdmin($(this))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Enable/Disable sound about new messages from users');?>"></a></li>
	<li><a href="#" class="sound-newchat-ico<?php $soundNewChatEnabled == 0 ? print ' sound-newchat-disabled' : ''?>" onclick="return lhinst.disableNewChatSoundAdmin($(this))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Enable/Disable sound about new pending chats');?>"></a></li>
</ul>
