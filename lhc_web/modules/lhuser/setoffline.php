<?php

$currentUser = erLhcoreClassUser::instance();
$userData = $currentUser->getUserData(true);

if ($Params['user_parameters']['status'] == 'false') {
	$userData->hide_online = 0;
} else {
	$userData->hide_online = 1;
}

erLhcoreClassUser::getSession()->update($userData);
erLhcoreClassUserDep::setHideOnlineStatus($userData);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.operator_status_changed',array('user' => & $userData, 'reason' => 'user_action'));

exit;
?>