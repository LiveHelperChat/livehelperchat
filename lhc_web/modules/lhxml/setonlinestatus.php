<?php

$options = erLhcoreClassModelChatConfig::fetch('mobile_options')->data;

if (!isset($options['notifications']) || !$options['notifications']) {
    exit;
}

$currentUser = erLhcoreClassUser::instance();

if (!$currentUser->isLogged() && !$currentUser->authenticate($_POST['username'],$_POST['password']))
{
	exit;
}

$UserData = $currentUser->getUserData(true);

if ( $currentUser->hasAccessTo('lhuser','changeonlinestatus') ) {

	if ($Params['user_parameters']['status'] == '0') {
		$UserData->hide_online = 0;
	} else {
		$UserData->hide_online = 1;
	}

	erLhcoreClassUser::getSession()->update($UserData);

	erLhcoreClassUserDep::setHideOnlineStatus($UserData);

    erLhcoreClassChat::updateActiveChats($UserData->id);

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.operator_status_changed',array('user' => & $UserData, 'reason' => 'user_action'));
}
exit;
?>