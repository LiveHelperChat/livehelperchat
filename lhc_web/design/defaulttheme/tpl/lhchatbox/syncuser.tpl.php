<?php foreach ($messages as $msg) : ?>
            <?php if ($msg['user_id'] == 0) { ?>
            	<div class="message-row chatbox-row"><span class="usr-tit radius"><?php echo htmlspecialchars($msg['name_support'])?><div class="msg-date"><?php if (date('Ymd') == date('Ymd',$msg['time'])) {	echo  date('H:i:s',$msg['time']);} else { echo date('Y-m-d H:i:s',$msg['time']);}; ?></div></span><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($msg['msg']))?></div>
            <?php } else { ?>
                <div class="message-row chatbox-row chatbox-row-response"><span class="usr-tit"><?php echo htmlspecialchars($chat->nick)?><div class="msg-date"><?php if (date('Ymd') == date('Ymd',$msg['time'])) { echo  date('H:i:s',$msg['time']);} else {	echo date('Y-m-d H:i:s',$msg['time']);}; ?></div></span><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($msg['msg']))?></div>
            <?php } ?>
<?php endforeach; ?>