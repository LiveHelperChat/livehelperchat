<?php

header('content-type: application/json; charset=utf-8');

// Set new chat owner
$currentUser = erLhcoreClassUser::instance();

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
	echo json_encode(array('error' => true, 'result' => 'Invalid CSRF Token' ));
	exit;
}

$db = ezcDbInstance::get();
$db->beginTransaction();

try {

    $chat = erLhcoreClassModelChat::fetchAndLock($Params['user_parameters']['chat_id']);

    erLhcoreClassChat::lockDepartment($chat->dep_id, $db);

    // Chat can be closed only by owner
    if ($chat->user_id == $currentUser->getUserID() || ($currentUser->hasAccessTo('lhchat','allowcloseremote') && erLhcoreClassChat::hasAccessToWrite($chat)))
    {
        $userData = $currentUser->getUserData(true);

        erLhcoreClassChatHelper::closeChat(array(
            'user' => $userData,
            'chat' => $chat,
        ));
    }

    $db->commit();
    echo json_encode(array('error' => false, 'result' => 'ok' ));
} catch (Exception $e) {
    erLhcoreClassLog::write($e->getTraceAsString());
    echo json_encode(array('error' => true, 'result' => $e->getMessage() ));
    $db->rollback();
}

exit;

?>