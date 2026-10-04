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

        // Supervisor steps in to the conversation (microphone only). Participants are informed.
        if ($action == 'barge') {

            if (!$currentUser->hasAccessTo('lhvoicevideo', 'supervise')) {
                http_response_code(403);
                echo json_encode(array('error' => true, 'result' => 'No permission'));
                exit;
            }

            $token = erLhcoreClassVoiceVideo::getSupervisorSpeakToken($chat, $vvcall, $currentUser->getUserID(), $userData->name_support);

            if ($token != '') {
                erLhcoreClassVoiceVideo::addSystemMessage($chat, $userData->name_support . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video', 'joined the call'));

                erLhcoreClassLog::write('Supervisor ' . $userData->name_official . ' (' . $currentUser->getUserID() . ') joined the conversation in a call in chat ' . $chat->id,
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

        // Recording start/stop
        if ($action == 'record_start' || $action == 'record_stop') {

            if (!$currentUser->hasAccessTo('lhvoicevideo', 'record')) {
                http_response_code(403);
                echo json_encode(array('error' => true, 'result' => 'No permission'));
                exit;
            }

            try {
                if ($action == 'record_start') {
                    erLhcoreClassVoiceVideoRecording::start($chat, $currentUser->getUserID());
                    erLhcoreClassVoiceVideo::addSystemMessage($chat, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video', 'Call recording started'));
                } else {
                    $recording = erLhcoreClassVoiceVideoRecording::getActiveRecording($chat->id);
                    if ($recording instanceof erLhcoreClassModelChatVoiceVideoRecording) {
                        erLhcoreClassVoiceVideoRecording::stop($recording);
                        erLhcoreClassVoiceVideo::addSystemMessage($chat, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video', 'Call recording stopped'));
                    }
                }
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode(array('error' => true, 'result' => $e->getMessage()));
                exit;
            }

            $chat->operation_admin = "lhinst.updateVoteStatus(".$chat->id.");";
            $chat->updateThis(array('update' => array('operation_admin')));

            echo json_encode(erLhcoreClassVoiceVideo::getCallState($vvcall, erLhcoreClassVoiceVideo::getOperatorToken($chat, $vvcall, $currentUser->getUserID(), $userData->name_support)));
            exit;
        }

        // Online operators call can be transferred to
        if ($action == 'operators') {
            $operators = array();

            if ($currentUser->hasAccessTo('lhchat', 'allowtransfer')) {
                foreach (erLhcoreClassChat::getOnlineUsers(array($currentUser->getUserID())) as $operator) {
                    $user = erLhcoreClassModelUser::fetch($operator['id']);
                    if ($user instanceof erLhcoreClassModelUser && erLhcoreClassRole::hasAccessTo($user->id, 'lhvoicevideo', 'use')) {
                        $operators[] = array('id' => (int)$user->id, 'name' => $user->name_official);
                    }
                }
            }

            echo json_encode(array('operators' => $operators));
            exit;
        }

        // Transfer call (and chat) to another operator. Call continues until new operator joins.
        if ($action == 'transfer') {

            $payload = json_decode(file_get_contents('php://input'), true);
            $userTo = isset($payload['user_id']) && is_numeric($payload['user_id']) ? erLhcoreClassModelUser::fetch((int)$payload['user_id']) : false;

            if (!$currentUser->hasAccessTo('lhchat', 'allowtransfer') || !($userTo instanceof erLhcoreClassModelUser) || $userTo->id == $currentUser->getUserID() || !erLhcoreClassRole::hasAccessTo($userTo->id, 'lhvoicevideo', 'use')) {
                http_response_code(400);
                echo json_encode(array('error' => true, 'result' => 'Call can not be transferred to selected operator'));
                exit;
            }

            // Replace pending transfer of this chat
            $transferLegacy = erLhcoreClassTransfer::getTransferByChat($chat->id);
            if (is_array($transferLegacy)) {
                erLhcoreClassTransfer::getSession()->delete(erLhcoreClassTransfer::getSession()->load('erLhcoreClassModelTransfer', $transferLegacy['id']));
            }

            $transfer = new erLhcoreClassModelTransfer();
            $transfer->chat_id = $chat->id;
            $transfer->ctime = time();
            $transfer->transfer_to_user_id = $userTo->id;
            $transfer->transfer_user_id = $currentUser->getUserID();
            $transfer->from_dep_id = $chat->dep_id;
            erLhcoreClassTransfer::getSession()->save($transfer);

            $chat->transfer_uid = $currentUser->getUserID();
            $chat->updateThis(array('update' => array('transfer_uid')));

            erLhcoreClassVoiceVideo::addSystemMessage($chat, $userData->name_support . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video', 'is transferring the call to') . ' ' . $userTo->name_support);

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chat_transfered', array('chat' => & $chat, 'transfer' => $transfer));

            echo json_encode(array_merge(erLhcoreClassVoiceVideo::getCallState($vvcall, erLhcoreClassVoiceVideo::getOperatorToken($chat, $vvcall, $currentUser->getUserID(), $userData->name_support)), array('transfer_to' => $userTo->name_support)));
            exit;
        }

        // Operator who is not the call owner anymore (call was transferred) leaves without changing call state
        $isCallOwner = $vvcall->op_status != erLhcoreClassModelChatVoiceVideo::STATUS_OP_JOINED || $vvcall->user_id == $currentUser->getUserID();

        if (($action == 'end' || $action == 'leave') && $isCallOwner === false) {
            // Nothing to change
        } elseif ($action == 'end') {
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

            // Another operator takes over the call (transfer). Previous operator's call record ends, new one starts.
            $isHandover = $vvcall->op_status == erLhcoreClassModelChatVoiceVideo::STATUS_OP_JOINED && $vvcall->user_id > 0 && $vvcall->user_id != $currentUser->getUserID();

            if ($isHandover === true) {
                erLhcoreClassVoiceVideo::trackCallEnd($chat, 'transferred', false, false);
                erLhcoreClassVoiceVideo::addSystemMessage($chat, $userData->name_support . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video', 'took over the call'));
            }

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

            if ($isHandover === true && $vvcall->vi_status == erLhcoreClassModelChatVoiceVideo::STATUS_VI_JOINED) {
                erLhcoreClassVoiceVideo::trackCallAnswered($chat, $vvcall, $currentUser->getUserID());
            } else {
                erLhcoreClassVoiceVideo::trackCallStart($chat, $vvcall, $currentUser->getUserID());
            }

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
