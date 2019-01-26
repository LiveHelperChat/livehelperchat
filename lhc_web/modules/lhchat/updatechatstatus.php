<?php
header ( 'content-type: application/json; charset=utf-8' );
$Chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

// Has access to read, chat
if ( erLhcoreClassChat::hasAccessToRead($Chat) && erLhcoreClassChat::hasAccessToWrite($Chat))
{
    $tpl = erLhcoreClassTemplate::getInstance('lhchat/chat_tabs/information_tab_user_info.tpl.php');
    $tpl->set('chat',$Chat);
    $tpl->set('canEditChat',true);
    echo erLhcoreClassChat::safe_json_encode(array('nick' => $Chat->nick, 'result' => $tpl->fetch()));
}

exit;
?>