<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/single.tpl.php');

if (is_numeric($Params['user_parameters']['chat_id']))
{
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

    if ( erLhcoreClassChat::hasAccessToRead($chat) )
    {
         $tpl->set('chat_to_load',$chat);
    } else {
         $tpl->setFile('lhchat/errors/adminchatnopermission.tpl.php');
    }

}
$tpl->set('chat_id',$Params['user_parameters']['chat_id']);

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'chattabs';

$title = isset($chat) ? $chat->nick : '.';

$Result['path'] = array(array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/single','Chat started with').' - '.$title))


?>