<?php
if (!isset($prev_nick)){$prev_nick = '';}
foreach ($messages as $msg) : ?>
            <?php if ($msg['user_id'] == 0) { ?>
            	<?php if ($prev_nick != $msg['name_support']) : $prev_nick = $msg['name_support'];?>
            		<div class="message-row chatbox-row"><span class="usr-tit radius"><?php echo htmlspecialchars($msg['name_support'])?><div class="msg-date"><?php if (date('Ymd') == date('Ymd',$msg['time'])) {	echo  date('H:i:s',$msg['time']);} else { echo date('Y-m-d H:i:s',$msg['time']);}; ?></div></span><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($msg['msg']))?></div>
            	<?php else : ?>
            		<div class="message-row-in"><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($msg['msg']))?></div>
            	<?php endif;?>
            <?php } else { ?>
            	<?php if ($prev_nick != $chat->nick) : $prev_nick = $chat->nick;?>
                <div class="message-row chatbox-row chatbox-row-response"><span class="usr-tit"><?php echo htmlspecialchars($chat->nick)?><div class="msg-date"><?php if (date('Ymd') == date('Ymd',$msg['time'])) { echo  date('H:i:s',$msg['time']);} else {	echo date('Y-m-d H:i:s',$msg['time']);}; ?></div></span><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($msg['msg']))?></div>
                <?php else : ?>
                	<div class="message-row-in"><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($msg['msg']))?></div>
                <?php endif;?>
            <?php } ?>
<?php endforeach; ?>