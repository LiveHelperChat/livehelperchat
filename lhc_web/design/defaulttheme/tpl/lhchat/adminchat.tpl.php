<div class="row">
	<div class="columns large-7">

		<div class="message-block pb10">
			<div class="msgBlock" id="messagesBlock-<?php echo $chat->id?>">
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
			<div class="user-is-typing"
				id="user-is-typing-<?php echo $chat->id?>">
				<i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','User is typing now...')?>
				</i>
			</div>
		</div>

		<?php include(erLhcoreClassDesign::designtpl('lhchat/part/above_textarea.tpl.php')); ?>

		<textarea rows="4" name="ChatMessage" id="CSChatMessage-<?php echo $chat->id?>"></textarea>

		<script type="text/javascript">
		jQuery('#CSChatMessage-<?php echo $chat->id?>').bind('keyup', 'return', function (evt){
		    lhinst.addmsgadmin('<?php echo $chat->id?>');
		});
		lhinst.initTypingMonitoringAdmin('<?php echo $chat->id?>');
		</script>

		<?php include(erLhcoreClassDesign::designtpl('lhchat/part/action_block.tpl.php')); ?>

	</div>
	<div class="columns large-5">
		<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/chat_tabs_container.tpl.php')); ?>
	</div>
</div>



<script type="text/javascript">
lhinst.addSynchroChat('<?php echo $chat->id;?>','<?php echo $LastMessageID?>');

$('#messagesBlock-<?php echo $chat->id?>').animate({ scrollTop: $('#messagesBlock-<?php echo $chat->id?>').prop('scrollHeight') }, 1000);

// Start synchronisation
lhinst.startSyncAdmin();
</script>
