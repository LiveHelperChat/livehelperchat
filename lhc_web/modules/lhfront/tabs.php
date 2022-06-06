<?php

erLhcoreClassRestAPIHandler::setHeaders();

$id = $Params['user_parameters_unordered']['id'];

if (is_array($id)) {
    erLhcoreClassChat::validateFilterIn($id);
}

$response = array();

if (!empty($id)) {

    $icons_additional = erLhAbstractModelChatColumn::getList(array('ignore_fields' => array('position','conditions','column_identifier','enabled'), 'sort' => false, 'filter' => array('icon_mode' => 1, 'enabled' => 1, 'chat_enabled' => 1)));

    $chats = erLhcoreClassModelChat::getList(array('sort' => 'id ASC', 'filterin' => array('id' => $id)));

    if (!empty($icons_additional)) {
        erLhcoreClassChat::prefillGetAttributes($chats, array(), array(), array('additional_columns' => $icons_additional, 'do_not_clean' => true));
    }

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
                'lmsg_id' => $chat->last_msg_id,
                'cc' => ($chat->country_code != '' ? erLhcoreClassDesign::design('images/flags') . '/' . (string)$chat->country_code . '.png' : ''),
                'msg' => erLhcoreClassChat::getGetLastChatMessagePending($chat->id, true, 3, ' » '),
                'vwa' => ($chat->status != erLhcoreClassModelChat::STATUS_CLOSED_CHAT && $chat->last_user_msg_time > ($chat->last_op_msg_time > 0 ? $chat->last_op_msg_time : $chat->pnd_time) && (time() - $chat->last_user_msg_time > (int)erLhcoreClassModelChatConfig::fetchCache('vwait_to_long')->current_value) ? erLhcoreClassChat::formatSeconds(time() - $chat->last_user_msg_time) : null)
        );

        $chatIcons = [];
        foreach ($icons_additional as $iconAdditional) {
            $columnIconData = json_decode($iconAdditional->column_icon,true);
            if (isset($chat->{'cc_' . $iconAdditional->id})) {
                $chatIcons[] = [
                    'has_popup' => $iconAdditional->has_popup,
                    'icon_id' => $iconAdditional->id,
                    'title' => (isset($chat->{'cc_' . $iconAdditional->id . '_tt'})) ? $chat->{'cc_' . $iconAdditional->id . '_tt'} : (isset($chat->{'cc_' . $iconAdditional->id}) ? $chat->{'cc_' . $iconAdditional->id} : ''),
                    'icon' => ($iconAdditional->column_icon != "" && strpos($iconAdditional->column_icon, '"') !== false) ? $columnIconData[$chat->{'cc_' . $iconAdditional->id}]['icon'] : $iconAdditional->column_icon,
                    'color' => isset($columnIconData[$chat->{'cc_' . $iconAdditional->id}]['color']) ? $columnIconData[$chat->{'cc_' . $iconAdditional->id}]['color'] : '#CECECE'
                ];
            }
        }
        $item['adicons'] = $chatIcons;

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