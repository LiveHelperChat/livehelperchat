<?php 

$lastOperatorChanged = false;
$lastOperatorId = false;

foreach ($messages as $msg ) : if ($msg->user_id > -1 || $msg->user_id == -2) : 

if ($lastOperatorId !== false && $lastOperatorId != $msg->user_id) {
    $lastOperatorChanged = true;
} else {
    $lastOperatorChanged = false;
}

$lastOperatorId = $msg->user_id;

?>
<div class="message-row<?php echo $msg->user_id == 0 ? ' response' : ' message-admin'.($lastOperatorChanged == true ? ' operator-changes' : '')?>" id="msg-<?php echo $msg->id?>"><div class="msg-date"><?php echo date(erLhcoreClassModule::$dateDateHourFormat,$msg->time);?></div><span class="usr-tit<?php echo $msg->user_id == 0 ? ' vis-tit' : ' op-tit'?>"><?php echo $msg->user_id == 0 ? htmlspecialchars($chat->nick) : htmlspecialchars($msg->name_support) ?>:&nbsp;</span><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($msg->msg))?></div>
<?php endif; endforeach; ?>