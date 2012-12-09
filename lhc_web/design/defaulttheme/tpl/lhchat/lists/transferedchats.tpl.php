<?php if (count($transferchats) > 0) { ?>

<?php foreach ($transferchats as $chat) : ?>
    <ul class="chat-ul-list">
          <li><img class="action-image" align="absmiddle" onclick="lhinst.startChatNewWindowTransfer('<?php echo $chat['id'];?>','<?php echo htmlspecialchars($chat['nick']);?>','<?php echo $chat['transfer_id'];?>')" src="<?php echo erLhcoreClassDesign::design('images/icons/application_add.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Open in new window');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Open in new window');?>"><?php echo $chat['id'];?>. <?php echo $chat['nick'];?> (<?php echo date('Y-m-d H:i',$chat['time']);?>)</li>
    </ul>
<?php endforeach; ?>

<?php } else { ?>

<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Empty...');?>

<?php } ?>