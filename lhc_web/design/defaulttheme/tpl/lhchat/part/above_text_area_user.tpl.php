
<div class="user-chatwidget-buttons" id="ChatSendButtonContainer">

    <a href="#" class="send-icons-action" onclick="lhinst.addmsguser(true)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Send')?>"><i class="material-icons">&#xf48a;</i></a>

    <div class="btn-group float-right dropleft" role="group">
        <a class="dropdown-toggle dropdown-toggle-widget" data-toggle="dropdown" aria-expanded="false">
              <i class="material-icons mr-0">&#xf493;</i>
        </a>
        <div role="menu" data-dropdown-content class="dropdown-menu dropup">
    		<?php if (erLhcoreClassModelChatConfig::fetch('bbc_button_visible')->current_value == 1) : ?>
    		<a class="dropdown-item" href="#" onclick="return lhc.revealModal({'url':'<?php echo erLhcoreClassDesign::baseurl('chat/bbcodeinsert')?>'})" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','BB Code')?>"><i class="material-icons">&#xf4fa;</i></a>
    		<?php endif; ?>
    		
    		<?php if (isset($chat_embed_mode) && $chat_embed_mode == true) : ?>
    		<a class="dropdown-item" href="#" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Close')?>" onclick="lhinst.userclosedchatembed();" ><i class="material-icons">&#xf156;</i></a></li>
    		<?php endif;?>
    		
    	</div>
    </div>

</div>

