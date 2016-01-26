<div class="user-chatwidget-buttons" id="ChatSendButtonContainer">     
   	<div class="btn-group" role="group" aria-label="...">
   	    <a href="#" class="btn btn-default btn-xs trigger-button"><i class="material-icons fs14 mr-0">mode_edit</i></a>
	
		<input type="button" class="btn btn-default btn-xs sendbutton invisible-button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Send')?>" onclick="lhinst.addmsgadmin(<?php echo $chat->id?>)" />
	
		<?php if (erLhcoreClassModelChatConfig::fetch('bbc_button_visible')->current_value == 1) : ?>
		<input type="button" class="btn btn-default btn-xs invisible-button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','BB Code')?>" onclick="return lhc.revealModal({'url':'<?php echo erLhcoreClassDesign::baseurl('chat/bbcodeinsertadmin')?>/<?php echo $chat->id?>'})" />
		<?php endif; ?>
		
		<?php if ( (isset($chat_embed_mode) && $chat_embed_mode == true) || (isset($chat_embed_mode) && $chat_embed_mode == true)) : ?>
		<input type="button" class="btn btn-default btn-xs invisible-button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Close')?>" onclick="lhinst.userclosedchatembed();" />
		<?php endif;?>
   	</div>
</div>
