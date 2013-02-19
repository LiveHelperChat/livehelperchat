<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/getstatus.tpl.php');

if ( erLhcoreClassModelChatConfig::fetch('track_online_visitors')->current_value == 1 ) {
    // To track online users
    erLhcoreClassModelChatOnlineUser::handleRequest();
}

$tpl->set('click',$Params['user_parameters_unordered']['click']);

echo $tpl->fetch();
exit;