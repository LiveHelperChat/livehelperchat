<?php

$currentUser = erLhcoreClassUser::instance();
$UserData = $currentUser->getUserData(true);

if ($Params['user_parameters']['status'] == 'false') {
	$UserData->hide_online = 0;
} else {
	$UserData->hide_online = 1;
}

erLhcoreClassUser::getSession()->update($UserData);
erLhcoreClassUserDep::setHideOnlineStatus($UserData);

exit;
?>