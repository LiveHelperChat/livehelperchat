<?php if ($msg['user_id'] > -1) : ?>
	<?php if ($msg['user_id'] == 0) { ?>
	        <div class="message-row response" id="msg-<?php echo $msg['id']?>"><div class="msg-date"><?php if (date('Ymd') == date('Ymd',$msg['time'])) {	echo  date(erLhcoreClassModule::$dateHourFormat,$msg['time']);} else { echo date(erLhcoreClassModule::$dateDateHourFormat,$msg['time']);}; ?></div><span class="usr-tit<?php echo $msg['user_id'] == 0 ? ' vis-tit' : ' op-tit'?>"><?php echo htmlspecialchars($chat->nick)?></span><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($msg['msg']))?></div>
	 <?php } else { ?>
	        <div class="message-row message-admin" id="msg-<?php echo $msg['id']?>"><div class="msg-date"><?php if (date('Ymd') == date('Ymd',$msg['time'])) { echo  date(erLhcoreClassModule::$dateHourFormat,$msg['time']);} else {	echo date(erLhcoreClassModule::$dateDateHourFormat,$msg['time']);}; ?></div><span class="usr-tit<?php echo $msg['user_id'] == 0 ? ' vis-tit' : ' op-tit'?>"><?php echo htmlspecialchars($msg['name_support'])?></span><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($msg['msg']))?></div>
	<?php } ?>
<?php endif;?>