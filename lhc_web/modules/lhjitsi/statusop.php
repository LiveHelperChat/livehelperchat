<?php

header('Content-Type: application/json; charset=utf-8');

if (!is_numeric($Params['user_parameters']['chat_id']) || !is_numeric($Params['user_parameters']['msg_id'])) {
    http_response_code(400);
    echo json_encode(['error' => true, 'message' => 'Invalid parameters.']);
    exit;
}

$action = (string)($Params['user_parameters_unordered']['action'] ?? '');
if (!in_array($action, ['poll', 'joined', 'left', 'end'], true)) {
    http_response_code(400);
    echo json_encode(['error' => true, 'message' => 'Invalid action.']);
    exit;
}

// Read-only poll for admin chat card auto-refresh
if ($action === 'poll') {
    try {
        $chat = erLhcoreClassModelChat::fetch((int)$Params['user_parameters']['chat_id']);
        if (!($chat instanceof erLhcoreClassModelChat) || !erLhcoreClassChat::hasAccessToRead($chat)) {
            throw new Exception('Permission denied.');
        }

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

        echo json_encode([
            'error' => false,
            'status' => $status,
            'op_joined' => !empty($jitsiMeta['op_joined']),
            'vi_joined' => !empty($jitsiMeta['vi_joined']),
            'state_key' => erLhcoreClassJitsi::getStateKey($jitsiMeta),
            'terminal' => in_array($status, [
                erLhcoreClassJitsi::STATUS_DENIED,
                erLhcoreClassJitsi::STATUS_CANCELED,
                erLhcoreClassJitsi::STATUS_ENDED,
            ], true),
        ]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    }
    exit;
}

$csrfToken = $_SERVER['HTTP_X_CSRFTOKEN'] ?? ($_POST['csfr_token'] ?? '');
if ($csrfToken === '' || !$currentUser->validateCSFRToken($csrfToken)) {
    http_response_code(403);
    echo json_encode(['error' => true, 'message' => 'Invalid CSRF token.']);
    exit;
}

$db = ezcDbInstance::get();
$db->beginTransaction();

try {
    $chat = erLhcoreClassModelChat::fetchAndLock((int)$Params['user_parameters']['chat_id']);
    if (!($chat instanceof erLhcoreClassModelChat) || !erLhcoreClassChat::hasAccessToRead($chat)) {
        throw new Exception('Permission denied.');
    }

    if ($action === 'joined' && (int)$chat->status === (int)erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
        throw new Exception('Cannot join a call in a closed chat.');
    }

    $message = erLhcoreClassModelmsg::fetch((int)$Params['user_parameters']['msg_id']);
    if (!($message instanceof erLhcoreClassModelmsg) || (int)$message->chat_id !== (int)$chat->id) {
        throw new Exception('Call message not found.');
    }

    $metaMessage = $message->meta_msg_array;
    if (!isset($metaMessage['content']['jitsi']) || !is_array($metaMessage['content']['jitsi'])) {
        throw new Exception('Message is not a Jitsi call.');
    }

    $transition = erLhcoreClassJitsi::applyStateTransition($metaMessage['content']['jitsi'], $action, 'operator');

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
        'state_key' => erLhcoreClassJitsi::getStateKey($transition['meta']),
    ]);
} catch (Exception $e) {
    $db->rollback();
    http_response_code(400);
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}

exit;
