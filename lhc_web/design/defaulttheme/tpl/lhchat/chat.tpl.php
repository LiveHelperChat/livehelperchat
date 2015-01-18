<div class="row collapse">

	<div class="columns small-9">
		<div id="status-chat">
		<?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?>
			<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','This chat is closed.'); ?></h4>
		<?php else : ?>
			<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Pending confirm')?></h4>
		<?php endif; ?>
		</div>
		
		
		<?php if ( erLhcoreClassModelChatConfig::fetch('reopen_chat_enabled')->current_value == 1 && erLhcoreClassModelChatConfig::fetch('allow_reopen_closed')->current_value == 1 && erLhcoreClassChat::canReopen($chat) ) : ?>
			<a href="<?php echo erLhcoreClassDesign::baseurl('chat/reopen')?>/<?php echo $chat->id?>/<?php echo $chat->hash?><?php if ( isset($chat_widget_mode) && $chat_widget_mode == true ) : ?>/(mode)/widget<?php endif; ?><?php if ( isset($chat_embed_mode) && $chat_embed_mode == true ) : ?>/(embedmode)/embed<?php endif;?>" class="tiny button round success" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatnotexists','Resume chat');?></a>
		<?php endif; ?>
				
		<?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT && isset($chat_widget_mode) && $chat_widget_mode == true ) : ?>
			<input type="button" class="tiny secondary button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Close')?>" onclick="lhinst.userclosedchatembed();" />
		<?php endif;?>
		
	</div>

	<div class="columns small-3">
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
	    	<ul class="button-group right button-action-right">    	
	    	
	    		<li><a href="#" class="tiny secondary button trigger-button"><i class="icon-pencil"></i></a></li>
	    	
	    		<li><input type="button" class="tiny secondary button sendbutton invisible-button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Send')?>" onclick="lhinst.addmsguser()" /></li>
	    	
	    		<?php if (erLhcoreClassModelChatConfig::fetch('bbc_button_visible')->current_value == 1) : ?>
	    		<li><input type="button" class="tiny secondary button invisible-button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','BB Code')?>" data-reveal-id="bbcodeReveal" data-reveal-ajax="<?php echo erLhcoreClassDesign::baseurl('chat/bbcodeinsert')?>" /></li>
	    		<?php endif; ?>
	    		
	    		<?php if ( isset($chat_embed_mode) && $chat_embed_mode == true ) : ?>
	    		<li><input type="button" class="tiny secondary button invisible-button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Close')?>" onclick="lhinst.userclosedchatembed();" /></li>
	    		<?php endif;?>
	    	</ul>    	
	    </div>
       
        <textarea rows="4" name="ChatMessage" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Enter your message')?>" id="CSChatMessage" class="live-chat-message"></textarea>
	    
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
	<div id="bbcodeReveal" class="reveal-modal"></div>
	
    


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