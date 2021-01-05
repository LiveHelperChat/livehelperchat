<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/getonlineuserinfo.tpl.php');

$onlineUser = erLhcoreClassModelChatOnlineUser::fetch($Params['user_parameters']['id']);
$tpl->set('online_user',$onlineUser);
$tpl->set('tab',$Params['user_parameters_unordered']['tab']);

echo $tpl->fetch();
exit;
?>