<?php

header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
header('content-type: application/json; charset=utf-8');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s',time()+60*60*8 ) . ' GMT' );
header('Cache-Control: no-store, no-cache, must-revalidate' );
header('Cache-Control: post-check=0, pre-check=0', false );
header('Pragma: no-cache' );

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