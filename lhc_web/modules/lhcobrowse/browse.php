<?php 

$tpl = erLhcoreClassTemplate::getInstance('lhcobrowse/browse.tpl.php');

if (is_numeric($Params['user_parameters']['chat_id']))
{
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
    if ( erLhcoreClassChat::hasAccessToRead($chat) )
    {
         $tpl->set('chat',$chat); 
         $tpl->set('browse',erLhcoreClassCoBrowse::getBrowseInstance($chat));
    } else {
         $tpl->setFile('lhchat/errors/adminchatnopermission.tpl.php');
    }
}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'cobrowse';

?>