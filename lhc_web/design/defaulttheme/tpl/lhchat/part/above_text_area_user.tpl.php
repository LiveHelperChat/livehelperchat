<div class="user-chatwidget-buttons" id="ChatSendButtonContainer"> 
    <div class="btn-group pull-right" role="group">
        
        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
              <i class="material-icons mr-0">&#xE254;</i>
              <span class="caret"></span>
        </button>
    
        <ul role="menu" data-dropdown-content class="dropdown-menu list-inline text-edit-menu">
    	         
    	    <li role="menuitem"><a href="#" onclick="lhinst.addmsguser()" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Send')?>"><i class="material-icons mat-100 mr-0">&#xE0C9;</i></a></li>
    	    	    
    		<?php if (erLhcoreClassModelChatConfig::fetch('bbc_button_visible')->current_value == 1) : ?>
    		<li role="menuitem"><a href="#" onclick="return lhc.revealModal({'url':'<?php echo erLhcoreClassDesign::baseurl('chat/bbcodeinsert')?>'})" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','BB Code')?>"><i class="material-icons mat-100 mr-0">&#xE86F;</i></a></li>
    		<?php endif; ?>
    		
    		<?php if (isset($chat_embed_mode) && $chat_embed_mode == true) : ?>
    		<li role="menuitem"><a href="#" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Close')?>" onclick="lhinst.userclosedchatembed();" ><i class="material-icons mat-100 mr-0">close</i></a></li>
    		<?php endif;?>
    		
    	</ul>
    </div>
</div>