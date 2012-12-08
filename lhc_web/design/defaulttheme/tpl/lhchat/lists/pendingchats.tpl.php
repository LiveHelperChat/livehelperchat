<? if (count($chats) > 0 || count($transferchats) > 0) { ?>
<ul class="chat-ul-list">
	<? foreach ($chats as $chat) : ?>
	      <li class="chat-row-<?=$chat['id'];?>">
	      <? if ($right === false) : ?><img class="action-image" align="absmiddle" onclick="lhinst.startChat('<?=$chat['id'];?>',$('#tabs'),'<?=htmlspecialchars($chat['nick']);?>')" src="<?=erLhcoreClassDesign::design('images/icons/accept.png');?>" alt="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Accept chat');?>" title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Accept chat');?>"><? endif; ?>
	      <img class="action-image" align="absmiddle" onclick="lhinst.startChatNewWindow('<?=$chat['id'];?>','<?=htmlspecialchars($chat['nick']);?>')" src="<?=erLhcoreClassDesign::design('images/icons/application_add.png');?>" alt="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in new window');?>" title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in new window');?>">
	      <img class="action-image" align="absmiddle" onclick="lhinst.rejectPendingChat('<?=$chat['id'];?>',$('#tabs'))" src="<?=erLhcoreClassDesign::design('images/icons/cancel.png');?>" alt="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Reject chat');?>" title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Reject chat');?>">
	      <?=$chat['id'];?>. <?=htmlspecialchars($chat['nick']);?> (<?=date('Y-m-d H:i',$chat['time']);?>) (<?=$chat['name'];?>)
	      </li>
	<? endforeach; ?>
</ul>
<? if ($right === false) : ?>
    <? if (count($transferchats) > 0) : ?>
    
    <h4><?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Transfered chats');?></h4>
    
        <? foreach ($transferchats as $chat) : ?>
            <ul class="chat-ul-list">
                  <li>                  
                  <img class="action-image" align="absmiddle" onclick="lhinst.startChatTransfer('<?=$chat['id'];?>',$('#tabs'),'<?=htmlspecialchars($chat['nick']);?>','<?=$chat['transfer_id'];?>')" src="<?=erLhcoreClassDesign::design('images/icons/accept.png');?>" alt="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Accept chat');?>" title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Accept chat');?>">
                  <img class="action-image" align="absmiddle" onclick="lhinst.startChatNewWindowTransfer('<?=$chat['id'];?>','<?=htmlspecialchars($chat['nick']);?>','<?=$chat['transfer_id'];?>')" src="<?=erLhcoreClassDesign::design('images/icons/application_add.png');?>" alt="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in new window');?>" title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in new window');?>"> <?=$chat['id'];?>. <?=$chat['nick'];?> (<?=date('Y-m-d H:i',$chat['time']);?>)
                  </li>
            </ul>
        <? endforeach; ?>
                                		
    <? endif; ?>

<? endif; ?>

<? } else { ?>

<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Empty...');?>

<? } ?>