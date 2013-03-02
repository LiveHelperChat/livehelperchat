<?php if (count($chats) > 0 || count($transferchats) > 0) { ?>
<ul class="no-bullet">
	<?php foreach ($chats as $chat) : ?>
	      <li class="chat-row-<?php echo $chat['id'];?>">
	      <?php if ( !empty($chat['country_code']) ) : ?><img src="<?php echo erLhcoreClassDesign::design('images/flags');?>/<?php echo $chat['country_code']?>.png" alt="<?php echo htmlspecialchars($chat['country_name'])?>" title="<?php echo htmlspecialchars($chat['country_name'])?>" />&nbsp;<?php endif; ?>    		      
	      <?php if ($right === false) : ?><img class="action-image" align="absmiddle" onclick="lhinst.startChat('<?php echo $chat['id'];?>',$('#tabs'),'<?php echo htmlspecialchars($chat['nick']);?>')" src="<?php echo erLhcoreClassDesign::design('images/icons/accept.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Accept chat');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Accept chat');?>"><?php endif; ?>
	      <img class="action-image" align="absmiddle" onclick="lhinst.startChatNewWindow('<?php echo $chat['id'];?>','<?php echo htmlspecialchars($chat['nick']);?>')" src="<?php echo erLhcoreClassDesign::design('images/icons/application_add.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in new window');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in new window');?>">
	      <img class="action-image" align="absmiddle" onclick="lhinst.rejectPendingChat('<?php echo $chat['id'];?>',$('#tabs'))" src="<?php echo erLhcoreClassDesign::design('images/icons/cancel.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Reject chat');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Reject chat');?>">
	      <?php echo $chat['id'];?>. <?php echo htmlspecialchars($chat['nick']);?> (<?php echo date('Y-m-d H:i:s',$chat['time']);?>) (<?php echo htmlspecialchars($chat['name']);?>)
	      </li>
	<?php endforeach; ?>
</ul>
<?php if ($right === false) : ?>
    <?php if (count($transferchats) > 0) : ?>
    
    <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Transfered chats');?></h4>
    
        <?php foreach ($transferchats as $chat) : ?>
            <ul class="no-bullet">
                  <li>                  
                  <img class="action-image" align="absmiddle" onclick="lhinst.startChatTransfer('<?php echo $chat['id'];?>',$('#tabs'),'<?php echo htmlspecialchars($chat['nick']);?>','<?php echo $chat['transfer_id'];?>')" src="<?php echo erLhcoreClassDesign::design('images/icons/accept.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Accept chat');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Accept chat');?>">
                  <img class="action-image" align="absmiddle" onclick="lhinst.startChatNewWindowTransfer('<?php echo $chat['id'];?>','<?php echo htmlspecialchars($chat['nick']);?>','<?php echo $chat['transfer_id'];?>')" src="<?php echo erLhcoreClassDesign::design('images/icons/application_add.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in new window');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in new window');?>"> <?php echo $chat['id'];?>. <?php echo htmlspecialchars($chat['nick']);?> (<?php echo date('Y-m-d H:i:s',$chat['time']);?>)
                  </li>
            </ul>
        <?php endforeach; ?>
                                		
    <?php endif; ?>

<?php endif; ?>

<?php } else { ?>
<p>
<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Empty...');?>
</p>
<?php } ?>