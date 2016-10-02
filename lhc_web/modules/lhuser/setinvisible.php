<?php

$currentUser = erLhcoreClassUser::instance();
$userData = $currentUser->getUserData(true);

if ($Params['user_parameters']['status'] == 'false') {
	$userData->invisible_mode = 0;
} else {
	$userData->invisible_mode = 1;
}

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.operator_visibility_changed',array('user' => & $userData, 'reason' => 'user_action'));

erLhcoreClassUser::getSession()->update($userData);
exit;

?>