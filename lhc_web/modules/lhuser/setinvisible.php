<?php

header('Content-Type: application/json');

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
    echo json_encode(array('error' => true));
    exit;
}

$currentUser = erLhcoreClassUser::instance();
$userData = $currentUser->getUserData(true);

if ($Params['user_parameters']['status'] == 'false') {
	$userData->invisible_mode = 0;
} else {
	$userData->invisible_mode = 1;
}

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.operator_visibility_changed',array('user' => & $userData, 'reason' => 'user_action'));

erLhcoreClassUser::getSession()->update($userData);

echo json_encode(array('error' => false));
exit;

?>