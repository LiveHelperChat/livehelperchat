<section>
    <p class="title" data-section-title><a href="#panel2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chats')?></a></p>
    <div class="content" data-section-content>
      	<div>
		<ul class="foot-print-content circle mb0" style="max-height: 170px;">
		<?php foreach (erLhcoreClassChat::getList(array('limit' => 100, 'filter' => array('online_user_id' => $online_user->id))) as $chat) : ?>
		<li>
		  <?php if ( !empty($chat->country_code) ) : ?><img src="<?php echo erLhcoreClassDesign::design('images/flags');?>/<?php echo $chat->country_code?>.png" alt="<?php echo htmlspecialchars($chat->country_name)?>" title="<?php echo htmlspecialchars($chat->country_name)?>" />&nbsp;<?php endif; ?>
	      <img class="action-image" align="absmiddle" data-title="<?php echo htmlspecialchars($chat->nick,ENT_QUOTES);?>" onclick="lhinst.startChatNewWindow('<?php echo $chat->id;?>',$(this).attr('data-title'))" src="<?php echo erLhcoreClassDesign::design('images/icons/application_add.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in a new window');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in a new window');?>"><?php echo $chat->id;?>. <?php echo htmlspecialchars($chat->nick);?> (<?php echo date('Y-m-d H:i:s',$chat->time);?>) (<?php echo htmlspecialchars($chat->department);?>)
		</li>
		<?php endforeach;?>
		</ul>
		</div>
    </div>
</section>