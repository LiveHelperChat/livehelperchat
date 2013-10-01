<?php

header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
header('Content-type: text/javascript');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s',time()+60*60*8 ) . ' GMT' );
header('Cache-Control: no-store, no-cache, must-revalidate' );
header('Cache-Control: post-check=0, pre-check=0', false );
header('Pragma: no-cache' );

$tpl = erLhcoreClassTemplate::getInstance('lhchat/chatcheckoperatormessage.tpl.php');

$userInstance = erLhcoreClassModelChatOnlineUser::handleRequest(array('department' => (string)$Params['user_parameters_unordered']['department'], 'identifier' => (string)$Params['user_parameters_unordered']['identifier'], 'pages_count' => ((int)$Params['user_parameters_unordered']['count_page'] == 1 ? true : false), 'vid' => (string)$Params['user_parameters_unordered']['vid'], 'check_message_operator' => true, 'pro_active_limitation' =>  erLhcoreClassModelChatConfig::fetch('pro_active_limitation')->current_value, 'pro_active_invite' => erLhcoreClassModelChatConfig::fetch('pro_active_invite')->current_value));

if ((int)$Params['user_parameters_unordered']['count_page'] == 1 && erLhcoreClassModelChatConfig::fetch('track_footprint')->current_value == 1 && isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
	erLhcoreClassModelChatOnlineUserFootprint::addPageView($userInstance);
}

if ($userInstance !== false) {
	$tpl->set('priority',is_numeric($Params['user_parameters_unordered']['priority']) ? (int)$Params['user_parameters_unordered']['priority'] : false);
	$tpl->set('visitor',$userInstance);
	$tpl->set('vid',(string)$Params['user_parameters_unordered']['vid']);
    echo $tpl->fetch();
}
exit;
?>