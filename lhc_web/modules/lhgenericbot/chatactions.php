<?php

$tpl = erLhcoreClassTemplate::getInstance('lhgenericbot/chatactions.tpl.php');

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['id']);

if ( erLhcoreClassChat::hasAccessToRead($chat) )
{
    $tpl->set('chat',$chat);
} else {
    $tpl->setFile( 'lhchat/errors/adminchatnopermission.tpl.php');
}

echo $tpl->fetch();
exit;

?>