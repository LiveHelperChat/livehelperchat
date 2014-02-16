<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/online_user/online_screenshot_image.tpl.php');

if (is_numeric($Params['user_parameters']['online_id']))
{
	try {
	    $online = erLhcoreClassModelChatOnlineUser::fetch($Params['user_parameters']['online_id']);
	    $tpl->set('online',$online);
	    echo $tpl->fetch();
	    exit; 
	} catch (Exception $e) {
		exit;
	} 
}
exit;
?>