<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhxml/userinfo.tpl.php' );

try {
	$onlineUsers = erLhcoreClassModelChatOnlineUser::fetch((int)$Params['user_parameters']['user_id']);
	$tpl->set('onlineUsers',$onlineUsers);
	echo json_encode(array('user' => $tpl->fetch()));
} catch (Exception $e) {
	echo json_encode(array('user' => '-'));
}

exit;
?>