<? if (count($chats) > 0) { ?>
<ul class="chat-ul-list">
	<? foreach ($chats as $chat) : ?>
	      <li class="chat-row-<?=$chat['id'];?>">    		      
	      <? if ($right === false) : ?><img class="action-image" align="absmiddle" onclick="lhinst.startChat('<?=$chat['id'];?>',$('#tabs'),'<?=htmlspecialchars($chat['nick']);?>')" src="<?=erLhcoreClassDesign::design('images/icons/add.png');?>" alt="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Add chat');?>" title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Add chat');?>"><? endif; ?>
	      <img class="action-image" align="absmiddle" onclick="lhinst.startChatNewWindow('<?=$chat['id'];?>','<?=htmlspecialchars($chat['nick']);?>')" src="<?=erLhcoreClassDesign::design('images/icons/application_add.png');?>" alt="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in new window');?>" title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in new window');?>">
	      <img class="action-image" align="absmiddle" onclick="lhinst.closeActiveChatDialog('<?=$chat['id'];?>',$('#tabs'),false)" src="<?=erLhcoreClassDesign::design('images/icons/cancel.png');?>" alt="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Close chat');?>" title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Close chat');?>">
	      <img class="action-image" align="absmiddle" onclick="lhinst.deleteChat('<?=$chat['id'];?>',$('#tabs'))" src="<?=erLhcoreClassDesign::design('images/icons/delete.png');?>" alt="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Delete chat');?>" title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Delete chat');?>"> <?=$chat['id'];?>. <?=htmlspecialchars($chat['nick']);?> (<?=date('Y-m-d H:i',$chat['time']);?>) (<?=$chat['name'];?>)
	      </li>
	<? endforeach; ?>
</ul>
<? } else { ?>

<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Empty...');?>

<? } ?>