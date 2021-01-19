<?php

header('Content-Type: application/json');

if (is_numeric($Params['user_parameters']['id']))
{
    $chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['id']);
    if ( erLhcoreClassChat::hasAccessToRead($chat) )
    {
        $vvcall = erLhcoreClassModelChatVoiceVideo::getInstance($chat->id);

        $updateStatus = false;

        if ($Params['user_parameters_unordered']['action'] == 'end') {
            $vvcall->op_status = erLhcoreClassModelChatVoiceVideo::STATUS_OP_PENDING;
            $vvcall->vi_status = erLhcoreClassModelChatVoiceVideo::STATUS_VI_PENDING;
            $vvcall->status = erLhcoreClassModelChatVoiceVideo::STATUS_PENDING;
            $vvcall->updateThis(array('update' => array('op_status','status','vi_status')));
            $updateStatus = true;
        } else if ($Params['user_parameters_unordered']['action'] == 'leave') {
            $vvcall->op_status = erLhcoreClassModelChatVoiceVideo::STATUS_OP_PENDING;
            $vvcall->updateThis(array('update' => array('op_status')));
            $updateStatus = true;
        } else if ($Params['user_parameters_unordered']['action'] == 'token') {
            $voiceData = (array)erLhcoreClassModelChatConfig::fetch('vvsh_configuration')->data;

            // Update token
            include 'lib/core/lhvoicevideo/RtcTokenBuilder.php';
            $token = AgoraIO\RtcTokenBuilder::buildTokenWithUserAccount($voiceData['agora_app_id'], $voiceData['agora_app_token'], $chat->id . '_' . $chat->hash, null, AgoraIO\RtcTokenBuilder::RoleAttendee, (new DateTime("now", new DateTimeZone('UTC')))->getTimestamp()+(300));
            $vvcall->token = $token;
            $vvcall->updateThis(array('update' => array('token')));

        } else if ($Params['user_parameters_unordered']['action'] == 'join') {
            $vvcall->op_status = erLhcoreClassModelChatVoiceVideo::STATUS_OP_JOINED;

            $voiceData = (array)erLhcoreClassModelChatConfig::fetch('vvsh_configuration')->data;

            // Update token
            include 'lib/core/lhvoicevideo/RtcTokenBuilder.php';
            $token = AgoraIO\RtcTokenBuilder::buildTokenWithUserAccount($voiceData['agora_app_id'], $voiceData['agora_app_token'], $chat->id . '_' . $chat->hash, null, AgoraIO\RtcTokenBuilder::RoleAttendee, (new DateTime("now", new DateTimeZone('UTC')))->getTimestamp()+(300));
            $vvcall->token = $token;

            $vvcall->updateThis(array('update' => array('op_status','token')));
            $updateStatus = true;

            // Inform operator that visitor want's a voice call
            $userData = $currentUser->getUserData();

            // Inform visitor that he want's to start a voice chat
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

        } else if ($Params['user_parameters_unordered']['action'] == 'letvisitorin') {
            $vvcall->vi_status = erLhcoreClassModelChatVoiceVideo::STATUS_VI_JOINED;
            $vvcall->status = erLhcoreClassModelChatVoiceVideo::STATUS_CONFIRMED;
            $vvcall->updateThis(array('update' => array('vi_status','status')));
            $updateStatus = true;
        }

        $chat->operation_admin = "lhinst.updateVoteStatus(".$chat->id.");";
        $chat->updateThis(array('update' => array('operation_admin')));

        echo json_encode($vvcall->getState());
    }
}

exit;


?>