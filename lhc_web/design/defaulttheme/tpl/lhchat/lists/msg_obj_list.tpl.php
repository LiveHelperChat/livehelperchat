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

?>
<div class="message-row<?php echo $msg->user_id == 0 ? ' response' : ' message-admin'.($lastOperatorChanged == true ? ' operator-changes' : '')?>" id="msg-<?php echo $msg->id?>"><div class="msg-date"><?php echo date(erLhcoreClassModule::$dateDateHourFormat,$msg->time);?></div><span class="usr-tit<?php echo $msg->user_id == 0 ? ' vis-tit' : ' op-tit'?>"><?php echo $msg->user_id == 0 ? htmlspecialchars($chat->nick) : htmlspecialchars($msg->name_support) ?>:&nbsp;</span>

    <?php $msgBody = $msg->msg; $paramsMessageRender = array('sender' => $msg->user_id);?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>

    <?php if (isset($metaMessageData)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/meta_render.tpl.php'));?>
    <?php endif; ?>
</div>
<?php endif; endforeach; ?>