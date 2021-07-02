<?php

erLhcoreClassRestAPIHandler::setHeaders();

$id = $Params['user_parameters_unordered']['id'];
erLhcoreClassChat::validateFilterIn($id);

$response = array();

if (!empty($id)) {
    $chats = erLhcoreClassModelChat::getList(array('sort' => 'id DESC', 'filterin' => array('id' => $id)));
    foreach ($chats as $chat) {
        $item = array(
                'id' => $chat->id,
                'nick' => $chat->nick,
                'cs' => $chat->status,
                'co' => $chat->user_id,
                'dep' => (string)$chat->department,
                'cn' => (string)$chat->country_name,
                'us' => $chat->user_status_front,
                'pnd_rsp' => $chat->pnd_rsp,
                'um' => $chat->has_unread_op_messages,
                'lmsg' => erLhcoreClassChat::formatSeconds(time() - ($chat->last_user_msg_time > 0 ? $chat->last_user_msg_time : $chat->time)),
                'cc' => ($chat->country_code != '' ? erLhcoreClassDesign::design('images/flags') . '/' . (string)$chat->country_code . '.png' : ''),
                'msg' => erLhcoreClassChat::getGetLastChatMessagePending($chat->id, true, 3, ' » '),
                'vwa' => ($chat->status != erLhcoreClassModelChat::STATUS_CLOSED_CHAT && $chat->last_user_msg_time > ($chat->last_op_msg_time > 0 ? $chat->last_op_msg_time : $chat->pnd_time) && (time() - $chat->last_user_msg_time > (int)erLhcoreClassModelChatConfig::fetchCache('vwait_to_long')->current_value) ? erLhcoreClassChat::formatSeconds(time() - $chat->last_user_msg_time) : null)
        );

        $aicons = $chat->aicons;
        if (!empty($aicons)) {
            $item['aicons'] = $aicons;
        }

        $response[] = $item;
    }
}

echo json_encode($response);

exit;
?>