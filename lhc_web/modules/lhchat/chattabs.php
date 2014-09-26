<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/chattabs.tpl.php');

if (is_numeric($Params['user_parameters']['chat_id']))
{
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
    $tpl->set('chat_to_load',$chat);
}
$tpl->set('chat_id',$Params['user_parameters']['chat_id']);

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'chattabs';

?>