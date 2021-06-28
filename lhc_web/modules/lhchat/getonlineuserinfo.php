<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/getonlineuserinfo.tpl.php');

$onlineUser = erLhcoreClassModelChatOnlineUser::fetch($Params['user_parameters']['id']);

if (!is_object($onlineUser)) {
    $onlineUser = new erLhcoreClassModelChatOnlineUser();
}

$tpl->set('online_user',$onlineUser);
$tpl->set('tab',$Params['user_parameters_unordered']['tab']);
$tpl->set('chat_id_present',$Params['user_parameters_unordered']['chat_id']);

if (isset($_GET['popup'])) {
    $tpl->set('popup',true);
    $Result['content'] = $tpl->fetch();
    $Result['pagelayout'] = 'chattabs';
} else {
    echo $tpl->fetch();
    exit;
}


?>