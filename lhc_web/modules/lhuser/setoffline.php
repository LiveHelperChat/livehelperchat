<?php

header('Content-Type: application/json');
$db = ezcDbInstance::get();

try {
    $db->beginTransaction();

    $currentUser = erLhcoreClassUser::instance();
    $userData = $currentUser->getUserData(true);

    if ($Params['user_parameters']['status'] == 'false') {
        $userData->hide_online = 0;
    } else {
        $userData->hide_online = 1;
    }

    erLhcoreClassUser::getSession()->update($userData);

    erLhcoreClassUserDep::setHideOnlineStatus($userData);

    erLhcoreClassChat::updateActiveChats($userData->id);

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.operator_status_changed',array('user' => & $userData, 'reason' => 'user_action'));

    echo json_encode(array('error' => false));

    $db->commit();

} catch (Exception $e) {
    echo json_encode(array('error' => true, 'msg' => $e->getMessage()));
    $db->rollback();
}

exit;
?>