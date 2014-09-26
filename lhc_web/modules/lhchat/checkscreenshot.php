<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/chat_tabs/operator_screenshot_image.tpl.php');

if (is_numeric($Params['user_parameters']['chat_id']))
{
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

    if ( erLhcoreClassChat::hasAccessToRead($chat) )
    {
         $tpl->set('chat',$chat);
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