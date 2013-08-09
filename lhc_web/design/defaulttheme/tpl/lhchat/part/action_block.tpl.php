<div class="row">
	<div class="columns small-4">
		<input type="button"
			value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Send')?>"
			class="small button round"
			onclick="lhinst.addmsgadmin('<?php echo $chat->id?>')" />
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