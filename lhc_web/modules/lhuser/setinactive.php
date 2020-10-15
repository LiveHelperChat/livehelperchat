<?php

$currentUser = erLhcoreClassUser::instance();
$userData = $currentUser->getUserData(true);

// We have to check is operator really inactive or it's just a tab trying to set inactive mode
if ($Params['user_parameters']['status'] == 'true') {
    $activityTimeout = erLhcoreClassModelUserSetting::getSetting('trackactivitytimeout',-1);

    // If there is no individual setting user global one
    if ($activityTimeout == -1) {
        $activityTimeout = (int)erLhcoreClassModelChatConfig::fetchCache('activity_timeout')->current_value*60;
    }

    // Operator was still active in another tab, do nothing
    if ($activityTimeout > (time() - $userData->lastd_activity)) {
        echo json_encode(array('error' => false, 'active' => true));
        exit;
    }
}

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
}

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.operator_inactivemode_changed',array('user' => & $userData, 'reason' => 'user_action'));


echo json_encode(array('error' => false, 'active' => false));

exit;

?>