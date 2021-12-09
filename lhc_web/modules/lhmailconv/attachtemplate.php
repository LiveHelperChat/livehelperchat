<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/attatchtemplate.tpl.php');

$tpl->set('dep_id', 0);

if (is_numeric($Params['user_parameters']['id'])) {
    $message = erLhcoreClassModelMailconvMessage::fetch($Params['user_parameters']['id']);
    if ($message instanceof erLhcoreClassModelMailconvMessage) {
        $conv = $message->conversation;
        if ($conv instanceof erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToRead($conv) )
        {
            $tpl->set('dep_id', $conv->dep_id);
            $tpl->set('conversation_id',$conv->id);
            $tpl->set('message_id',$message->id);
        }
    }
} else {
    $mailbox = erLhcoreClassModelMailconvMailbox::findOne(['filter' => ['mail' => $Params['user_parameters']['id']]]);
    if ($mailbox instanceof erLhcoreClassModelMailconvMailbox && $mailbox->dep_id > 0) {
        $tpl->set('dep_id', $mailbox->dep_id);
    }
}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'popup';

?>