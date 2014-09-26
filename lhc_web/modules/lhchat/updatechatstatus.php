<?php

$Chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

// Has access to read, chat
if ( erLhcoreClassChat::hasAccessToRead($Chat) )
{    	
    $tpl = erLhcoreClassTemplate::getInstance('lhchat/chat_tabs/information_tab_user_info.tpl.php');
    $tpl->set('chat',$Chat);
    echo json_encode(array('result' => $tpl->fetch()));
}

exit;
?>