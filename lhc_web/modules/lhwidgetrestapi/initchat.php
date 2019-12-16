<?php

erLhcoreClassRestAPIHandler::setHeaders();
$requestPayload = json_decode(file_get_contents('php://input'),true);

try {
    $db = ezcDbInstance::get();
    $db->beginTransaction();

    $chat = erLhcoreClassModelChat::fetchAndLock($requestPayload['id']);

    erLhcoreClassChat::setTimeZoneByChat($chat);

    if ($chat->hash == $requestPayload['hash'])
    {
        // User online
        if ($chat->user_status != 0) {
            $chat->support_informed = 1;
            $chat->user_typing = time();// Show for shorter period these status messages
            $chat->is_user_typing = 1;
            if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != ''){

                $refererSite = $_SERVER['HTTP_REFERER'];

                if ($refererSite != '' && strlen($refererSite) > 50) {
                    if ( function_exists('mb_substr') ) {
                        $refererSite = mb_substr($refererSite, 0, 50);
                    } else {
                        $refererSite = substr($refererSite, 0, 50);
                    }
                }

                $chat->user_typing_txt = $refererSite;
            } else {
                $chat->user_typing_txt = htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/userjoined','Visitor has joined the chat!'),ENT_QUOTES);
            }

            if ($chat->user_status == erLhcoreClassModelChat::USER_STATUS_PENDING_REOPEN && ($onlineuser = $chat->online_user) !== false) {
                $onlineuser->reopen_chat = 0;
                $onlineuser->saveThis();
            }

            $chat->unread_op_messages_informed = 0;
            $chat->has_unread_op_messages = 0;
            $chat->unanswered_chat = 0;

            $chat->user_status = erLhcoreClassModelChat::USER_STATUS_JOINED_CHAT;

            if ($chat->unanswered_chat == 1 && $chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT)
            {
                $chat->unanswered_chat = 0;
            }

            erLhcoreClassChat::getSession()->update($chat);
        }

        $db->commit();

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chatwidgetchat',array('params' => & $Params, 'chat' => & $chat));

        $outputResponse = array(
            'operator' => 'operator',
            'messages' => [],
            'chat_ui' => array(
                
            )
        );

        if (isset($requestPayload['theme']) && $requestPayload['theme'] > 0) {
            $theme = erLhAbstractModelWidgetTheme::fetch($requestPayload['theme']);
              if (isset($theme->bot_configuration_array['placeholder_message']) && !empty($theme->bot_configuration_array['placeholder_message'])) {
                $outputResponse['chat_ui']['placeholder_message'] = $theme->bot_configuration_array['placeholder_message'];
            }
        }

        echo erLhcoreClassRestAPIHandler::outputResponse($outputResponse);
        
    } else {
        //$tpl->setFile( 'lhchat/errors/chatnotexists.tpl.php');
    }

} catch(Exception $e) {
    $db->rollback();
    //$tpl->setFile('lhchat/errors/chatnotexists.tpl.php');
}
exit;

?>