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
            	<div class="message-row"><div class="msg-date"><?php if (date('Ymd') == date('Ymd',$msg['time'])) {	echo  date('H:i:s',$msg['time']);} else { echo date('Y-m-d H:i:s',$msg['time']);}; ?></div><span class="usr-tit"><?php if (isset($chat_widget_mode) && $chat_widget_mode == true) : ?><img src="<?php echo erLhcoreClassDesign::design('images/icons/user_green.png');?>" title="<?php echo htmlspecialchars($chat->nick)?>" alt="<?php echo htmlspecialchars($chat->nick)?>" /><?php else : ?><?php echo htmlspecialchars($chat->nick)?>:<?php endif;?>&nbsp;</span><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($msg['msg']))?></div>
            <?php } else { ?>
                <div class="message-row response"><div class="msg-date"><?php if (date('Ymd') == date('Ymd',$msg['time'])) { echo  date('H:i:s',$msg['time']);} else {	echo date('Y-m-d H:i:s',$msg['time']);}; ?></div><span class="usr-tit"><?php if (isset($chat_widget_mode) && $chat_widget_mode == true) : ?><img src="<?php echo erLhcoreClassDesign::design('images/icons/user_suit.png');?>" title="<?php echo htmlspecialchars($msg['name_support'])?>" alt="<?php echo htmlspecialchars($msg['name_support'])?>" /><?php else : ?><?php echo htmlspecialchars($msg['name_support'])?>:<?php endif;?>&nbsp;</span><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($msg['msg']))?></div>
            <?php } ?>
         <?php endif; $lastMessageID = $msg['id']; endforeach; ?>
       </div>

    </div>

    <div class="pt5" id="ChatMessageContainer">

        <textarea rows="4" name="ChatMessage" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Enter your message')?>" id="CSChatMessage" > </textarea>

	</div>

    <div class="pt5" id="ChatSendButtonContainer">
    	<?php if ( isset($chat_embed_mode) && $chat_embed_mode == true ) : ?>
    	<input type="button" class="secondary tiny button round right" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Close')?>" onclick="lhinst.userclosedchatembed();" />
    	<?php endif;?>
    </div>


<script type="text/javascript">
    lhinst.setChatID('<?php echo $chat_id?>');
    lhinst.setChatHash('<?php echo $hash?>');
    lhinst.setLastUserMessageID('<?php echo $lastMessageID;?>');

    <?php if ( isset($chat_widget_mode) && $chat_widget_mode == true ) : ?>
    lhinst.setWidgetMode(true);
	<?php endif; ?>

	$('#messagesBlock').animate({ scrollTop: $('#messagesBlock').prop('scrollHeight') }, 1000);

    // Start user chat synchronization
    lhinst.chatsyncuserpending();
    lhinst.syncusercall();

    $(window).bind('beforeunload', function(){
        lhinst.userclosedchat();
    });
	
	var oldUserMsg;
	function sendContentUserTyping() {
		if (lhinst.typing_timeout)
			clearTimeout(lhinst.typing_timeout);
		var textArea = $('#CSChatMessage');
		if (textArea === null) return;
		var editor = textArea.data('sceditor');
		if (editor === null) return;
		var msg = editor.getWysiwygEditorValue();
		if ((msg && msg!=='' && oldUserMsg && msg !== oldUserMsg) ||
			(msg && !oldUserMsg) || (msg && oldUserMsg === '')) {
			lhinst.is_typing = true;
			oldUserMsg = msg;
			$.postJSON(lhinst.wwwDir + 'chat/usertyping/<?php echo $chat_id; ?>/'+ lhinst.hash +'/true',{msg:msg}, function(){
				lhinst.typing_timeout = setTimeout(sendContentUserTyping, 3000);
			}).fail(function() {
				lhinst.typing_timeout = setTimeout(sendContentUserTyping, 3000);
			});
		}
		else
			lhinst.typing_timeout = setTimeout(sendContentUserTyping, 3000);
	}
	
	$(function() {
		$("#CSChatMessage").sceditor({
			customizeToolbar: "<div id='id-operator-typing'><i></i></div>",
			keyEnter: function() {
				$.getJSON(lhinst.wwwDir + 'chat/usertyping/<?php echo $chat_id; ?>/'+lhinst.hash+'/false',{ }, function(){
					lhinst.is_typing = false;
				}).fail(function(){
					lhinst.is_typing = false;
				});
			
				var sc = $('#CSChatMessage').data('sceditor');
				sc.updateOriginal();
				lhinst.addmsguser();
				sc.val(' ');
				sc.focus();
			}
		});
		sendContentUserTyping();
	});
	
</script>
<?php endif;?>