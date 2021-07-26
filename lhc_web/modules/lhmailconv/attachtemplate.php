<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/attatchtemplate.tpl.php');

$message = erLhcoreClassModelMailconvMessage::fetch($Params['user_parameters']['id']);

$conv = $message->conversation;

if ($conv instanceof erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToRead($conv) )
{
    $tpl->set('dep_id', $conv->dep_id);
}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'popup';

?>