<div class="row">
	<div class="col-sm-7 chat-main-left-column" id="chat-main-column-<?php echo $chat->id;?>">
		<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Show/Hide right column')?>" href="#" class="icon-right-circled collapse-right" onclick="lhinst.processCollapse('<?php echo $chat->id;?>')"></a>

		<?php include(erLhcoreClassDesign::designtpl('lhchat/part/above_messages_block.tpl.php')); ?>
		
		<div class="message-block">
			<div class="msgBlock msgBlock-admin" id="messagesBlock-<?php echo $chat->id?>">
				<?php
				$LastMessageID = 0;
    			$messages = erLhcoreClassChat::getChatMessages($chat->id); ?>
				<?php include(erLhcoreClassDesign::designtpl('lhchat/syncadmin.tpl.php'));?>
				<?php if (isset($msg)) {	$LastMessageID = $msg['id'];} ?>

				<?php if ($chat->user_status == 1) : ?>
				<?php include(erLhcoreClassDesign::designtpl('lhchat/userleftchat.tpl.php')); ?>
				<?php elseif ($chat->user_status == 0) : ?>
				<?php include(erLhcoreClassDesign::designtpl('lhchat/userjoined.tpl.php')); ?>
				<?php endif;?>

			</div>
			
		</div>

		<?php include(erLhcoreClassDesign::designtpl('lhchat/part/above_textarea.tpl.php')); ?>
		
		<div class="user-is-typing" id="user-is-typing-<?php echo $chat->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','User is typing now...')?></div>
		
		
		<textarea class="form-control form-group" rows="4" <?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?>readonly="readonly"<?php endif;?> name="ChatMessage" id="CSChatMessage-<?php echo $chat->id?>"></textarea>
					
		<script type="text/javascript">
		jQuery('#CSChatMessage-<?php echo $chat->id?>').bind('keydown', 'return', function (evt){
		    lhinst.addmsgadmin('<?php echo $chat->id?>');
		    return false;
		});
		jQuery('#CSChatMessage-<?php echo $chat->id?>').bind('keyup', 'up', function (evt){
			lhinst.editPrevious('<?php echo $chat->id?>');		   
		});
		lhinst.initTypingMonitoringAdmin('<?php echo $chat->id?>');
		</script>

		<?php include(erLhcoreClassDesign::designtpl('lhchat/part/after_text_area_block.tpl.php')); ?>
		
	</div>
	<div class="col-sm-5 chat-main-right-column" id="chat-right-column-<?php echo $chat->id;?>">
		<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/chat_tabs_container.tpl.php')); ?>
	</div>
</div>



<script type="text/javascript">
lhinst.addSynchroChat('<?php echo $chat->id;?>','<?php echo $LastMessageID?>');

$('#messagesBlock-<?php echo $chat->id?>').animate({ scrollTop: $('#messagesBlock-<?php echo $chat->id?>').prop('scrollHeight') }, 1000);

// Start synchronisation
lhinst.startSyncAdmin();
</script>
