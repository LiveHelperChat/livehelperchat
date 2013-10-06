<?php

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
}
exit;
?>