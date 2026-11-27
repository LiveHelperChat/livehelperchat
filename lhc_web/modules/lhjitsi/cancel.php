<?php

header('Content-Type: application/json; charset=utf-8');

$csrfToken = $_SERVER['HTTP_X_CSRFTOKEN'] ?? ($_POST['csfr_token'] ?? '');
if ($csrfToken === '' || !$currentUser->validateCSFRToken($csrfToken)) {
    http_response_code(403);
    echo json_encode(['error' => true, 'message' => 'Invalid CSRF token.']);
    exit;
}

if (!is_numeric($Params['user_parameters']['chat_id']) || !is_numeric($Params['user_parameters']['msg_id'])) {
    http_response_code(400);
    echo json_encode(['error' => true, 'message' => 'Invalid parameters.']);
    exit;
}

$db = ezcDbInstance::get();
$db->beginTransaction();

try {
    $chat = erLhcoreClassModelChat::fetchAndLock((int)$Params['user_parameters']['chat_id']);

    if (!($chat instanceof erLhcoreClassModelChat) || !erLhcoreClassChat::hasAccessToRead($chat) || !erLhcoreClassChat::hasAccessToWrite($chat)) {
        throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'Permission denied.'));
    }

    $message = erLhcoreClassModelmsg::fetch((int)$Params['user_parameters']['msg_id']);
    if (!($message instanceof erLhcoreClassModelmsg) || (int)$message->chat_id !== (int)$chat->id) {
        throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'Call message not found.'));
    }

    $metaMessage = $message->meta_msg_array;
    if (!isset($metaMessage['content']['jitsi']) || !is_array($metaMessage['content']['jitsi'])) {
        throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'Message is not a Jitsi call.'));
    }

    $requestedAction = isset($Params['user_parameters_unordered']['action']) && $Params['user_parameters_unordered']['action'] === 'end'
        ? 'end'
        : 'cancel';

    $transition = erLhcoreClassJitsi::applyStateTransition($metaMessage['content']['jitsi'], $requestedAction, 'operator');

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
