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

if ($msg->meta_msg != '') {
    $metaMessageData = json_decode($msg->meta_msg, true); $messageId = $msg->id;
} else if (isset($metaMessageData)) {
    unset($metaMessageData);
}

$msgRendered = erLhcoreClassBBCode::make_clickable(htmlspecialchars($msg->msg));
$msgRenderedMedia = strip_tags($msgRendered);
$emojiMessage = trim(preg_replace('#(x1F642|[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}])#u','', $msgRendered)) == '';

?>
<div class="message-row<?php echo $msg->user_id == 0 ? ' response' : ' message-admin'.($lastOperatorChanged == true ? ' operator-changes' : '')?>" id="msg-<?php echo $msg->id?>"><div class="msg-date"><?php echo date(erLhcoreClassModule::$dateDateHourFormat,$msg->time);?></div><span class="usr-tit<?php echo $msg->user_id == 0 ? ' vis-tit' : ' op-tit'?>"><?php echo $msg->user_id == 0 ? htmlspecialchars($chat->nick) : htmlspecialchars($msg->name_support) ?>:&nbsp;</span>
    <div class="msg-body<?php ($msgRenderedMedia == '') ? print ' msg-body-media' : ''?><?php ($emojiMessage == true) ? print ' msg-body-emoji' : ''?>"><?php echo $msgRendered?></div>
    <?php if (isset($metaMessageData)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/meta_render.tpl.php'));?>
    <?php endif; ?>
</div>
<?php endif; endforeach; ?>