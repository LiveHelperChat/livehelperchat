
	<div class="form-group" id="action-block-row-<?php echo $chat->id?>">
		<div class="send-row<?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?> hide<?php endif;?>">
		
		<div class="btn-group btn-group-justified">
			<a href="#" class="btn btn-default" onclick="return lhinst.addmsgadmin('<?php echo $chat->id?>')"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Send')?></a>
						
			<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhspeech','use')) : ?>
			     <a class="btn btn-default icon-mic" href="#" id="mic-chat-<?php echo $chat->id?>" onclick="return lhc.methodCall('lhc.speak','listen',{'chat_id':'<?php echo $chat->id?>'})"></a>
			<?php endif;?>
			
			<?php include(erLhcoreClassDesign::designtpl('lhchat/part/translation_action.tpl.php')); ?>
               
			<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Send delayed canned message instantly')?>" href="#" class="btn btn-default icon-mail" onclick="return lhinst.sendCannedMessage('<?php echo $chat->id?>',$(this))"></a>
		</div>
		
		</div>
		<?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?><input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Reopen chat')?>" class="btn btn-default" data-id="<?php echo $chat->id?>" onclick="lhinst.reopenchat($(this))" /><?php endif;?>
	</div>
	
	<?php include(erLhcoreClassDesign::designtpl('lhchat/part/canned_messages_action.tpl.php')); ?>