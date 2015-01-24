<div class="row">
	<div class="columns small-12" id="action-block-row-<?php echo $chat->id?>">
		<div class="send-row<?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?> hide<?php endif;?>">
		
		<ul class="button-group radius even-4">
			<li><a href="#" class="button small" onclick="return lhinst.addmsgadmin('<?php echo $chat->id?>')"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Send')?></a>
						
			<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhspeech','use')) : ?>
			<li><a class="button icon-mic small" href="#" id="mic-chat-<?php echo $chat->id?>" onclick="return lhinst.speechToText('<?php echo $chat->id?>')"></a></li>
			<?php endif;?>
									
			<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhspeech','change_chat_recognition')) : ?>
			<li><a class="icon-mic button small" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Choose other than default recognition language')?>" onclick="lhinst.speechLanguage('<?php echo $chat->id?>')"> <i class="icon-tools"></i></a></li>
			<?php endif;?>
			
			<li><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Send delayed canned message instantly')?>" href="#" class="button small icon-mail" onclick="return lhinst.sendCannedMessage('<?php echo $chat->id?>',$(this))"></a></li>
		</ul>
		
		</div>
		<?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?><input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Reopen chat')?>" class="small button secondary round" data-id="<?php echo $chat->id?>" onclick="lhinst.reopenchat($(this))" /><?php endif;?>
	</div>
	<div class="columns small-12">

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
				<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Fill textarea with canned message')?>" href="#" onclick="$('#CSChatMessage-<?php echo $chat->id?>').val(($('#id_CannedMessage-<?php echo $chat->id?>').val() > 0) ? $('#id_CannedMessage-<?php echo $chat->id?>').find(':selected').text() : '');return false;" class="button small radius expand icon-pencil"></a>
				
			</div>
		</div>

	</div>
</div>