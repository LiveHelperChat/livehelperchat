<?php

header('Content-Type: application/json');
$db = ezcDbInstance::get();

try {
    $db->beginTransaction();

    $currentUser = erLhcoreClassUser::instance();
    $userData = $currentUser->getUserData(true);

    // Lock the user record to prevent race conditions when updating inactive_mode
    $userData->syncAndLock();

    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        throw new Exception('Invalid CSFR Token');
    }

    // We have to check is operator really inactive or it's just a tab trying to set inactive mode
    if ($Params['user_parameters']['status'] == 'true') {
        $activityTimeout = erLhcoreClassModelUserSetting::getSetting('trackactivitytimeout',-1);

        // If there is no individual setting user global one
        if ($activityTimeout == -1) {
            $activityTimeout = (int)erLhcoreClassModelChatConfig::fetchCache('activity_timeout')->current_value*60;
        }

        // Operator was still active in another tab, do nothing
        if ($activityTimeout > (time() - $userData->lastd_activity)) {
            $db->commit();
            echo json_encode(array('error' => false, 'active' => true));
            exit;
        }
    }

    $originalInactiveMode = $userData->inactive_mode;

    if ($Params['user_parameters']['status'] == 'true') {
        $userData->inactive_mode = 1;
    } else {
        $userData->inactive_mode = 0;
    }

    erLhcoreClassUser::getSession()->update($userData);

    // Construct temporary object to change inactive modes
    $userDataTemp = new stdClass();
    $userDataTemp->id = $userData->id;

    if ($userData->hide_online == 0) { // change status only if he's not offline manually
        $userDataTemp->hide_online = $userData->inactive_mode;
        $userDataTemp->always_on = $userData->always_on;

        erLhcoreClassUserDep::setHideOnlineStatus($userDataTemp);

        if ($originalInactiveMode != $userData->inactive_mode){
            $currentUser->updateLastVisit(time(), $userDataTemp->hide_online == 1 ? 2 : 1); // Went offline OR went online
        }
    }

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.operator_inactivemode_changed',array('user' => & $userData, 'reason' => 'user_action'));

    echo json_encode(array('error' => false, 'active' => false));

    $db->commit();

} catch (Exception $e) {
    echo json_encode(array('error' => true, 'msg' => $e->getMessage()));
    $db->rollback();
}

exit;

?>