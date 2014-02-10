<?php if (count($transferchats) > 0) { ?>
<ul class="no-bullet small-list">
<?php foreach ($transferchats as $chat) : ?>
    		  <li>
    		  <img class="action-image right-action-hide" data-title="<?php echo erLhcoreClassDesign::shrt($chat['nick'],10,'...',30,ENT_QUOTES);?>" align="absmiddle" onclick="lhinst.startChatTransfer('<?php echo $chat['id'];?>',$('#tabs'),$(this).attr('data-title'),'<?php echo $chat['transfer_id'];?>')" src="<?php echo erLhcoreClassDesign::design('images/icons/accept.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Accept chat');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Accept chat');?>">
              <img class="action-image" align="absmiddle" data-title="<?php echo erLhcoreClassDesign::shrt($chat['nick'],10,'...',30,ENT_QUOTES);?>" onclick="lhinst.startChatNewWindowTransfer('<?php echo $chat['id'];?>',$(this).attr('data-title'),'<?php echo $chat['transfer_id'];?>')" src="<?php echo erLhcoreClassDesign::design('images/icons/application_add.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in a new window');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in a new window');?>"> <?php echo $chat['id'];?>. <?php echo htmlspecialchars($chat['nick']);?> (<?php echo date(erLhcoreClassModule::$dateDateHourFormat,$chat['time']);?>)
              </li>
<?php endforeach; ?>
</ul>
<?php } else { ?>
<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Empty...');?></p>
<?php } ?>