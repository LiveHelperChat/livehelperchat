<?php if ($chat->status != erLhcoreClassModelChat::STATUS_CHATBOX_CHAT) : ?>
	<?php foreach ($messages as $msg ) : ?>
	<div class="message-row<?php echo $msg['user_id'] == 0 ? ' response' : ''?>"><div class="msg-date"><?php echo date('Y-m-d H:i:s',$msg['time']);?></div><span class="usr-tit"><?php echo $msg['user_id'] == 0 ? htmlspecialchars($chat->nick) : htmlspecialchars($msg['name_support']) ?>:&nbsp;</span><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($msg['msg']))?></div>
	<?php endforeach; ?>
<?php else : ?>
	<?php foreach ($messages as $msg ) : ?>
	<div class="message-row<?php echo $msg['user_id'] == 0 ? ' response' : ''?>"><div class="msg-date"><?php echo date('Y-m-d H:i:s',$msg['time']);?></div><span class="usr-tit"><?php echo $msg['user_id'] == 0 ? htmlspecialchars($msg['name_support']) : htmlspecialchars($chat->nick) ?>:&nbsp;</span><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($msg['msg']))?></div>
	<?php endforeach; ?>
<?php endif;?>