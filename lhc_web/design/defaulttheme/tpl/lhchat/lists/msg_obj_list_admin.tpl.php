<?php 

$lastOperatorChanged = false;
$lastOperatorId = false;
$lastOperatorNick = '';

foreach ($messages as $msg) : 

if ($lastOperatorId !== false && ($lastOperatorId != $msg->user_id || $lastOperatorNick != $msg->name_support)) {
    $lastOperatorChanged = true;
    $lastOperatorNick = $msg->name_support;
} else {
    $lastOperatorChanged = false;
}

$lastOperatorId = $msg->user_id;


?>
<?php if ($msg->user_id == -1) : ?>
	<div class="message-row system-response" id="msg-<?php echo $msg->id?>"><div class="msg-date"><?php echo date(erLhcoreClassModule::$dateDateHourFormat,$msg->time);?></div><i><span class="usr-tit sys-tit"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmin','System assistant')?></span><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($msg->msg))?></i></div>
<?php else : ?>
    <?php if ($msg->msg != '') : ?>
        <div class="message-row<?php echo $msg->user_id == 0 ? ' response' : ' message-admin'.($lastOperatorChanged == true ? ' operator-changes' : '')?>" id="msg-<?php echo $msg->id?>"><div class="msg-date"><?php echo date(erLhcoreClassModule::$dateDateHourFormat,$msg->time);?></div><span class="usr-tit<?php echo $msg->user_id == 0 ? ' vis-tit' : ' op-tit'?>"><?php if ($msg->user_id == 0) : ?><i class="material-icons"><?php echo ($chat->device_type == 0 ? 'computer' : ($chat->device_type == 1 ? 'smartphone' : 'tablet'))?></i><?php endif;?><?php echo $msg->user_id == 0 ? htmlspecialchars($chat->nick) : htmlspecialchars($msg->name_support) ?></span>
            <?php $msgBody = $msg->msg; $paramsMessageRender = array('sender' => $msg->user_id);?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>
        </div>
    <?php endif;?>
<?php endif;?>
<?php endforeach;?>