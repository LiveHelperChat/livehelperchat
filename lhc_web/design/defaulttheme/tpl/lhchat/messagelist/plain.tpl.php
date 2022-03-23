<?php foreach ($messages as $msg ) :

    if (isset($msg->meta_msg) && $msg->meta_msg != '') {
        $metaMessageData = json_decode($msg->meta_msg, true);
        $messageId = isset($triggerMessageId) ? $triggerMessageId : $msg->id;
    } else if (isset($metaMessageData)) {
        unset($metaMessageData);
    }

    $metaRenderedAppend = '';
    $subtype = '';
    if (isset($metaMessageData) && is_array($metaMessageData)) {
        foreach ($metaMessageData['content'] as $type => $metaMessage) {
            if ( $type == 'html' ) {
                $metaRenderedAppend .= '[html]' . str_replace('{msg_id}', $msg->id, $metaMessage['content']) . '[/html]'. "\n";
            } elseif ( $type == 'attr_options' ) {
                if (isset($metaMessage['hide_text_area'])) {
                    $metaRenderedAppend .=  '('.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/plain','attribute').') ' . ($metaMessage['hide_text_area'] == true ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/plain','hide textarea') : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/plain','show textarea')) . "\n";
                }
            } elseif ( $type == 'execute_js' ) {
                $metaRenderedAppend .= '('. erTranslationClassLhTranslation::getInstance()->getTranslation('chat/plain','execute javascript').') ' .  $metaMessage['payload']. "\n";
            } elseif ( $type == 'typing' ) {
                $metaRenderedAppend .= '(' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/plain','typing') . ') ' .  $metaMessage['text']. "\n";
            } elseif ( $type == 'quick_replies' || $type == 'buttons_generic') {
                foreach ($metaMessage as $item) {
                    $metaRenderedAppend .= "[button]".$item['content']['name']."[button] ";
                }
                $metaRenderedAppend .= "\n";
            }
        }
    }

    ?><?php if (!isset($remove_meta) || $remove_meta == false ) :?>[<?php echo date(erLhcoreClassModule::$dateDateHourFormat,$msg->time);?>] [<?php echo $msg->user_id == 0 ? htmlspecialchars($chat->nick) : htmlspecialchars($msg->user_id == -1 ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/plain','System assistant') : $msg->name_support) ?>]<?php isset($metaMessageData['content']['whisper']) ? print ' (' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/plain','whisper') . ')' : ''?> <?php endif;?><?php echo trim(erLhcoreClassBBCodePlain::make_clickable($msg->msg, array('sender' => $msg->user_id)). "\n" . $metaRenderedAppend),"\n"?><?php endforeach; ?>