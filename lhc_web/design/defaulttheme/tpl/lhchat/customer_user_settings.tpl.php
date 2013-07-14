<?php $soundMessageEnabled = erLhcoreClassModelUserSetting::getSetting('chat_message',(int)(erConfigClassLhConfig::getInstance()->getSetting('chat','new_message_sound_user_enabled'))); ?>

<ul class="no-bullet inline-list user-settings-list">
	<li><a href="#" class="sound-ico<?php $soundMessageEnabled == 0 ? print ' sound-disabled' : ''?>" onclick="return lhinst.disableChatSoundUser($(this))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Enable/Disable sound about new messages from the operator');?>"></a></li>
</ul>
