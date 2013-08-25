<?php

header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');
header("Content-type: text/javascript");

$tpl = erLhcoreClassTemplate::getInstance('lhchat/chatcheckoperatormessage.tpl.php');

$userInstance = erLhcoreClassModelChatOnlineUser::handleRequest(array('pro_active_limitation' =>  erLhcoreClassModelChatConfig::fetch('pro_active_limitation')->current_value, 'pro_active_invite' => erLhcoreClassModelChatConfig::fetch('pro_active_invite')->current_value));

if ($userInstance !== false) {	
	$tpl->set('priority',is_numeric($Params['user_parameters_unordered']['priority']) ? (int)$Params['user_parameters_unordered']['priority'] : false);
	$tpl->set('visitor',$userInstance);
    echo $tpl->fetch();
}
exit;
?>