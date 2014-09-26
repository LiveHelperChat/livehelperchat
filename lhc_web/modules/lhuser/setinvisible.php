<?php

$currentUser = erLhcoreClassUser::instance();
$UserData = $currentUser->getUserData(true);

if ($Params['user_parameters']['status'] == 'false') {
	$UserData->invisible_mode = 0;
} else {
	$UserData->invisible_mode = 1;
}

erLhcoreClassUser::getSession()->update($UserData);
exit;

?>