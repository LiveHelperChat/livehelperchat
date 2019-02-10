<?php
if (!isset($prev_nick)){$prev_nick = '';}
foreach ($messages as $msg) : $msgBody = $msg['msg']; ?>
            <?php if ($msg['user_id'] == 0) { ?>
            	<?php if ($prev_nick != $msg['name_support']) : $prev_nick = $msg['name_support'];?>
            		<div class="message-row response operator-changes"><span class="usr-tit radius" data-sender="<?php echo htmlspecialchars($msg['name_support'])?>"><?php echo htmlspecialchars($msg['name_support'])?><div class="msg-date"><?php if (date('Ymd') == date('Ymd',$msg['time'])) {	echo  date(erLhcoreClassModule::$dateHourFormat,$msg['time']);} else { echo date(erLhcoreClassModule::$dateDateHourFormat,$msg['time']);}; ?></div></span>
                        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>
                    </div>
            	<?php else : ?>
            		<div class="message-row response">
                        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>
                    </div>
            	<?php endif;?>
            <?php } else { ?>
            	<?php if ($prev_nick != $chat->nick) : $prev_nick = $chat->nick;?>
                <div class="message-row message-admin operator-changes"><span class="usr-tit" data-sender="<?php echo htmlspecialchars($chat->nick)?>"><?php echo htmlspecialchars($chat->nick)?><div class="msg-date"><?php if (date('Ymd') == date('Ymd',$msg['time'])) { echo  date(erLhcoreClassModule::$dateHourFormat,$msg['time']);} else {	echo date(erLhcoreClassModule::$dateDateHourFormat,$msg['time']);}; ?></div></span>
                    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>
                </div>
                <?php else : ?>
                	<div class="message-row message-admin">
                        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>
                    </div>
                <?php endif;?>
            <?php } ?>
<?php endforeach; ?>