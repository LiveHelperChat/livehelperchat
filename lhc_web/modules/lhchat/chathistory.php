<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/chathistory.tpl.php');

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
if ( erLhcoreClassChat::hasAccessToRead($chat) )
{
    $tpl->set('chat',$chat);
} else {
    $tpl->setFile( 'lhchat/errors/adminchatnopermission.tpl.php');
}

echo $tpl->fetch();
exit;

?>