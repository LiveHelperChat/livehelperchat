<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/attatchtemplate.tpl.php');

$message = erLhcoreClassModelMailconvMessage::fetch($Params['user_parameters']['id']);

$conv = $message->conversation;

if ($conv instanceof erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToRead($conv) )
{
    $tpl->set('dep_id', $conv->dep_id);
    $tpl->set('conversation_id',$conv->id);
    $tpl->set('message_id',$message->id);
}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'popup';

?>