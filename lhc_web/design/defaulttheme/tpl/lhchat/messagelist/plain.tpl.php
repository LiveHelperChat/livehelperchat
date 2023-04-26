<?php

$separatorMessage = isset($render_as_html) && $render_as_html == true ? "<br />" : "\n";

foreach ($messages as $msg ) :

    if (isset($see_sensitive_information) && $see_sensitive_information === false) {
        $msg->msg = \LiveHelperChat\Models\LHCAbstract\ChatMessagesGhosting::maskMessage($msg->msg);
    }

    if (isset($msg->meta_msg) && $msg->meta_msg != '') {
        $metaMessageData = json_decode($msg->meta_msg, true);
        $messageId = isset($triggerMessageId) ? $triggerMessageId : $msg->id;
    } else if (isset($metaMessageData)) {
        unset($metaMessageData);
    }

    $metaRenderedAppend = '';
    $subtype = '';
    if (isset($metaMessageData) && is_array($metaMessageData)) {
        if (isset($metaMessageData['content'])){
            foreach ($metaMessageData['content'] as $type => $metaMessage) {
                if ( $type == 'html' ) {
                    if (isset($render_as_html) && $render_as_html == true){
                        $metaRenderedAppend .= str_replace('{msg_id}', $msg->id, $metaMessage['content']) . $separatorMessage;
                    } else {
                        $metaRenderedAppend .= '[html]' . str_replace('{msg_id}', $msg->id, $metaMessage['content']) . '[/html]'. $separatorMessage;
                    }
                } elseif ( $type == 'attr_options' ) {
                    if (isset($metaMessage['hide_text_area'])) {
                        $valMessage = '('.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/plain','attribute').') ' . ($metaMessage['hide_text_area'] == true ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/plain','hide textarea') : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/plain','show textarea'));
                        if (isset($render_as_html_params['bot_attr_html'])) {
                            $valMessage = str_replace('{val}',$valMessage,$render_as_html_params['bot_attr_html']);
                        }
                        $metaRenderedAppend .= $valMessage . $separatorMessage;
                    }
                } elseif ( $type == 'execute_js' ) {
                    $metaRenderedAppend .= '('. erTranslationClassLhTranslation::getInstance()->getTranslation('chat/plain','execute javascript').') ' .  $metaMessage['payload'] . $separatorMessage;
                } elseif ( $type == 'typing' ) {
                    $valMessage = '(' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/plain','typing') . ') ' .  $metaMessage['text'];
                    if (isset($render_as_html_params['bot_attr_html'])) {
                        $valMessage = str_replace('{val}',$valMessage,$render_as_html_params['bot_attr_html']);
                    }
                    $metaRenderedAppend .= $valMessage . $separatorMessage;
                } elseif ( $type == 'quick_replies' || $type == 'buttons_generic') {
                    foreach ($metaMessage as $item) {
                        if (isset($render_as_html_params['bot_button_html'])) {
                            $metaRenderedAppend .= str_replace('{val}', $item['content']['name'], $render_as_html_params['bot_button_html']);
                        } else {
                            $metaRenderedAppend .= "[button]".$item['content']['name']."[button] ";
                        }
                    }
                    $metaRenderedAppend .= $separatorMessage;
                }
            }
        }
    }

    if (isset($_GET['meta']) && $_GET['meta'] == 'true' && $msg->meta_msg != '') {
        $metaRenderedAppend .= "====================={$separatorMessage}" . $msg->meta_msg . "{$separatorMessage}====================={$separatorMessage}";
    }

    if (!isset($remove_meta) || $remove_meta == false ) {

        $nickValue = '[' . ($msg->user_id == 0 ? htmlspecialchars($chat->nick) : htmlspecialchars($msg->user_id == -1 ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/plain','System assistant') : $msg->name_support)) . ']';

        $date = '[' . date(erLhcoreClassModule::$dateDateHourFormat,$msg->time) . ']';

        if (isset($render_as_html) && $render_as_html == true) {
            if ($msg->user_id == 0) {
                $nickValue = str_replace('{val}', $nickValue, $render_as_html_params['visitor_name_html']);
            } elseif ($msg->user_id == -1) {
                $nickValue = str_replace('{val}', $nickValue, $render_as_html_params['system_name_html']);
            } elseif ($msg->user_id == -2) {
                $nickValue = str_replace('{val}', $nickValue, $render_as_html_params['bot_name_html']);
            } else {
                $nickValue = str_replace('{val}', $nickValue, $render_as_html_params['operator_name_html']);
            }
            if (isset($render_as_html_params['msg_date_html']) && $render_as_html_params['msg_date_html'] != '') {
                $date = str_replace('{val}', date(erLhcoreClassModule::$dateDateHourFormat,$msg->time), $render_as_html_params['msg_date_html']);
            }
        }

        $nick = $date . ' '. $nickValue . ' ' . (isset($metaMessageData['content']['whisper']) ? '- (' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/plain','whisper') . ')' : '');
    }

    // Nothing to render
    if ($msg->msg == "" && preg_replace("/((<br \/>)|(<br\/>)|(<br>)|(<br >))+$/","",$metaRenderedAppend) == "") {
        continue;
    }

    ?><?php if (!isset($remove_meta) || $remove_meta == false ){ echo $nick;}?><?php echo trim(erLhcoreClassBBCodePlain::make_clickable($msg->msg, array('sender' => $msg->user_id)). ($metaRenderedAppend != "" ? ($msg->msg != '' ? $separatorMessage : '') . (isset($render_as_html) && $render_as_html == true ? preg_replace("/((<br \/>)|(<br\/>)|(<br>)|(<br >))+$/","", $metaRenderedAppend) : $metaRenderedAppend) : "")),$separatorMessage?><?php endforeach; ?>