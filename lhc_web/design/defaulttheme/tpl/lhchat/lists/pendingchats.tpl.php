<?php if (count($chats) > 0) { ?>
<ul class="no-bullet small-list">
	<?php foreach ($chats as $chat) : ?>
	      <li class="chat-row-<?php echo $chat->id;?>">
	      <?php if ( !empty($chat->country_code) ) : ?><img src="<?php echo erLhcoreClassDesign::design('images/flags');?>/<?php echo $chat->country_code?>.png" alt="<?php echo htmlspecialchars($chat->country_name)?>" title="<?php echo htmlspecialchars($chat->country_name)?>" /><?php endif; ?>
	      <a class="icon-info" title="ID - <?php echo $chat->id;?>" onclick="lhinst.previewChat('<?php echo $chat->id;?>')" ></a><a class="icon-reply" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Redirect user to contact form.');?>" onclick="lhinst.redirectContact('<?php echo $chat->id;?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Are you sure?');?>')" ></a><a class="right-action-hide icon-chat" data-title="<?php echo erLhcoreClassDesign::shrt($chat->nick,10,'...',30,ENT_QUOTES);?>" onclick="lhinst.startChat('<?php echo $chat->id;?>',$('#tabs'),$(this).attr('data-title'))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Accept chat');?>"></a><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in a new window');?>" class="icon-popup" onclick="lhinst.startChatNewWindow('<?php echo $chat->id;?>',$(this).attr('data-title'))" data-title="<?php echo htmlspecialchars($chat->nick,ENT_QUOTES);?>"></a><?php echo htmlspecialchars($chat->nick);?>, <?php echo $chat->time_created_front;?>, <?php echo htmlspecialchars($chat->department);?>
	      </li>
	<?php endforeach; ?>
</ul>
<?php } else { ?>
<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Empty...');?></p>
<?php } ?>