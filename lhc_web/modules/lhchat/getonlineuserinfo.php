<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/getonlineuserinfo.tpl.php');

$onlineUser = erLhcoreClassModelChatOnlineUser::fetch($Params['user_parameters']['id']);
$tpl->set('online_user',$onlineUser);

echo $tpl->fetch();
exit;
?>