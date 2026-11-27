<?php

header('Content-Type: application/json; charset=utf-8');

$csrfToken = $_SERVER['HTTP_X_CSRFTOKEN'] ?? ($_POST['csfr_token'] ?? '');
if ($csrfToken === '' || !$currentUser->validateCSFRToken($csrfToken)) {
    http_response_code(403);
    echo json_encode(['error' => true, 'message' => 'Invalid CSRF token.']);
    exit;
}

$config = erLhcoreClassJitsi::getConfig();
if (!erLhcoreClassJitsi::isEnabled($config)) {
    http_response_code(400);
    echo json_encode(['error' => true, 'message' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'Jitsi calling is not enabled.')]);
    exit;
}

if (!is_numeric($Params['user_parameters']['chat_id'])) {
    http_response_code(400);
    echo json_encode(['error' => true, 'message' => 'Invalid chat ID.']);
    exit;
}

$db = ezcDbInstance::get();
$db->beginTransaction();

try {
    $chat = erLhcoreClassModelChat::fetchAndLock((int)$Params['user_parameters']['chat_id']);

    if (!($chat instanceof erLhcoreClassModelChat) || !erLhcoreClassChat::hasAccessToRead($chat) || !erLhcoreClassChat::hasAccessToWrite($chat)) {
        throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'You do not have permission to initiate a call in this chat.'));
    }

    if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
        throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'Cannot initiate a call in a closed chat.'));
    }

    $requestedMode = isset($_POST['mode']) ? (string)$_POST['mode'] : (string)$config['default_mode'];
    $mode = erLhcoreClassJitsi::normalizeMode($requestedMode, $config);
    $room = erLhcoreClassJitsi::generateRoomName((int)$chat->id, (string)$config['room_prefix']);

    $userData = $currentUser->getUserData();

    $msg = new erLhcoreClassModelmsg();
    $msg->user_id = $currentUser->getUserID();
    $msg->name_support = $userData->name_support;
    $msg->chat_id = $chat->id;
    $msg->msg = '';
    $msg->time = time();

    \LiveHelperChat\Models\Departments\UserDepAlias::getAlias([
        'scope' => 'msg',
        'msg' => & $msg,
        'chat' => & $chat,
        'user_id' => $userData->id,
    ]);

    $jitsiMeta = [
        'room' => $room,
        'mode' => $mode,
        'status' => erLhcoreClassJitsi::STATUS_INVITED,
        'op_joined' => false,
        'vi_joined' => false,
        'invited_at' => $msg->time,
        'accepted_at' => 0,
        'started_at' => 0,
        'ended_at' => 0,
        'ended_by' => '',
        'operator_name' => (string)$msg->name_support,
    ];

    $msg->meta_msg_array = [
        'content' => [
            'jitsi' => $jitsiMeta,
        ],
    ];
    $msg->meta_msg = json_encode($msg->meta_msg_array);

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_msg_admin_saved', [
        'msg' => & $msg,
        'chat' => & $chat,
        'user_id' => $userData->id,
    ]);

    $msg->saveThis();

    $chat->last_msg_id = max((int)$chat->last_msg_id, (int)$msg->id);
    $chat->last_op_msg_time = $msg->time;
    $chat->has_unread_op_messages = 1;
    $chat->unread_op_messages_informed = 0;
    $chat->updateThis(['update' => ['last_msg_id', 'last_op_msg_time', 'has_unread_op_messages', 'unread_op_messages_informed']]);

    $db->commit();

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.web_add_msg_admin', [
        'msg' => & $msg,
        'chat' => & $chat,
        'ou' => $userData,
    ]);

    echo json_encode([
        'error' => false,
        'msg_id' => (int)$msg->id,
        'room' => $room,
        'mode' => $mode,
        'status' => erLhcoreClassJitsi::STATUS_INVITED,
        'auto_join' => !empty($config['auto_join_operator']),
        'join_url' => erLhcoreClassDesign::baseurl('jitsi/joinoperator') . '/' . $chat->id . '/' . $msg->id,
    ]);
} catch (Exception $e) {
    $db->rollback();
    http_response_code(400);
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}

exit;
