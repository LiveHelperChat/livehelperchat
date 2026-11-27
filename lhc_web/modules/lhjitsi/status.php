<?php

erLhcoreClassRestAPIHandler::setHeaders();
erTranslationClassLhTranslation::$htmlEscape = false;

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

$action = (string)($Params['user_parameters_unordered']['action'] ?? '');
if (!in_array($action, ['poll', 'joined', 'left', 'end'], true)) {
    http_response_code(400);
    echo json_encode(['error' => true, 'message' => 'Invalid action.']);
    exit;
}

// Read-only poll endpoint for visitor waiting room (checks whether operator has joined first)
if ($action === 'poll') {
    try {
        $config = erLhcoreClassJitsi::getConfig();
        $chat = erLhcoreClassModelChat::fetch((int)$Params['user_parameters']['chat_id']);
        $hash = (string)($Params['user_parameters']['hash'] ?? '');

        if (!erLhcoreClassJitsi::isVisitorChatValid($chat, $hash, true)) {
            throw new Exception('Invalid chat or hash.');
        }

        $isChatClosed = !erLhcoreClassJitsi::isVisitorChatValid($chat, $hash, false);

        $message = erLhcoreClassModelmsg::fetch((int)$Params['user_parameters']['msg_id']);
        if (!($message instanceof erLhcoreClassModelmsg) || (int)$message->chat_id !== (int)$chat->id) {
            throw new Exception('Call message not found.');
        }

        $metaMessage = $message->meta_msg_array;
        if (!isset($metaMessage['content']['jitsi']) || !is_array($metaMessage['content']['jitsi'])) {
            throw new Exception('Message is not a Jitsi call.');
        }

        $jitsiMeta = $metaMessage['content']['jitsi'];
        $status = (string)($jitsiMeta['status'] ?? '');
        $isTerminal = in_array($status, [
            erLhcoreClassJitsi::STATUS_DENIED,
            erLhcoreClassJitsi::STATUS_CANCELED,
            erLhcoreClassJitsi::STATUS_ENDED,
        ], true);

        $stateKey = erLhcoreClassJitsi::getStateKey($jitsiMeta);
        $opJoined = !empty($jitsiMeta['op_joined']);
        $viJoined = !empty($jitsiMeta['vi_joined']);

        if ($isTerminal || $isChatClosed || !erLhcoreClassJitsi::isEnabled($config)) {
            echo json_encode([
                'error' => false,
                'ready' => false,
                'ended' => true,
                'status' => $status,
                'op_joined' => $opJoined,
                'vi_joined' => $viJoined,
                'state_key' => $stateKey,
            ]);
            exit;
        }

        if (!erLhcoreClassJitsi::canVisitorJoinRoom($jitsiMeta)) {
            echo json_encode([
                'error' => false,
                'ready' => false,
                'ended' => false,
                'status' => $status,
                'op_joined' => $opJoined,
                'vi_joined' => $viJoined,
                'state_key' => $stateKey,
            ]);
            exit;
        }

        $room = (string)$jitsiMeta['room'];
        $mode = erLhcoreClassJitsi::normalizeMode((string)($jitsiMeta['mode'] ?? ''), $config);
        $clientConfig = erLhcoreClassJitsi::buildClientConfig($config, $mode, false);
        $visitorName = trim((string)$chat->nick) !== ''
            ? (string)$chat->nick
            : erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Visitor');

        $visitorEmail = erLhcoreClassJitsi::shouldHideProfilePic($config, false) ? '' : (string)$chat->email;
        $jwt = erLhcoreClassJitsi::generateJwt($config, $room, [
            'id' => 'vi_' . $chat->id,
            'name' => $visitorName,
            'email' => $visitorEmail,
        ], false);

        echo json_encode([
            'error' => false,
            'ready' => true,
            'ended' => false,
            'status' => $status,
            'op_joined' => $opJoined,
            'vi_joined' => $viJoined,
            'state_key' => $stateKey,
            'jitsi' => [
                'domain' => erLhcoreClassJitsi::getEffectiveDomain($config),
                'externalApiUrl' => erLhcoreClassJitsi::getExternalApiUrl($config),
                'directRoomUrl' => erLhcoreClassJitsi::getDirectRoomUrl($room, $config),
                'roomName' => erLhcoreClassJitsi::getFullRoomName($room, $config),
                'jwt' => $jwt,
                'displayName' => $visitorName,
                'email' => $visitorEmail,
                'configOverwrite' => $clientConfig['configOverwrite'],
                'interfaceConfigOverwrite' => $clientConfig['interfaceConfigOverwrite'],
            ],
        ], JSON_UNESCAPED_SLASHES);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    }
    exit;
}

$db = ezcDbInstance::get();
$db->beginTransaction();

try {
    $chat = erLhcoreClassModelChat::fetchAndLock((int)$Params['user_parameters']['chat_id']);
    $hash = (string)($Params['user_parameters']['hash'] ?? '');

    // Allow leave/end notifications even if chat was closed while call was running
    $allowClosed = in_array($action, ['left', 'end'], true);
    if (!erLhcoreClassJitsi::isVisitorChatValid($chat, $hash, $allowClosed)) {
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

    $transition = erLhcoreClassJitsi::applyStateTransition($metaMessage['content']['jitsi'], $action, 'visitor');

    if ($transition['error'] !== null) {
        throw new Exception($transition['error']);
    }

    if ($transition['changed']) {
        erLhcoreClassJitsi::updateCallMessage($chat, $message, $transition['meta'], true, true);
    }

    $db->commit();

    if ($transition['changed']) {
        erLhcoreClassJitsi::dispatchCallUpdated($chat, $message);
    }

    echo json_encode([
        'error' => false,
        'status' => $transition['meta']['status'],
    ]);
} catch (Exception $e) {
    $db->rollback();
    http_response_code(400);
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}

exit;
