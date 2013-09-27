<div class="row">
	<div class="columns small-4" id="action-block-row-<?php echo $chat->id?>">
		<div class="send-row<?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?> hide<?php endif;?>">
			<input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Send')?>" class="small button round" onclick="lhinst.addmsgadmin('<?php echo $chat->id?>')" />
		</div>
		<?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?><input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Reopen chat')?>" class="small button secondary round" data-id="<?php echo $chat->id?>" onclick="lhinst.reopenchat($(this))" /><?php endif;?>
	</div>
	<div class="columns small-8">
		<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
						'input_name'     => 'CannedMessage-'.$chat->id,
						'on_change'      => "$('#CSChatMessage-".$chat->id."').val(($(this).val() > 0) ? $(this).find(':selected').text() : '')",
						'optional_field' =>  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Select a canned message'),
						'display_name'   => 'msg',
						'selected_id'    => '',
						'list_function'  => 'erLhcoreClassModelCannedMsg::getList'
            )); ?>
	</div>
</div>