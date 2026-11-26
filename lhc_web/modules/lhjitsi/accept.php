<?php

erLhcoreClassRestAPIHandler::setHeaders();
erTranslationClassLhTranslation::$htmlEscape = false;

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

$db = ezcDbInstance::get();
$db->beginTransaction();

try {
    $config = erLhcoreClassJitsi::getConfig();
    if (!erLhcoreClassJitsi::isEnabled($config)) {
        throw new Exception('Jitsi calling is not enabled.');
    }

    $chat = erLhcoreClassModelChat::fetchAndLock((int)$Params['user_parameters']['chat_id']);
    $hash = (string)($Params['user_parameters']['hash'] ?? '');

    if (!erLhcoreClassJitsi::isVisitorChatValid($chat, $hash)) {
        throw new Exception('Invalid chat or hash.');
    }

    $message = erLhcoreClassModelmsg::fetch((int)$Params['user_parameters']['msg_id']);
    if (!($message instanceof erLhcoreClassModelmsg) || (int)$message->chat_id !== (int)$chat->id) {
        throw new Exception('Call message not found.');
    }

    $metaMessage = $message->meta_msg_array;
    if (!isset($metaMessage['content']['jitsi']) || !is_array($metaMessage['content']['jitsi'])) {
        throw new Exception('Message is not a Jitsi call.');
    }

    $transition = erLhcoreClassJitsi::applyStateTransition($metaMessage['content']['jitsi'], 'accept', 'visitor');
    if ($transition['error'] !== null) {
        throw new Exception($transition['error']);
    }

    if ($transition['changed']) {
        erLhcoreClassJitsi::updateCallMessage($chat, $message, $transition['meta'], false, true);
    }

    $db->commit();

    if ($transition['changed']) {
        erLhcoreClassJitsi::dispatchCallUpdated($chat, $message);
    }

    echo json_encode([
        'error' => false,
        'status' => $transition['meta']['status'],
        'op_joined' => !empty($transition['meta']['op_joined']),
        'join_url' => erLhcoreClassSystem::getHost() . erLhcoreClassDesign::baseurl('jitsi/join') . '/' . $chat->id . '/' . $chat->hash . '/' . $message->id,
    ]);
} catch (Exception $e) {
    $db->rollback();
    http_response_code(400);
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}

exit;
