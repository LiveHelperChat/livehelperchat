<?php

/**
 * LiveKit webhook receiver. Keeps call state correct when participant closes browser, loses connection etc.
 * Configure in livekit.yaml
 *
 * webhook:
 *   api_key: <API key>
 *   urls:
 *     - https://chat.example.com/index.php/voicevideo/webhook
 */

header('Content-Type: application/json');

$body = file_get_contents('php://input');
$authorization = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) ? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] : '');

if (!erLhcoreClassVoiceVideo::verifyLiveKitWebhook($body, $authorization)) {
    http_response_code(401);
    echo json_encode(array('error' => true));
    exit;
}

$event = json_decode($body, true);

if (!is_array($event) || !isset($event['event']) || !isset($event['room']['name'])) {
    echo json_encode(array('error' => false, 'result' => 'ignored'));
    exit;
}

$chat = erLhcoreClassVoiceVideo::getChatByRoomName($event['room']['name']);

$vvcall = $chat instanceof erLhcoreClassModelChat ? erLhcoreClassModelChatVoiceVideo::findOne(array('filter' => array('chat_id' => $chat->id))) : null;

if (!($vvcall instanceof erLhcoreClassModelChatVoiceVideo)) {
    echo json_encode(array('error' => false, 'result' => 'ignored'));
    exit;
}

$result = 'ignored';

if ($event['event'] == 'participant_left' && isset($event['participant']['identity'])) {

    // Same participant joined from another tab/device, call continues there
    $reason = isset($event['participant']['disconnectReason']) ? $event['participant']['disconnectReason'] : '';

    if ($reason !== 'DUPLICATE_IDENTITY') {
        $identity = $event['participant']['identity'];

        if ($identity === erLhcoreClassVoiceVideo::getVisitorIdentity($chat) && $vvcall->vi_status == erLhcoreClassModelChatVoiceVideo::STATUS_VI_JOINED) {
            $vvcall->vi_status = erLhcoreClassModelChatVoiceVideo::STATUS_VI_PENDING;
            $vvcall->updateThis(array('update' => array('vi_status')));
            erLhcoreClassVoiceVideo::trackCallEnd($chat, 'visitor_disconnected');
            $result = 'visitor_left';
        } elseif ($vvcall->user_id > 0 && $identity === erLhcoreClassVoiceVideo::getOperatorIdentity($vvcall->user_id) && $vvcall->op_status == erLhcoreClassModelChatVoiceVideo::STATUS_OP_JOINED) {
            $vvcall->op_status = erLhcoreClassModelChatVoiceVideo::STATUS_OP_PENDING;
            $vvcall->updateThis(array('update' => array('op_status')));
            erLhcoreClassVoiceVideo::trackCallEnd($chat, 'operator_disconnected');
            $result = 'operator_left';
        }
    }

} elseif ($event['event'] == 'room_finished') {

    if ($vvcall->op_status != erLhcoreClassModelChatVoiceVideo::STATUS_OP_PENDING || $vvcall->vi_status == erLhcoreClassModelChatVoiceVideo::STATUS_VI_JOINED) {
        $vvcall->op_status = erLhcoreClassModelChatVoiceVideo::STATUS_OP_PENDING;
        $vvcall->vi_status = erLhcoreClassModelChatVoiceVideo::STATUS_VI_PENDING;
        $vvcall->status = erLhcoreClassModelChatVoiceVideo::STATUS_PENDING;
        $vvcall->updateThis(array('update' => array('op_status', 'vi_status', 'status')));
        $result = 'room_finished';
    }

    erLhcoreClassVoiceVideo::trackCallEnd($chat, 'room_finished');
}

if ($result != 'ignored') {
    $chat->operation_admin = "lhinst.updateVoteStatus(" . $chat->id . ");";
    $chat->updateThis(array('update' => array('operation_admin')));
}

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('voicevideo.webhook', array('chat' => & $chat, 'call' => & $vvcall, 'event' => $event));

echo json_encode(array('error' => false, 'result' => $result));
exit;

?>
