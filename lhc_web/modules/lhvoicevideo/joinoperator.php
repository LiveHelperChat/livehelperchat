<?php

$tpl = erLhcoreClassTemplate::getInstance('lhvoicevideo/joinoperator.tpl.php');

if (is_numeric($Params['user_parameters']['id']))
{
    $chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['id']);
    if ( erLhcoreClassChat::hasAccessToRead($chat) )
    {
        $voiceData = (array)erLhcoreClassModelChatConfig::fetch('vvsh_configuration')->data;
        $tpl->set('chat',$chat);
        $tpl->set('voice_data',$voiceData);
    } else {
        $tpl->setFile('lhchat/errors/adminchatnopermission.tpl.php');
    }
}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'voicevideo';

?>