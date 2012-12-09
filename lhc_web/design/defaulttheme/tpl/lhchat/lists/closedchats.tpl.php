<?php if (count($chats) > 0) { ?>
<ul class="chat-ul-list">
<?php foreach ($chats as $chat) : ?>
      <li class="chat-row-<?php echo $chat['id'];?>">    		      
           <img class="action-image" align="absmiddle" onclick="lhinst.startChat('<?php echo $chat['id'];?>',$('#tabs'),'<?php echo htmlspecialchars($chat['nick']);?>')" src="<?php echo erLhcoreClassDesign::design('images/icons/add.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Add chat');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Add chat');?>">
           <img class="action-image" align="absmiddle" onclick="lhinst.startChatNewWindow('<?php echo $chat['id'];?>','<?php echo htmlspecialchars($chat['nick']);?>')" src="<?php echo erLhcoreClassDesign::design('images/icons/application_add.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in new window');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in new window');?>">
           <img class="action-image" align="absmiddle" onclick="lhinst.deleteChat('<?php echo $chat['id'];?>',$('#tabs'),false)" src="<?php echo erLhcoreClassDesign::design('images/icons/delete.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Delete chat');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Delete chat');?>"> <?php echo $chat['id'];?>. <?php echo htmlspecialchars($chat['nick']);?> (<?php echo date('Y-m-d H:i',$chat['time']);?>) (<?php echo $chat['name'];?>)
      </li>
<?php endforeach; ?>
</ul>
<?php } else { ?>

<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Empty...');?>

<?php } ?>