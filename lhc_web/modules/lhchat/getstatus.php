<?php

// For IE to support headers if chat is installed on different domain
header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');

$tpl = erLhcoreClassTemplate::getInstance('lhchat/getstatus.tpl.php');

if ( erLhcoreClassModelChatConfig::fetch('track_online_visitors')->current_value == 1 ) {
    // To track online users
    erLhcoreClassModelChatOnlineUser::handleRequest();
}

$tpl->set('click',$Params['user_parameters_unordered']['click']);
$tpl->set('position',$Params['user_parameters_unordered']['position']);
$tpl->set('hide_offline',$Params['user_parameters_unordered']['hide_offline']);

echo $tpl->fetch();
exit;