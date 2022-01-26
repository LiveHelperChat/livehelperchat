<?php foreach ($messages as $msg ) :

    if (isset($msg->meta_msg) && $msg->meta_msg != '') {
        $metaMessageData = json_decode($msg->meta_msg, true); $messageId = isset($triggerMessageId) ? $triggerMessageId : $msg->id;
    } else if (isset($metaMessageData)) {
        unset($metaMessageData);
    }

    if (isset($metaMessageData['content']['whisper']) || empty($msg->msg)) {
        continue;
    }

    ?><?php if (!isset($remove_meta) || $remove_meta == false ) :?>[<?php echo date(erLhcoreClassModule::$dateDateHourFormat,$msg->time);?>] [<?php echo $msg->user_id == 0 ? htmlspecialchars($chat->nick) : htmlspecialchars($msg->name_support) ?>] <?php endif;?><?php echo erLhcoreClassBBCodePlain::make_clickable($msg->msg, array('sender' => $msg->user_id)),"\n"?><?php endforeach; ?>