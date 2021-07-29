<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/chat_nick_override_multiinclude.tpl.php'));?>
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

        if ($msg['meta_msg'] != '') {
            $metaMessageData = json_decode($msg['meta_msg'], true); $messageId = $msg['id'];
        } else if (isset($metaMessageData)) {
            unset($metaMessageData);
        }
        
        // We skip render only if message is empty and it's not one of the supported admin meta messages
        if ($msg['msg'] == '' &&
            (!isset($metaMessageData['content']['text_conditional'])) &&
            (!isset($metaMessageData['content']['chat_operation'])) &&
            (!isset($metaMessageData['content']['html']['content'])) &&
            (!isset($metaMessageData['content']['button_message']))) {
            continue;
        }

if ($msg['user_id'] == -1) : ?>
<div class="message-row system-response" id="msg-<?php echo $msg['id']?>" title="<?php echo erLhcoreClassChat::formatDate($msg['time']);?>">
    <span class="usr-tit sys-tit"><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmin','System assistant')?></i><span class="msg-date text-white pl-2 font-weight-normal"><?php echo erLhcoreClassChat::formatDate($msg['time']);?></span></span>

        <?php if ($msg['msg'] != '') : ?>
            <i><?php $msgBody = $msg['msg']; $paramsMessageRender = array('sender' => $msg['user_id'], 'html_as_text' => true); ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?></i>
        <?php endif; ?>

        <?php if (isset($metaMessageData)) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/meta_render_admin.tpl.php'));?>
        <?php endif; ?>
</div>
<?php else : ?>
<div class="message-row<?php echo $msg['user_id'] == 0 ? ' response' : ' message-admin'.($lastOperatorChanged == true ? ' operator-changes' : '') ?><?php echo $otherOperator == true ? ' other-operator' : ''?>" data-op-id="<?php echo $msg['user_id']?>" title="<?php echo erLhcoreClassChat::formatDate($msg['time']);?>" id="msg-<?php echo $msg['id']?>">
    <span class="usr-tit <?php echo $msg['user_id'] == 0 ? ' vis-tit' : ' op-tit'?>"><?php if ($msg['user_id'] != 0) : ?><span class="msg-date text-muted font-weight-normal pr-2"><?php echo erLhcoreClassChat::formatDate($msg['time']);?></span><?php endif;?><?php echo $msg['user_id'] == 0 ? '<i class="material-icons chat-operators mi-fs15 mr-0 pb-1">'.($chat->device_type == 0 ? '&#xE30A;' : ($chat->device_type == 1 ? '&#xE32C;' : '&#xE32F;')).'</i> '.htmlspecialchars(isset($lhcNickAlias) ? $lhcNickAlias : $chat->nick) : '<i class="material-icons chat-operators mi-fs15 mr-0 pb-1">&#xE851;</i>'.htmlspecialchars($msg['name_support']) ?><?php if ($msg['user_id'] == 0) : ?><span class="pl-2 msg-date text-muted font-weight-normal"><?php echo erLhcoreClassChat::formatDate($msg['time']);?></span><?php endif;?></span>
        <?php $msgBody = $msg['msg']; $paramsMessageRender = array('sender' => $msg['user_id'], 'html_as_text' => true);?>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>

        <?php if (isset($metaMessageData)) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/meta_render_admin.tpl.php'));?>
        <?php endif; ?>

    </div>
<?php endif;?>

	<?php endforeach; ?>
<?php else : ?>
	<?php foreach ($messages as $msg ) : ?>
<div class="message-row<?php echo $msg['user_id'] == 0 ? ' response' : ' message-admin'?>" id="msg-<?php echo $msg['id']?>" title="<?php echo erLhcoreClassChat::formatDate($msg['time']);?>">
	<div class="msg-date"><?php echo erLhcoreClassChat::formatDate($msg['time']);?></div>
	<span class="usr-tit<?php echo $msg['user_id'] == 0 ? ' vis-tit' : ' op-tit'?>"><?php echo $msg['user_id'] == 0 ? htmlspecialchars($msg['name_support']) : htmlspecialchars($chat->nick) ?></span>
    <?php $msgBody = $msg['msg']; $paramsMessageRender = array('sender' => $msg['user_id'], 'html_as_text' => true); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>
</div>
<?php endforeach; ?>
<?php endif;?>