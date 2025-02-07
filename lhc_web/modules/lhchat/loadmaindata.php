<?php

header('Content-Type: application/json');

$items = array();
$data_ext = array();

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

    if (isset($dataPrevious) && $dataPrevious['has_messages'] == true && isset($dataPrevious['chat_history']) && is_object($dataPrevious['chat_history'])) {
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

    // Check do we have private chat related to this chat.
    $groupChat = erLhcoreClassModelGroupChat::findOne(array('filter' => array('chat_id' => $chat->id)));

    if ($groupChat instanceof erLhcoreClassModelGroupChat) {
        $items[] = array (
            'selector' => '#private-chat-tab-link-' . $chat->id,
            'event_name' => 'privateChatStart',
            'event_value' => [$chat->id],
            'attr' => array(
                'private-loaded' => 'true'
            ),
            'action' => 'event'
        );
    }

    if (erLhcoreClassModelChatBlockedUser::isBlocked(array('online_user_id' => $chat->online_user_id, 'country_code' => $chat->country_code, 'ip' => $chat->ip, 'dep_id' => $chat->dep_id, 'nick' => $chat->nick, 'email' => $chat->email))) {
        $items[] = array (
            'selector' => '#block-status-' . $chat->id,
            'attr' => array(
                'text' => 'Visitor is blocked!'
            )
        );
        $items[] = array (
            'selector' => '#block-status-wrap-' . $chat->id,
            'action' =>'add_class',
            'class' => 'text-danger fw-bold'
        );
        $items[] = array (
            'selector' => '#block-status-wrap-' . $chat->id,
            'action' =>'remove_class',
            'class' => 'text-muted'
        );
    }

    $shortcutCommands = erLhcoreClassModelGenericBotCommand::getList(['customfilter' => ['(dep_id = 0 OR dep_id = ' . (int)$chat->dep_id . ')'], 'filternot' => ['shortcut_1' => '','shortcut_2' => '']]);
    foreach ($shortcutCommands as $command) {
        if ($command->enabled_display == 1) {
            $items[] = array (
                'selector' => '#CSChatMessage-' . $chat->id,
                'event_data' => [
                    'a' => strtolower($command->shortcut_1),
                    'b' => strtolower($command->shortcut_2),
                    'url' => 'chatcommand/command/' . $chat->id . '/' . $command->id,
                ],
                'action' => 'keyupmodal'
            );
        } else {
            $items[] = array (
                'selector' => '#CSChatMessage-' . $chat->id,
                'event_data' => [
                    'a' => strtolower($command->shortcut_1),
                    'b' => strtolower($command->shortcut_2),
                    'cmd' => trim($command->command . ' ' . $command->sub_command)
                ],
                'action' => 'keyup'
            );
        }

    }

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.loadmainchatdata',array(
        'chat' => $chat,
        'items' => & $items,
        'user' => $currentUser->getUserData(true),
        'data_ext' => & $data_ext));
}

echo json_encode(array('error' => true, 'items' => $items, 'data_ext' => $data_ext));
exit;
?>