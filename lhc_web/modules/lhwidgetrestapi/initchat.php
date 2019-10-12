<?php

erLhcoreClassRestAPIHandler::setHeaders();
$requestPayload = json_decode(file_get_contents('php://input'),true);

/*if ($Params['user_parameters_unordered']['sound'] !== null && is_numeric($Params['user_parameters_unordered']['sound'])) {
    erLhcoreClassModelUserSetting::setSetting('chat_message',(int)$Params['user_parameters_unordered']['sound'] == 1 ? 1 : 0);
}*/

try {
    $db = ezcDbInstance::get();
    $db->beginTransaction();

    $chat = erLhcoreClassModelChat::fetchAndLock($requestPayload['id']);

    erLhcoreClassChat::setTimeZoneByChat($chat);

    /*if (is_numeric($Params['user_parameters_unordered']['pchat'])) {
        erLhcoreClassChatPaid::openChatWidget(array(
            'tpl' => & $tpl,
            'pchat' => $Params['user_parameters_unordered']['pchat'],
            'chat' => $chat
        ));
    }*/

    if ($chat->hash == $requestPayload['hash'])
    {
        //$survey = is_numeric($Params['user_parameters_unordered']['survey']) ? (int)$Params['user_parameters_unordered']['survey'] : false;
        /*$tpl->set('chat_id',$Params['user_parameters']['chat_id']);
        $tpl->set('hash',$Params['user_parameters']['hash']);
        $tpl->set('chat',$chat);
        $tpl->set('chat_widget_mode',true);
        $tpl->set('chat_embed_mode',$embedMode);
        $tpl->set('survey',$survey);*/

        //if ($survey > 0) {
            //$Result['parent_messages'][] = 'lhc_chat_survey:' . $survey;
        //}

        //$Result['chat'] = $chat;

/*        // If survey send parent message instantly
        if ($chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW) {
            $args = erLhcoreClassChatHelper::getSubStatusArguments($chat);
            $Result['parent_messages'][] = 'lhc_chat_closed' . ($args != '' ? ':' . $args : '');
        }*/

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

/*            $nick = isset($_GET['prefill']['username']) ? trim($_GET['prefill']['username']) : '';

            // Update nick if required
            if (isset($_GET['prefill']['username']) && $chat->nick != $_GET['prefill']['username'] && !empty($nick) && $chat->nick == erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Visitor')) {
                $chat->nick = $_GET['prefill']['username'];
                $chat->operation_admin .= "lhinst.updateVoteStatus(".$chat->id.");";

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.nickchanged', array('chat' => & $chat));
            }*/

            if ($chat->unanswered_chat == 1 && $chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT)
            {
                $chat->unanswered_chat = 0;
            }

            erLhcoreClassChat::getSession()->update($chat);
        }

        $db->commit();

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chatwidgetchat',array('params' => & $Params, 'chat' => & $chat));

/*        // Parse and return messages
        $messages = erLhcoreClassChat::getChatMessages($chat->id);
        $tpl = erLhcoreClassTemplate::getInstance( 'lhwidgetrestapi/messages.tpl.php');
        $tpl->set('messages', $messages);

        end($messages);
        $lastMessage = current($messages);

        $messagesParsed = $tpl->fetch();*/

        $outputResponse = array(
            'operator' => 'operator',
            'messages' => []
        );

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