<?php

$hash = sha1(erLhcoreClassIPDetect::getIP().$Params['user_parameters']['timets'].erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' ));

if ( (time()-$Params['user_parameters']['timets']) > 600 || (time()-$Params['user_parameters']['timets']) < 0) {
	echo json_encode(array('result' => 'false'));
	exit;
}

if (erLhcoreClassModelChatConfig::fetch('session_captcha')->current_value == 1) {
	// Start session if required only
	$currentUser = erLhcoreClassUser::instance();
	
	$_SESSION[$_SERVER['REMOTE_ADDR']][$Params['user_parameters']['captcha_name']] = $hash;
}

echo json_encode(array('result' => $hash));

exit;