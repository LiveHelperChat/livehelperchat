<div class="row collapse">

	<div class="columns small-9">
		<div id="status-chat">
		<?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?>
			<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','This chat is closed.'); ?></h4>
		<?php else : ?>
			<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Pending confirm')?></h4>
		<?php endif; ?>
		</div>
		
		
		<?php if ( erLhcoreClassModelChatConfig::fetch('reopen_chat_enabled')->current_value == 1 && erLhcoreClassChat::canReopen($chat) ) : ?>
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
        <div class="msgBlock" id="messagesBlock"><?php
        $lastMessageID = 0;
        foreach (erLhcoreClassChat::getChatMessages($chat_id) as $msg) : if ($msg['user_id'] > -1) :?>
            <?php if ($msg['user_id'] == 0) { ?>
            	<div class="message-row"><div class="msg-date"><?php if (date('Ymd') == date('Ymd',$msg['time'])) {	echo  date(erLhcoreClassModule::$dateHourFormat,$msg['time']);} else { echo date(erLhcoreClassModule::$dateDateHourFormat,$msg['time']);}; ?></div><span class="usr-tit"><?php if (isset($chat_widget_mode) && $chat_widget_mode == true) : ?><img src="<?php echo erLhcoreClassDesign::design('images/icons/user_green.png');?>" title="<?php echo htmlspecialchars($chat->nick)?>" alt="<?php echo htmlspecialchars($chat->nick)?>" /><?php else : ?><?php echo htmlspecialchars($chat->nick)?>:<?php endif;?>&nbsp;</span><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($msg['msg']))?></div>
            <?php } else { ?>
                <div class="message-row response"><div class="msg-date"><?php if (date('Ymd') == date('Ymd',$msg['time'])) { echo  date(erLhcoreClassModule::$dateHourFormat,$msg['time']);} else {	echo date(erLhcoreClassModule::$dateDateHourFormat,$msg['time']);}; ?></div><span class="usr-tit"><?php if (isset($chat_widget_mode) && $chat_widget_mode == true) : ?><img src="<?php echo erLhcoreClassDesign::design('images/icons/user_suit.png');?>" title="<?php echo htmlspecialchars($msg['name_support'])?>" alt="<?php echo htmlspecialchars($msg['name_support'])?>" /><?php else : ?><?php echo htmlspecialchars($msg['name_support'])?>:<?php endif;?>&nbsp;</span><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($msg['msg']))?></div>
            <?php } ?>
         <?php endif; $lastMessageID = $msg['id']; endforeach; ?>
       </div>

    </div>

    <div class="pt5" id="ChatMessageContainer">

        <textarea rows="4" name="ChatMessage" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Enter your message')?>" id="CSChatMessage" ></textarea>
        <div id="id-operator-typing">
	            <i></i>
	    </div>
        <script type="text/javascript">
        jQuery('#CSChatMessage').bind('keydown', 'return', function (evt){
            lhinst.addmsguser();
            return false;
        });
        lhinst.initTypingMonitoringUser('<?php echo $chat_id?>');
        </script>
    </div>
	<div id="bbcodeReveal" class="reveal-modal"></div>
    <div class="pt5" id="ChatSendButtonContainer">
    	<input type="button" class="tiny radius secondary button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Send')?>" onclick="lhinst.addmsguser()" />
    	
    	<ul class="button-group radius right">
    	
    		<?php if (erLhcoreClassModelChatConfig::fetch('bbc_button_visible')->current_value == 1) : ?>
    		<li><input type="button" class="tiny secondary button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','BB Code')?>" data-reveal-id="bbcodeReveal" data-reveal-ajax="<?php echo erLhcoreClassDesign::baseurl('chat/bbcodeinsert')?>" /></li>
    		<?php endif; ?>
    		
    		<?php if ( isset($chat_embed_mode) && $chat_embed_mode == true ) : ?>
    		<li><input type="button" class="tiny secondary button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Close')?>" onclick="lhinst.userclosedchatembed();" /></li>
    		<?php endif;?>
    	</ul>
    	
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