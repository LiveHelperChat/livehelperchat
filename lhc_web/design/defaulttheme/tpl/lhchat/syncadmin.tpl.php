<?php if ($chat->status != erLhcoreClassModelChat::STATUS_CHATBOX_CHAT) : ?>
	<?php foreach ($messages as $msg ) : ?>

		<?php if ($msg['user_id'] == -1) : ?>
			<div class="message-row system-response" id="msg-<?php echo $msg['id']?>"><div class="msg-date"><?php echo erLhcoreClassChat::formatDate($msg['time']);?></div><i><span class="usr-tit"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmin','System assistant')?>:&nbsp;</span><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($msg['msg']))?></i></div>
		<?php else : ?>
			<div class="message-row<?php echo $msg['user_id'] == 0 ? ' response' : ''?>" id="msg-<?php echo $msg['id']?>"><div class="msg-date"><?php echo erLhcoreClassChat::formatDate($msg['time']);?></div><span class="usr-tit"><?php echo $msg['user_id'] == 0 ? htmlspecialchars($chat->nick) : htmlspecialchars($msg['name_support']) ?>:&nbsp;</span><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($msg['msg']))?></div>
		<?php endif;?>

	<?php endforeach; ?>
<?php else : ?>
	<?php foreach ($messages as $msg ) : ?>
	<div class="message-row<?php echo $msg['user_id'] == 0 ? ' response' : ''?>" id="msg-<?php echo $msg['id']?>"><div class="msg-date"><?php echo erLhcoreClassChat::formatDate($msg['time']);?></div><span class="usr-tit"><?php echo $msg['user_id'] == 0 ? htmlspecialchars($msg['name_support']) : htmlspecialchars($chat->nick) ?>:&nbsp;</span><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($msg['msg']))?></div>
	<?php endforeach; ?>
<?php endif;?>