<?php

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

if ( erLhcoreClassChat::hasAccessToRead($chat) ) {

    if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
        exit;
    }

    $filter = [
        'limit' => false,
        'filter' => ['from_address' => $chat->email],
        'filterin' => ['status' =>
            [
                erLhcoreClassModelMailconvConversation::STATUS_PENDING,
                erLhcoreClassModelMailconvConversation::STATUS_ACTIVE,
            ]
        ],
    ];

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.related_actions', array('chat' => $chat, 'filter' => & $filter));

    $mails = erLhcoreClassModelMailconvConversation::getList($filter);

    if (!empty($mails)){
        $tpl = erLhcoreClassTemplate::getInstance('lhchat/relatedactions.tpl.php');
        $tpl->set('chat',$chat);
        $tpl->set('mails',$mails);
        $tpl->set('related_actions', json_decode(file_get_contents('php://input'),true));
    } else {
        exit;
    }

} else {
    exit;
}

echo $tpl->fetch();
exit;

?>