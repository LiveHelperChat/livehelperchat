<?php

header('Content-Type: application/json');
$db = ezcDbInstance::get();

try {
    $db->beginTransaction();

    $currentUser = erLhcoreClassUser::instance();
    $userData = $currentUser->getUserData(true);

    if ($Params['user_parameters']['status'] == 'false') {
        $userData->always_on = 0;
    } else {
        $userData->always_on = 1;
    }

    erLhcoreClassUser::getSession()->update($userData);

    erLhcoreClassUserDep::setHideOnlineStatus($userData);

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.operator_always_online_status_changed',array('user' => & $userData, 'reason' => 'user_action'));

    echo json_encode(array('error' => false));

    $db->commit();

} catch (Exception $e) {
    echo json_encode(array('error' => true, 'msg' => $e->getMessage()));
    $db->rollback();
}

exit;
?>