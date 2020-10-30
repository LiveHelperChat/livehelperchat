<?php

erLhcoreClassRestAPIHandler::setHeaders();

$id = $Params['user_parameters_unordered']['id'];
erLhcoreClassChat::validateFilterIn($id);

$response = array();

if (!empty($id)) {
    $chats = erLhcoreClassModelChat::getList(array('sort' => 'id DESC', 'filterin' => array('id' => $id)));
    foreach ($chats as $chat) {
        $response[] = array(
            'id' => $chat->id,
            'nick' => $chat->nick,
            'cs' => $chat->status,
            'dep' => (string)$chat->department,
            'cn' => (string)$chat->country_name,
            'us' => $chat->user_status_front,
            'um' => $chat->has_unread_op_messages,
            'lmsg' => erLhcoreClassChat::formatSeconds(time() - ($chat->last_user_msg_time > 0 ? $chat->last_user_msg_time : $chat->time)),
            'cc' => ($chat->country_code != '' ? erLhcoreClassDesign::design('images/flags') . '/' . (string)$chat->country_code . '.png' : ''),
            'msg' => erLhcoreClassChat::getGetLastChatMessagePending($chat->id, true, 3, ' » '),
        );
    }
}

echo json_encode($response);

exit;
?>