
	<div class="form-group" id="action-block-row-<?php echo $chat->id?>">
		<div class="send-row<?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?> hide<?php endif;?>">
		
		<div class="btn-group btn-group-justified">
			<a href="#" class="btn btn-default" onclick="return lhinst.addmsgadmin('<?php echo $chat->id?>')"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Send')?></a>
			
			<?php include(erLhcoreClassDesign::designtpl('lhchat/part/speech_action.tpl.php')); ?>
			
			<?php include(erLhcoreClassDesign::designtpl('lhchat/part/translation_action.tpl.php')); ?>
              
            <?php include(erLhcoreClassDesign::designtpl('lhchat/part/send_delayed_canned_action.tpl.php')); ?>
			
		</div>
		
		</div>
		<?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?><input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Reopen chat')?>" class="btn btn-default" data-id="<?php echo $chat->id?>" onclick="lhinst.reopenchat($(this))" /><?php endif;?>
	</div>
	
	<?php include(erLhcoreClassDesign::designtpl('lhchat/part/canned_messages_action.tpl.php')); ?>