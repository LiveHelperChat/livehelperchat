
<div class="user-chatwidget-buttons" id="ChatSendButtonContainer">

    <a href="#" class="send-icons-action" onclick="lhinst.addmsguser(true)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Send')?>"><i class="material-icons">send</i></a>

    <div class="btn-group float-right dropleft" role="group">
        <a class="dropdown-toggle dropdown-toggle-widget" data-toggle="dropdown" aria-expanded="false">
              <i class="material-icons mr-0">settings</i>
        </a>
        <div role="menu" data-dropdown-content class="dropdown-menu dropup">
    		<?php if (erLhcoreClassModelChatConfig::fetch('bbc_button_visible')->current_value == 1) : ?>
    		<a class="dropdown-item" href="#" onclick="return lhc.revealModal({'url':'<?php echo erLhcoreClassDesign::baseurl('chat/bbcodeinsert')?>'})" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','BB Code')?>"><i class="material-icons">&#xE24E;</i></a>
    		<?php endif; ?>
    		
    		<?php if (isset($chat_embed_mode) && $chat_embed_mode == true) : ?>
    		<a class="dropdown-item" href="#" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Close')?>" onclick="lhinst.userclosedchatembed();" ><i class="material-icons">close</i></a></li>
    		<?php endif;?>
    		
    	</div>
    </div>

</div>

