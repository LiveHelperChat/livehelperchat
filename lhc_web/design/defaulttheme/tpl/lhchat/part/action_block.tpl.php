<div class="row">
	<div class="columns small-4" id="action-block-row-<?php echo $chat->id?>">
		<div class="send-row<?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?> hide<?php endif;?>">
			<input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Send')?>" class="small button round" onclick="lhinst.addmsgadmin('<?php echo $chat->id?>')" />
		</div>
		<?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?><input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Reopen chat')?>" class="small button secondary round" data-id="<?php echo $chat->id?>" onclick="lhinst.reopenchat($(this))" /><?php endif;?>
	</div>
	<div class="columns small-8">

		<div class="row">
			<div class="columns small-8">
	            <select name="CannedMessage-<?php echo $chat->id?>" id="id_CannedMessage-<?php echo $chat->id?>">
	            	<option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Select a canned message')?></option>
	            <?php 
	            $nameSupport = (string)erLhcoreClassUser::instance()->getUserData(true)->name_support;
	            foreach (erLhcoreClassModelCannedMsg::getCannedMessages($chat->dep_id,erLhcoreClassUser::instance()->getUserID()) as $item) : ?>
	            	<option data-delay="<?php echo $item->delay?>" value="<?php echo $item->id?>"><?php echo htmlspecialchars(str_replace(array('{nick}','{operator}'), array($chat->nick,$nameSupport), $item->msg))?></option>
	            <?php endforeach;?>
	            </select>
	         </div>
			<div class="columns small-4 sub-action-chat">
					<ul class="button-group round even-2">
						  <li><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Fill textarea with canned message')?>" href="#" onclick="$('#CSChatMessage-<?php echo $chat->id?>').val(($('#id_CannedMessage-<?php echo $chat->id?>').val() > 0) ? $('#id_CannedMessage-<?php echo $chat->id?>').find(':selected').text() : '');return false;" class="button tiny icon-pencil"></a></li>
						  <li><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Send delayed canned message instantly')?>" href="#" class="button tiny icon-mail" onclick="return lhinst.sendCannedMessage('<?php echo $chat->id?>',$(this))"></a></li>
					</ul>
			</div>
		</div>

	</div>
</div>