<?php

header('Content-Type: application/json');

if (is_numeric($Params['user_parameters']['id']))
{
    $chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['id']);
    if ( erLhcoreClassChat::hasAccessToRead($chat) )
    {
        $action = (string)$Params['user_parameters_unordered']['action'];

        // All state changing actions require a valid CSRF token. Header for XHR requests, POST field for sendBeacon on page close.
        $csrfToken = isset($_SERVER['HTTP_X_CSRFTOKEN']) ? $_SERVER['HTTP_X_CSRFTOKEN'] : (isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null);
        if ($action != '' && ($csrfToken === null || !$currentUser->validateCSFRToken($csrfToken))) {
            http_response_code(403);
            echo json_encode(array('error' => true, 'result' => 'Invalid CSRF token'));
            exit;
        }

        if ($action != '' && !erLhcoreClassVoiceVideo::isEnabled()) {
            http_response_code(400);
            echo json_encode(array('error' => true, 'result' => 'Calls are disabled'));
            exit;
        }

        $vvcall = erLhcoreClassModelChatVoiceVideo::getInstance($chat->id);

        $userData = $currentUser->getUserData();

        // Supervisor silent monitoring. Does not change call state.
        if ($action == 'listen') {

            if (!$currentUser->hasAccessTo('lhvoicevideo', 'supervise')) {
                http_response_code(403);
                echo json_encode(array('error' => true, 'result' => 'No permission'));
                exit;
            }

            $token = erLhcoreClassVoiceVideo::getSupervisorToken($chat, $vvcall, $currentUser->getUserID(), $userData->name_support);

            if ($token != '') {
                erLhcoreClassLog::write('Supervisor ' . $userData->name_official . ' (' . $currentUser->getUserID() . ') started listening to a call in chat ' . $chat->id,
                    ezcLog::SUCCESS_AUDIT,
                    array(
                        'source' => 'lhc',
                        'category' => 'voice_call_supervise',
                        'line' => __LINE__,
                        'file' => __FILE__,
                        'object_id' => $chat->id
                    )
                );
            }

            echo json_encode(erLhcoreClassVoiceVideo::getCallState($vvcall, $token));
            exit;
        }

        if ($action == 'end') {
            $vvcall->op_status = erLhcoreClassModelChatVoiceVideo::STATUS_OP_PENDING;
            $vvcall->vi_status = erLhcoreClassModelChatVoiceVideo::STATUS_VI_PENDING;
            $vvcall->status = erLhcoreClassModelChatVoiceVideo::STATUS_PENDING;
            $vvcall->updateThis(array('update' => array('op_status','status','vi_status')));
            erLhcoreClassVoiceVideo::trackCallEnd($chat, 'operator_ended');
        } else if ($action == 'leave') {
            $vvcall->op_status = erLhcoreClassModelChatVoiceVideo::STATUS_OP_PENDING;
            $vvcall->updateThis(array('update' => array('op_status')));
            erLhcoreClassVoiceVideo::trackCallEnd($chat, 'operator_left');
        } else if ($action == 'join') {
            $vvcall->op_status = erLhcoreClassModelChatVoiceVideo::STATUS_OP_JOINED;
            $vvcall->user_id = $currentUser->getUserID();

            $payload = json_decode(file_get_contents('php://input'),true);
            if (isset($payload['type']) && in_array($payload['type'], array('audio', 'audiovideo'))) {
                $vvcall->voice = 1;
                if ($payload['type'] == 'audiovideo') {
                    $vvcall->video = 1;
                }
            }

            $vvcall->updateThis(array('update' => array('op_status', 'user_id', 'voice', 'video')));

            erLhcoreClassVoiceVideo::trackCallStart($chat, $vvcall, $currentUser->getUserID());

            // Inform visitor that operator wants to start a voice chat
            if ($vvcall->vi_status == erLhcoreClassModelChatVoiceVideo::STATUS_VI_PENDING) {
                $msg = new erLhcoreClassModelmsg();
                $msg->user_id = $currentUser->getUserID();
                $msg->name_support = $userData->name_support;
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

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_msg_admin_saved',array('msg' => & $msg, 'chat' => & $chat));

                $msg->saveThis();

                $chat->last_msg_id = $msg->id;
                $chat->last_op_msg_time = $msg->time;
                $chat->has_unread_op_messages = 1;
                $chat->unread_op_messages_informed = 0;

                $chat->updateThis(array('update' => array('last_msg_id', 'last_op_msg_time', 'has_unread_op_messages', 'unread_op_messages_informed')));
            }

        } else if ($action == 'letvisitorin') {
            $vvcall->vi_status = erLhcoreClassModelChatVoiceVideo::STATUS_VI_JOINED;
            $vvcall->status = erLhcoreClassModelChatVoiceVideo::STATUS_CONFIRMED;
            $vvcall->updateThis(array('update' => array('vi_status','status')));
            erLhcoreClassVoiceVideo::trackCallAnswered($chat, $vvcall, $currentUser->getUserID());
        }

        if ($action == '') {
            erLhcoreClassVoiceVideo::checkRingTimeout($chat, $vvcall);
        }

        if ($action != '' && $action != 'token') {
            $chat->operation_admin = "lhinst.updateVoteStatus(".$chat->id.");";
            $chat->updateThis(array('update' => array('operation_admin')));
        }

        echo json_encode(erLhcoreClassVoiceVideo::getCallState($vvcall, erLhcoreClassVoiceVideo::getOperatorToken($chat, $vvcall, $currentUser->getUserID(), $userData->name_support)));
    }
}

exit;

?>
