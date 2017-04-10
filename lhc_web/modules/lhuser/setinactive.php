<?php

$currentUser = erLhcoreClassUser::instance();
$userData = $currentUser->getUserData(true);

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
    
    erLhcoreClassUserDep::setHideOnlineStatus($userDataTemp);
}

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.operator_inactivemode_changed',array('user' => & $userData, 'reason' => 'user_action'));

echo json_encode(array('error' => false));

exit;

?>