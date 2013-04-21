<?php if (count($transferchats) > 0) { ?>
<ul class="no-bullet small-list">
<?php foreach ($transferchats as $chat) : ?>
    		  <li>
    		  <img class="action-image right-action-hide" align="absmiddle" onclick="lhinst.startChatTransfer('<?php echo $chat['id'];?>',$('#tabs'),'<?php echo htmlspecialchars($chat['nick']);?>','<?php echo $chat['transfer_id'];?>')" src="<?php echo erLhcoreClassDesign::design('images/icons/accept.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Accept chat');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Accept chat');?>">
              <img class="action-image" align="absmiddle" onclick="lhinst.startChatNewWindowTransfer('<?php echo $chat['id'];?>','<?php echo htmlspecialchars($chat['nick']);?>','<?php echo $chat['transfer_id'];?>')" src="<?php echo erLhcoreClassDesign::design('images/icons/application_add.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in new window');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in new window');?>"> <?php echo $chat['id'];?>. <?php echo htmlspecialchars($chat['nick']);?> (<?php echo date('Y-m-d H:i:s',$chat['time']);?>)
              </li>
<?php endforeach; ?>
</ul>
<?php } else { ?>
<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Empty...');?></p>
<?php } ?>