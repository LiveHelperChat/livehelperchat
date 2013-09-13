<?php

header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
header("Content-type: text/javascript");

$tpl = erLhcoreClassTemplate::getInstance('lhchat/chatcheckoperatormessage.tpl.php');

$userInstance = erLhcoreClassModelChatOnlineUser::handleRequest(array('vid' => (string)$Params['user_parameters_unordered']['vid'], 'check_message_operator' => true, 'pro_active_limitation' =>  erLhcoreClassModelChatConfig::fetch('pro_active_limitation')->current_value, 'pro_active_invite' => erLhcoreClassModelChatConfig::fetch('pro_active_invite')->current_value));

if ($userInstance !== false) {
	$tpl->set('priority',is_numeric($Params['user_parameters_unordered']['priority']) ? (int)$Params['user_parameters_unordered']['priority'] : false);
	$tpl->set('visitor',$userInstance);
	$tpl->set('vid',(string)$Params['user_parameters_unordered']['vid']);
    echo $tpl->fetch();
}
exit;
?>