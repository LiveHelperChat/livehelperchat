<div class="row">
	<div class="columns large-7" id="chat-main-column-<?php echo $chat->id;?>">
		<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Show/Hide right column')?>" href="#" class="icon-right-circled collapse-right" onclick="lhinst.processCollapse('<?php echo $chat->id;?>')"></a>

		<div class="message-block pb10">
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
			<div class="user-is-typing" id="user-is-typing-<?php echo $chat->id?>">
				<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','User is typing now...')?>
			</div>
		</div>

		<?php include(erLhcoreClassDesign::designtpl('lhchat/part/above_textarea.tpl.php')); ?>

		<textarea rows="4" <?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?>readonly="readonly"<?php endif;?> name="ChatMessage" id="CSChatMessage-<?php echo $chat->id?>"> </textarea>

		<?php include(erLhcoreClassDesign::designtpl('lhchat/part/action_block.tpl.php')); ?>

		<?php include(erLhcoreClassDesign::designtpl('lhchat/part/below_action_block.tpl.php')); ?>

	</div>
	<div class="columns large-5" id="chat-right-column-<?php echo $chat->id;?>">
		<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/chat_tabs_container.tpl.php')); ?>
	</div>
</div>



<script type="text/javascript">
lhinst.addSynchroChat('<?php echo $chat->id;?>','<?php echo $LastMessageID?>');

$('#messagesBlock-<?php echo $chat->id?>').animate({ scrollTop: $('#messagesBlock-<?php echo $chat->id?>').prop('scrollHeight') }, 1000);

// Start synchronisation
lhinst.startSyncAdmin();

$(function() {
	$("#CSChatMessage-<?php echo $chat->id?>").sceditor({
		readOnly: <?php if($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) echo 'true'; else echo 'false'; ?>,
		keyEnter: function() {
			$('#CSChatMessage-<?php echo $chat->id?>').data.msgSubmit(<?php echo $chat->id?>);
		}
	});
	$("#CSChatMessage-<?php echo $chat->id?>").data.msgSubmit = function(chat_id) {
		$.getJSON(lhinst.wwwDir + 'chat/operatortyping/' + chat_id+'/false',{ }, function(){
			lhinst.is_typing = false;
		}).fail(function(){
			lhinst.is_typing = false;
		});
		var sc = $('#CSChatMessage-' + chat_id).data('sceditor');
		sc.updateOriginal();
		lhinst.addmsgadmin(chat_id);
		sc.val(' ');
		sc.focus();
	}
	$("#CSChatMessage-<?php echo $chat->id?>").data.adminTyping = function(chat_id) {
		var txt = $("#CSChatMessage-" + chat_id);
		if (txt.length == 0) return;
		if (!txt.data) return;
		var editor = txt.data("sceditor");
		if (!editor) return;
		var old = txt.data.oldAdminMsg;
		var msg = editor.getWysiwygEditorValue();
		if (txt.data.timeAdminTyping)
			clearTimeout(txt.data.timeAdminTyping);
		if ((msg && msg!=='' && old && msg !== old) ||
			(msg && !old) || (msg && old === '')) {
			old = msg;
			txt.data.oldAdminMsg = old;
			$.getJSON(lhinst.wwwDir + 'chat/operatortyping/' + chat_id+'/true',{ }, function(){
			   txt.data.timeAdminTyping = setTimeout(function(){txt.data.adminTyping(chat_id);},3000);
			}).fail(function(){
				txt.data.timeAdminTyping = setTimeout(function(){txt.data.adminTyping(chat_id);},3000);
			});
		}
		else
			$("#CSChatMessage-" + chat_id).data.timeAdminTyping = setTimeout(function(){$("#CSChatMessage-" + chat_id).data.adminTyping(chat_id);},3000);
	};
	$("#CSChatMessage-<?php echo $chat->id?>").data.adminTyping(<?php echo $chat->id?>);
});
</script>
