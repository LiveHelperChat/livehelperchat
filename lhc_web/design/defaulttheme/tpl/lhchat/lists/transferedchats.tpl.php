<? if (count($transferchats) > 0) { ?>

<? foreach ($transferchats as $chat) : ?>
    <ul class="chat-ul-list">
          <li><img class="action-image" align="absmiddle" onclick="lhinst.startChatNewWindowTransfer('<?=$chat['id'];?>','<?=htmlspecialchars($chat['nick']);?>','<?=$chat['transfer_id'];?>')" src="<?=erLhcoreClassDesign::design('images/icons/application_add.png');?>" alt="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Open in new window');?>" title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Open in new window');?>"><?=$chat['id'];?>. <?=$chat['nick'];?> (<?=date('Y-m-d H:i',$chat['time']);?>)</li>
    </ul>
<? endforeach; ?>

<? } else { ?>

<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Empty...');?>

<? } ?>