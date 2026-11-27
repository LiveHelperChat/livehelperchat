<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/attatchtemplate.tpl.php');

$depId = 0;

if (is_numeric($Params['user_parameters']['id'])) {
    $message = erLhcoreClassModelMailconvMessage::fetch($Params['user_parameters']['id']);
    if ($message instanceof erLhcoreClassModelMailconvMessage) {
        $conv = $message->conversation;
        if ($conv instanceof erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToRead($conv) )
        {
            $depId = $conv->dep_id;
            $tpl->set('conversation_id',$conv->id);
            $tpl->set('message_id',$message->id);
        }
    }
} else {
    $mailbox = erLhcoreClassModelMailconvMailbox::findOne(['filter' => ['mail' => $Params['user_parameters']['id']]]);
    if ($mailbox instanceof erLhcoreClassModelMailconvMailbox && $mailbox->dep_id > 0) {
        $depId = $mailbox->dep_id;
    }
}

if (isset($Params['user_parameters_unordered']['chat_id']) && is_numeric($Params['user_parameters_unordered']['chat_id'])) {
    $chat = erLhcoreClassModelChat::fetch($Params['user_parameters_unordered']['chat_id']);
    if ($chat instanceof erLhcoreClassModelChat && erLhcoreClassChat::hasAccessToRead($chat)) {
        $tpl->set('chat_id', $chat->id);
        if ($depId == 0 && $chat->dep_id > 0) {
            $depId = $chat->dep_id;
        }
    }
}

$tpl->set('dep_id', $depId);

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'popup';

?>