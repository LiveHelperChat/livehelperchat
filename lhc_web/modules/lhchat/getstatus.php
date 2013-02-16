<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/getstatus.tpl.php');

if ( erLhcoreClassModelChatConfig::fetch('track_online_visitors')->current_value == 1 ) {
    // To track online users
    erLhcoreClassModelChatOnlineUser::handleRequest();
}

echo $tpl->fetch();
exit;