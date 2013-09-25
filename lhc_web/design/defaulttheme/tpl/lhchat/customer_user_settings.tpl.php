<?php $soundMessageEnabled = erLhcoreClassModelUserSetting::getSetting('chat_message',(int)(erConfigClassLhConfig::getInstance()->getSetting('chat','new_message_sound_user_enabled'))); ?>

<div class="right pos-rel">
	<a href="#" data-dropdown="drop2" class="tiny secondary round button dropdown"><i class="icon-tools"></i></a>

	<ul id="drop2" data-dropdown-content class="f-dropdown widget-options">
		 <li><a href="#" class="sound-ico<?php $soundMessageEnabled == 0 ? print ' sound-disabled' : ''?>" onclick="return lhinst.disableChatSoundUser($(this))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Enable/Disable sound about new messages from the operator');?>"></a></li>
	     <?php if ( isset($chat) ) : ?>
		 <li><a target="_blank" href="<?php echo erLhcoreClassDesign::baseurl('chat/printchat')?>/<?php echo $chat->id?>/<?php echo $chat->hash?>" class="icon-print" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Print')?>"></a></li>
		 <li><a target="_blank" onclick="$.colorbox({className:'user-action-colorbox',closeButton:false,href:'<?php echo erLhcoreClassDesign::baseurl('chat/sendchat')?>/<?php echo $chat->id?>/<?php echo $chat->hash?>'});return false;" href="#" class="icon-mail" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Send chat transcript to your e-mail')?>"></a></li>
		 <?php endif;?>
	</ul>
</div>