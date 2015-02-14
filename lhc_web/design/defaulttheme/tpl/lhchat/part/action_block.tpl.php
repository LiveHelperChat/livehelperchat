
	<div class="form-group" id="action-block-row-<?php echo $chat->id?>">
		<div class="send-row<?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?> hide<?php endif;?>">
		
		<div class="btn-group btn-group-justified">
			<a href="#" class="btn btn-default" onclick="return lhinst.addmsgadmin('<?php echo $chat->id?>')"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Send')?></a>
						
			<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhspeech','use')) : ?>
			<a class="btn btn-default icon-mic" href="#" id="mic-chat-<?php echo $chat->id?>" onclick="return lhc.methodCall('lhc.speak','listen',{'chat_id':'<?php echo $chat->id?>'})"></a></li>
			<?php endif;?>
									
			<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhspeech','change_chat_recognition')) : ?>
			<a class="btn btn-default icon-mic" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Choose other than default recognition language')?>" onclick="lhinst.speechLanguage('<?php echo $chat->id?>')"> <i class="icon-tools"></i></a></li>
			<?php endif;?>
			
			<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Send delayed canned message instantly')?>" href="#" class="btn btn-default icon-mail" onclick="return lhinst.sendCannedMessage('<?php echo $chat->id?>',$(this))"></a></li>
		</div>
		
		</div>
		<?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?><input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Reopen chat')?>" class="small button secondary round" data-id="<?php echo $chat->id?>" onclick="lhinst.reopenchat($(this))" /><?php endif;?>
	</div>
	
	<div class="row">
		<div class="col-xs-8">
            <select class="form-control" name="CannedMessage-<?php echo $chat->id?>" id="id_CannedMessage-<?php echo $chat->id?>">
            	<option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Select a canned message')?></option>
            <?php 
            $nameSupport = (string)erLhcoreClassUser::instance()->getUserData(true)->name_support;
            foreach (erLhcoreClassModelCannedMsg::getCannedMessages($chat->dep_id,erLhcoreClassUser::instance()->getUserID()) as $item) : ?>
            	<option data-delay="<?php echo $item->delay?>" value="<?php echo $item->id?>"><?php echo htmlspecialchars(str_replace(array('{nick}','{operator}'), array($chat->nick,$nameSupport), $item->msg))?></option>
            <?php endforeach;?>
            </select>
         </div>
		<div class="col-xs-4 sub-action-chat">
			<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Fill textarea with canned message')?>" href="#" onclick="$('#CSChatMessage-<?php echo $chat->id?>').val(($('#id_CannedMessage-<?php echo $chat->id?>').val() > 0) ? $('#id_CannedMessage-<?php echo $chat->id?>').find(':selected').text() : '');return false;" class="btn btn-default icon-pencil"></a>
		</div>
	</div>

	
