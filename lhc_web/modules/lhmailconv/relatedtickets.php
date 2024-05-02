<?php

session_write_close();

header ( 'content-type: application/json; charset=utf-8' );

$chat = erLhcoreClassModelChat::fetchAndLock($Params['user_parameters']['id']);

if ($chat instanceof erLhcoreClassModelChat && erLhcoreClassChat::hasAccessToRead($chat) )
{
    $tpl = erLhcoreClassTemplate::getInstance('lhmailconv/relatedtickets.tpl.php');

    if ( trim($chat->email) != '') {
        $filter = [
            'limit' => false,
            'filter' => ['from_address' => trim($chat->email)],
            'filterin' => ['status' =>
                [
                    erLhcoreClassModelMailconvConversation::STATUS_PENDING,
                    erLhcoreClassModelMailconvConversation::STATUS_ACTIVE,
                ]
            ],
        ];

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.related_actions', array('chat' => $chat, 'filter' => & $filter));

        $tpl->set('mails', erLhcoreClassModelMailconvConversation::getList($filter));
    } else {
        $tpl->set('mails', []);
    }

    $tpl->set('chat', $chat);

    $data = $tpl->fetch();

    echo json_encode([
        'data' => $data,
    ],\JSON_INVALID_UTF8_IGNORE);

} else {
    echo json_encode([
        'data' => 'No permission to read a chat',
    ]);
}



exit;

?>