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

    $wasOnline = ((int)$userData->hide_online === 0);

    $offlineReasonId = (isset($Params['user_parameters_unordered']['reason']) && is_numeric($Params['user_parameters_unordered']['reason'])) ? (int)$Params['user_parameters_unordered']['reason'] : 0;
    $previousOfflineReasonId = (int)$userData->offline_reason_id;

    $reason = \LiveHelperChat\Models\LHCAbstract\OfflineReason::getReason($offlineReasonId);
    $isOnlineModeReason = ($reason !== false && $reason->isOnlineMode());

    // An "online mode" reason (e.g. Training) keeps the operator online, only the reason is applied.
    $goingOffline = ($Params['user_parameters']['status'] == 'true') && !$isOnlineModeReason;

    // Re-selecting the same online mode reason does not create another online session record.
    if ($isOnlineModeReason && (int)$userData->hide_online === 0 && $previousOfflineReasonId === $offlineReasonId) {
        $db->commit();
        echo json_encode(array('error' => false));
        exit;
    }

    // If already offline and only changing the reason, skip the full status toggle
    if ($goingOffline && $userData->hide_online == 1 && $offlineReasonId > 0 && $offlineReasonId != $previousOfflineReasonId) {
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

    if ($goingOffline) {
        $userData->hide_online = 1;
        $userData->offline_reason_id = $offlineReasonId;
    } else {
        $userData->hide_online = 0;
        // Online mode reasons are remembered while the operator is online, plain online clears the reason.
        $userData->offline_reason_id = $isOnlineModeReason ? $offlineReasonId : 0;
    }

    erLhcoreClassUser::getSession()->update($userData);

    // A reason with "Disable auto assigned" temporarily excludes the operator from auto assignment
    \LiveHelperChat\Models\LHCAbstract\OfflineReason::syncAutoAssignStatus($userData);

    // Went offline (2) OR went online (1). Any reason change while online starts a new online record.
    $reasonForSessionOffline = $goingOffline ? $userData->offline_reason_id : 0;
    $reasonForSessionOnline = (!$goingOffline && $isOnlineModeReason) ? $userData->offline_reason_id : 0;
    $currentUser->updateLastVisit(time(), $userData->hide_online == 1 ? 2 : 1, 0, $reasonForSessionOffline, $reasonForSessionOnline);

    if (!$goingOffline && $wasOnline) {
        // Switching reason while already online - close the previous record at the new one start (no offline gap)
        erLhcoreClassModelUserOnlineSession::closePreviousRecord($userData->id);
    }

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