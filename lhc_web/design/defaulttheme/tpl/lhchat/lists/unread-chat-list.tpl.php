<?php if (count($chats) > 0) { ?>
<ul class="no-bullet fs12">
	<?php foreach ($chats as $chat) : ?>
	      <li class="chat-row-<?php echo $chat->id;?>">
	      <?php if ( !empty($chat->country_code) ) : ?><img src="<?php echo erLhcoreClassDesign::design('images/flags');?>/<?php echo $chat->country_code?>.png" alt="<?php echo htmlspecialchars($chat->country_name)?>" title="<?php echo htmlspecialchars($chat->country_name)?>" /><?php endif; ?>
	      <a class="material-icons" title="ID - <?php echo $chat->id;?>" onclick="lhc.previewChat('<?php echo $chat->id;?>')" >info_outline</a><a class="right-action-hide material-icons" data-title="<?php echo erLhcoreClassDesign::shrt($chat->nick,10,'...',30,ENT_QUOTES);?>" onclick="lhinst.startChat('<?php echo $chat->id;?>',$('#tabs'),$(this).attr('data-title'))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Add chat');?>">chat</a><a class="material-icons" data-title="<?php echo htmlspecialchars($chat->nick,ENT_QUOTES);?>" onclick="lhinst.startChatNewWindow('<?php echo $chat->id;?>',$(this).attr('data-title'))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in a new window');?>">open_in_new</a> <?php echo htmlspecialchars($chat->nick);?>, <?php echo $chat->time_created_front;?>, <?php echo htmlspecialchars($chat->department);?> | <?php
	      $diff = time()-$chat->last_user_msg_time;
	      $hours = floor($diff/3600);
	      $minits = floor(($diff - ($hours * 3600))/60);
	      $seconds = ($diff - ($hours * 3600) - ($minits * 60));
	      ?><b><?php echo $hours?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','h.');?> <?php echo $minits ?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','m.');?> <?php echo $seconds?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','s.');?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','ago');?>.</b>
	      </li>
	<?php endforeach; ?>
</ul>
<?php } else { ?>
<p>
<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Empty...');?>
</p>
<?php } ?>