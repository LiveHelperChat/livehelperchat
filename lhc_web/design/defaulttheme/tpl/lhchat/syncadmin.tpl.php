<?php if ($chat->status != erLhcoreClassModelChat::STATUS_CHATBOX_CHAT) : 

    $lastOperatorChanged = false;
    $lastOperatorId = false;
    $lastOperatorNick = '';

    foreach ($messages as $msg) :
    
        if ($lastOperatorId !== false && ($lastOperatorId != $msg['user_id'] || $lastOperatorNick != $msg['name_support'])) {
            $lastOperatorChanged = true;
        } else {
            $lastOperatorChanged = false;
        }
        
        $lastOperatorId = $msg['user_id'];
        $lastOperatorNick = $msg['name_support'];

        $otherOperator = $msg['user_id'] == -2 || (isset($current_user_id) && $msg['user_id'] > 0 && $msg['user_id'] != $current_user_id);

        if ($msg['msg'] == '') {
            continue;
        }

if ($msg['user_id'] == -1) : ?>
<div class="message-row system-response" id="msg-<?php echo $msg['id']?>" title="<?php echo erLhcoreClassChat::formatDate($msg['time']);?>">
    <div class="msg-date"><?php echo erLhcoreClassChat::formatDate($msg['time']);?></div><i><span class="usr-tit sys-tit"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmin','System assistant')?></span>
        <?php $msgBody = $msg['msg']; $paramsMessageRender = array('sender' => $msg['user_id']); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>
    </i>
</div>
<?php else : ?>
<div class="message-row<?php echo $msg['user_id'] == 0 ? ' response' : ' message-admin'.($lastOperatorChanged == true ? ' operator-changes' : '') ?><?php echo $otherOperator == true ? ' other-operator' : ''?>" data-op-id="<?php echo $msg['user_id']?>" title="<?php echo erLhcoreClassChat::formatDate($msg['time']);?>" id="msg-<?php echo $msg['id']?>">
    <div class="msg-date"><?php echo erLhcoreClassChat::formatDate($msg['time']);?></div><span class="usr-tit<?php echo $msg['user_id'] == 0 ? ' vis-tit' : ' op-tit'?>"><?php echo $msg['user_id'] == 0 ? '<i class="material-icons chat-operators mi-fs15 mr-0">'.($chat->device_type == 0 ? '&#xE30A;' : ($chat->device_type == 1 ? '&#xE32C;' : '&#xE32F;')).'</i> '.htmlspecialchars($chat->nick) : '<i class="material-icons chat-operators mi-fs15 mr-0">&#xE851;</i>'.htmlspecialchars($msg['name_support']) ?></span>
        <?php $msgBody = $msg['msg']; $paramsMessageRender = array('sender' => $msg['user_id']);?>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>
    </div>
<?php endif;?>

	<?php endforeach; ?>
<?php else : ?>
	<?php foreach ($messages as $msg ) : ?>
<div class="message-row<?php echo $msg['user_id'] == 0 ? ' response' : ' message-admin'?>" id="msg-<?php echo $msg['id']?>" title="<?php echo erLhcoreClassChat::formatDate($msg['time']);?>">
	<div class="msg-date"><?php echo erLhcoreClassChat::formatDate($msg['time']);?></div>
	<span class="usr-tit<?php echo $msg['user_id'] == 0 ? ' vis-tit' : ' op-tit'?>"><?php echo $msg['user_id'] == 0 ? htmlspecialchars($msg['name_support']) : htmlspecialchars($chat->nick) ?></span>
    <?php $msgBody = $msg['msg']; $paramsMessageRender = array('sender' => $msg['user_id']); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>
</div>
<?php endforeach; ?>
<?php endif;?>