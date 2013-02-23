<?php foreach ($messages as $msg ) : ?>
<div class="message-row response"><div class="msg-date"><?php 

if (date('Ymd') == date('Ymd',$msg['time'])) {
	echo  date('H:i:s',$msg['time']);
} else {
	echo date('Y-m-d H:i:s',$msg['time']);
}

?></div><span class="usr-tit"><?php if ($sync_mode == 'widget') : ?><img src="<?php echo erLhcoreClassDesign::design('images/icons/user_suit.png');?>" title="<?php echo htmlspecialchars($msg['name_support'])?>" alt="<?php echo htmlspecialchars($msg['name_support'])?>" /><?php else : ?><?php echo htmlspecialchars($msg['name_support'])?>:<?php endif;?>&nbsp;</span><?php echo nl2br(htmlspecialchars(trim($msg['msg'])));?></div>
<?php endforeach; ?>