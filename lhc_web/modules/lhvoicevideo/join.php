<?php

erLhcoreClassRestAPIHandler::setHeaders();
erTranslationClassLhTranslation::$htmlEscape = false;

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['id']);

$validStatuses = array(
    erLhcoreClassModelChat::STATUS_PENDING_CHAT,
    erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,
    erLhcoreClassModelChat::STATUS_BOT_CHAT,
);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.validstatus_chat',array('chat' => & $chat, 'valid_statuses' => & $validStatuses));

if (erLhcoreClassVoiceVideo::isEnabled() && $chat instanceof erLhcoreClassModelChat && $chat->hash === $Params['user_parameters']['hash'] && (in_array($chat->status,$validStatuses)) && !in_array($chat->status_sub, array(erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW,erLhcoreClassModelChat::STATUS_SUB_CONTACT_FORM))) // Allow add messages only if chat is active
{
    $vvcall = erLhcoreClassModelChatVoiceVideo::getInstance($chat->id);

    if ($Params['user_parameters_unordered']['action'] == 'cancel') {

        if ($vvcall->vi_status != erLhcoreClassModelChatVoiceVideo::STATUS_VI_PENDING) {
            erLhcoreClassVoiceVideo::trackCallEnd($chat, 'visitor_left');
        }

        $vvcall->vi_status = erLhcoreClassModelChatVoiceVideo::STATUS_VI_PENDING;

        $chat->operation_admin .= "lhinst.updateVoteStatus(".$chat->id.");";
        $chat->updateThis(array('update' => array('operation_admin')));

        $vvcall->updateThis(array('update' => array('vi_status')));

    } else if ($Params['user_parameters_unordered']['action'] == 'request') {

        $payload = json_decode(file_get_contents('php://input'),true);

        $settings = erLhcoreClassVoiceVideo::getSettings();

        if (isset($payload['type']) && $payload['type'] == 'audiovideo' && $settings['video'] == true) {
            $vvcall->voice = 1;
            $vvcall->video = 1;
        } elseif (isset($payload['type']) && ($payload['type'] == 'audio' || $payload['type'] == 'audiovideo')) {
            $vvcall->voice = 1;
            $vvcall->video = 0;
        }

        if ($vvcall->status == erLhcoreClassModelChatVoiceVideo::STATUS_CONFIRMED) {
            // Visitor was already let in during this chat, re-join
            $vvcall->vi_status = erLhcoreClassModelChatVoiceVideo::STATUS_VI_JOINED;
            erLhcoreClassVoiceVideo::trackCallAnswered($chat, $vvcall);
        } else {
            $vvcall->vi_status = erLhcoreClassModelChatVoiceVideo::STATUS_VI_REQUESTED;
            $vvcall->status = erLhcoreClassModelChatVoiceVideo::STATUS_CONFIRM;

            erLhcoreClassVoiceVideo::trackCallStart($chat, $vvcall);

            if ($vvcall->op_status !== erLhcoreClassModelChatVoiceVideo::STATUS_OP_JOINED) {
                // Inform operator that visitor want's a voice call
                $msg = new erLhcoreClassModelmsg();
                $msg->user_id = -1;
                $msg->chat_id = $chat->id;
                $msg->meta_msg = json_encode([
                    'content' => [
                        'button_message' => [
                            'type' => 'voice_requested'
                        ]
                    ]
                ]);
                $msg->msg = '';
                $msg->time = time();
                $msg->saveThis();

                $chat->operation_admin = "lhinst.updateVoteStatus(".$chat->id.");";
                $chat->last_user_msg_time = $msg->time;
                $chat->lsync = time();
                $chat->last_msg_id = $chat->last_msg_id < $msg->id ? $msg->id : $chat->last_msg_id;
                $chat->has_unread_messages = 1;
                $chat->updateThis(array('update' => array('operation_admin','last_user_msg_time','lsync','last_msg_id','has_unread_messages')));
            }
        }

        $vvcall->updateThis(array('update' => array('vi_status', 'voice', 'video', 'status')));
    }

    $state = erLhcoreClassVoiceVideo::getCallState($vvcall, erLhcoreClassVoiceVideo::getVisitorToken($chat, $vvcall));

    // Nobody answered within ring timeout
    if ($Params['user_parameters_unordered']['action'] == '' && erLhcoreClassVoiceVideo::checkRingTimeout($chat, $vvcall)) {
        $state = erLhcoreClassVoiceVideo::getCallState($vvcall, '');
        $state['missed'] = true;
    }

    // Token is issued only if operator has let visitor in
    echo json_encode($state);

} else {
    echo json_encode(array(
        'vi_status' => erLhcoreClassModelChatVoiceVideo::STATUS_VI_PENDING,
        'op_status' => erLhcoreClassModelChatVoiceVideo::STATUS_OP_PENDING,
        'status' => erLhcoreClassModelChatVoiceVideo::STATUS_PENDING,
        'token' => '',
    ));
}

exit;
?>
