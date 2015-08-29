<?php

header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
header('Content-type: text/javascript');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s',time()+60*60*8 ) . ' GMT' );
header('Cache-Control: no-store, no-cache, must-revalidate' );
header('Cache-Control: post-check=0, pre-check=0', false );
header('Pragma: no-cache' );

$ignorable_ip = erLhcoreClassModelChatConfig::fetch('ignorable_ip')->current_value;

if ( $ignorable_ip == '' || !erLhcoreClassIPDetect::isIgnored(erLhcoreClassIPDetect::getIP(),explode(',',$ignorable_ip))) {
	
	if (is_array($Params['user_parameters_unordered']['department'])){
		erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['department']);
		$department = $Params['user_parameters_unordered']['department'];
	} else {
		$department = false;
	}
	
	$userInstance = erLhcoreClassModelChatOnlineUser::handleRequest(array('uactiv' => (int)$Params['user_parameters_unordered']['uactiv'], 'wopen' => (int)$Params['user_parameters_unordered']['wopen'], 'tz' => $Params['user_parameters_unordered']['tz'], 'message_seen_timeout' => erLhcoreClassModelChatConfig::fetch('message_seen_timeout')->current_value, 'department' => $department, 'identifier' => (string)$Params['user_parameters_unordered']['identifier'], 'pages_count' => true, 'vid' => (string)$Params['user_parameters_unordered']['vid']));
	
	if (erLhcoreClassModelChatConfig::fetch('track_footprint')->current_value == 1 && isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
		erLhcoreClassModelChatOnlineUserFootprint::addPageView($userInstance);
	}		
}
exit;
?>