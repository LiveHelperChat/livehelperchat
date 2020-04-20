<?php

header('Content-Type: application/json');

$items = array();

$chat = erLhcoreClassModelChat::fetchAndLock($Params['user_parameters']['chat_id']);

if ($chat instanceof erLhcoreClassModelChat && erLhcoreClassChat::hasAccessToRead($chat) ) {

    if ($chat->status == erLhcoreClassModelChat::STATUS_OPERATORS_CHAT) {
        if ($currentUser->getUserID() != $chat->user_id && $chat->user_id > 0 && $chat->n_off_full !== false) {
            $nick = $chat->n_off_full;
        } elseif ($currentUser->getUserID() != $chat->sender_user_id && $chat->sender_user_id > 0) {
            try {
                $nick = erLhcoreClassModelUser::fetch($chat->sender_user_id)->name_official;
            } catch (Exception $e) {

            }
        }

        $items[] = array (
            'selector' => '#user-chat-status-' . $chat->id,
            'attr' => array(
                'text' => 'group'
            )
        );

        if (isset($nick)) {
            $items[] = array (
                'selector' => '#ntab-chat-' . $chat->id,
                'attr' => array(
                    'text' => erLhcoreClassDesign::shrt($nick,10,'...',30,ENT_QUOTES),
                )
            );
        }
    }

    $messages = erLhcoreClassChat::getChatMessages($chat->id, erLhcoreClassChat::$limitMessages);

    $dataPrevious = erLhcoreClassChatWorkflow::hasPreviousChats(array(
        'messages' => $messages,
        'chat' => $chat,
    ));

    if ($dataPrevious['has_messages'] == true && isset($dataPrevious['chat_history']) && is_object($dataPrevious['chat_history'])) {
        $items[] = array (
            'selector' => '#load-prev-btn-' . $chat->id,
            'action' => 'show',
            'attr' => array(
                'chat-original-id' => $chat->id,
                'chat-id' => $dataPrevious['chat_history']->id,
                'message-id' => $dataPrevious['message_id']
            )
        );
    } else {
        $items[] = array (
            'selector' => '#load-prev-btn-' . $chat->id,
            'action' => 'hide'
        );
    }

    $loadPrevious = false;

    if ($dataPrevious['has_messages'] == true)
    {
        $loadPrevious = erLhcoreClassModelUserSetting::getSetting('auto_preload',0) == 1;
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.auto_preload',array('chat' => $chat, 'load_previous' => & $loadPrevious));
    }

    if ($loadPrevious == 1) {

        $soundData = erLhcoreClassModelChatConfig::fetch('sync_sound_settings');
        $data = (array)$soundData->data;

        if (isset($data['preload_messages']) && $data['preload_messages'] == 1)
        {
            $items[] = array (
                'selector' => '#load-prev-btn-' . $chat->id,
                'action' => 'click'
            );
        }
    }
}



echo json_encode(array('error' => true, 'items' => $items));
exit;
?>