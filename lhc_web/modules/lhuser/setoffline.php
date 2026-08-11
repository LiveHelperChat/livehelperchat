<?php

header('Content-Type: application/json');
$db = ezcDbInstance::get();

try {
    $db->beginTransaction();

    $currentUser = erLhcoreClassUser::instance();

    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        throw new Exception('Invalid CSFR Token');
    }

    $userData = $currentUser->getUserData(true);
    
    // Lock the user record to prevent race conditions when updating hide_online
    $userData->syncAndLock();

    $offlineReasonId = (isset($Params['user_parameters_unordered']['reason']) && is_numeric($Params['user_parameters_unordered']['reason'])) ? (int)$Params['user_parameters_unordered']['reason'] : 0;
    $previousOfflineReasonId = (int)$userData->offline_reason_id;

    // If already offline and only changing the reason, skip the full status toggle
    if ($userData->hide_online == 1 && $Params['user_parameters']['status'] == 'true' && $offlineReasonId > 0 && $offlineReasonId != $previousOfflineReasonId) {
        $userData->offline_reason_id = $offlineReasonId;
        erLhcoreClassUser::getSession()->update($userData);

        $stmt = $db->prepare('UPDATE `lh_users_online_session` SET `offline_reason_id` = :offline_reason_id WHERE `user_id` = :user_id AND `lactivity` > :timeout ORDER BY `id` DESC LIMIT 1');
        $stmt->bindValue(':offline_reason_id', $offlineReasonId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userData->id, PDO::PARAM_INT);
        $stmt->bindValue(':timeout', time() - 21600, PDO::PARAM_INT);
        $stmt->execute();

        $db->commit();
        echo json_encode(array('error' => false));
        exit;
    }

    if ($Params['user_parameters']['status'] == 'false') {
        $userData->hide_online = 0;
        $userData->offline_reason_id = 0;
    } else {
        $userData->hide_online = 1;
        $userData->offline_reason_id = $offlineReasonId;
    }

    erLhcoreClassUser::getSession()->update($userData);

    $currentUser->updateLastVisit(time(), $userData->hide_online == 1 ? 2 : 1, 0, $offlineReasonId); // Went offline OR went online

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