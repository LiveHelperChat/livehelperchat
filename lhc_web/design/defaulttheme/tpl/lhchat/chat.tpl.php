<div class="row">

	<div class="col-xs-9">
		<div id="status-chat">
		<?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?>
			<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','This chat is closed.'); ?></h4>
		<?php else : ?>
			<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Pending confirm')?></h4>
		<?php endif; ?>
		</div>
		
		
		<?php if ( erLhcoreClassModelChatConfig::fetch('reopen_chat_enabled')->current_value == 1 && erLhcoreClassModelChatConfig::fetch('allow_reopen_closed')->current_value == 1 && erLhcoreClassChat::canReopen($chat) ) : ?>
			<a href="<?php echo erLhcoreClassDesign::baseurl('chat/reopen')?>/<?php echo $chat->id?>/<?php echo $chat->hash?><?php if ( isset($chat_widget_mode) && $chat_widget_mode == true ) : ?>/(mode)/widget<?php endif; ?><?php if ( isset($chat_embed_mode) && $chat_embed_mode == true ) : ?>/(embedmode)/embed<?php endif;?>" class="btn btn-default" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatnotexists','Resume chat');?></a>
		<?php endif; ?>
				
		<?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT && isset($chat_widget_mode) && $chat_widget_mode == true ) : ?>
			<input type="button" class="btn btn-default" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Close')?>" onclick="lhinst.userclosedchatembed();" />
		<?php endif;?>
		
	</div>

	<div class="col-xs-3">
		<?php include(erLhcoreClassDesign::designtpl('lhchat/customer_user_settings.tpl.php'));?>
	</div>

</div>

<?php if ($chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT) : ?>
    <div id="messages" >
        <div class="msgBlock" <?php if (erLhcoreClassModelChatConfig::fetch('mheight')->current_value > 0) : ?>style="height:<?php echo (int)erLhcoreClassModelChatConfig::fetch('mheight')->current_value?>px"<?php endif?> id="messagesBlock"><?php
        $lastMessageID = 0;
        foreach (erLhcoreClassChat::getChatMessages($chat_id) as $msg) : ?>        		
        	<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/user_msg_row.tpl.php'));?>	        	
         <?php $lastMessageID = $msg['id']; endforeach; ?>
       </div>
    </div>
    <div id="id-operator-typing"></div>
 
    <div id="ChatMessageContainer">    
		<div class="user-chatwidget-buttons" id="ChatSendButtonContainer">     
		
		
		   	<div class="btn-group" role="group" aria-label="...">
		   	    <a href="#" class="btn btn-default btn-xs trigger-button"><i class="icon-pencil"></i></a>
	    	
	    		<input type="button" class="btn btn-default btn-xs sendbutton invisible-button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Send')?>" onclick="lhinst.addmsguser()" />
	    	
	    		<?php if (erLhcoreClassModelChatConfig::fetch('bbc_button_visible')->current_value == 1) : ?>
	    		<input type="button" class="btn btn-default btn-xs invisible-button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','BB Code')?>" onclick="return lhc.revealModal({'url':'<?php echo erLhcoreClassDesign::baseurl('chat/bbcodeinsert')?>'})" />
	    		<?php endif; ?>
	    		
	    		<?php if ( isset($chat_embed_mode) && $chat_embed_mode == true ) : ?>
	    		<input type="button" class="btn btn-default btn-xs invisible-button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Close')?>" onclick="lhinst.userclosedchatembed();" />
	    		<?php endif;?>
		   	</div>
		  	    	    	
	    </div>
       
        <textarea class="form-control" rows="4" name="ChatMessage" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Enter your message')?>" id="CSChatMessage" class="live-chat-message"></textarea>
	    
        <script type="text/javascript">        
        jQuery('#CSChatMessage').bind('keydown', 'return', function (evt){
            lhinst.addmsguser();
            return false;
        });        
        jQuery('#CSChatMessage').bind('keyup', 'up', function (evt){
			lhinst.editPreviousUser();		   
		});		
        lhinst.initTypingMonitoringUser('<?php echo $chat_id?>');
        </script>
    </div>
    
<script type="text/javascript">
    lhinst.setChatID('<?php echo $chat_id?>');
    lhinst.setChatHash('<?php echo $hash?>');
    lhinst.setLastUserMessageID('<?php echo $lastMessageID;?>');

    <?php if ( isset($chat_widget_mode) && $chat_widget_mode == true ) : ?>
    lhinst.setWidgetMode(true);
	<?php endif; ?>

	setTimeout(function(){
			$('#messagesBlock').scrollTop($('#messagesBlock').prop('scrollHeight'));
	},100);
	
	
    // Start user chat synchronization
    lhinst.chatsyncuserpending();    
    lhinst.scheduleSync();

    $(window).bind('beforeunload', function(){
        lhinst.userclosedchat();
    });
</script>
<?php endif;?>