<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/online_user/online_user_info.tpl.php');

if (is_numeric($Params['user_parameters']['chat_id']))
{
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

    if ( erLhcoreClassChat::hasAccessToRead($chat) )
    {
         $tpl->set('online_user',$chat->online_user);
         echo $tpl->fetch();
         exit;
    } else {
         $tpl->setFile('lhchat/errors/adminchatnopermission.tpl.php');
         echo $tpl->fetch();
         exit;
    }
}
exit;
?>