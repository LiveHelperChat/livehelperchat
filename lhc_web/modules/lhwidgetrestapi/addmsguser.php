<?php

erLhcoreClassRestAPIHandler::setHeaders();
erTranslationClassLhTranslation::$htmlEscape = false;

$payload = json_decode(file_get_contents('php://input'),true);

$r = '';

try {
    $minLengthMessage = (int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value;

    if ($minLengthMessage === 0) {
        $minLengthMessage = (int)erLhcoreClassModelChatConfig::fetchException('max_message_length')->current_value;
    }

} catch (Exception $e) {

    $minLengthMessage = 500;

    // Log to file
    erLhcoreClassLog::write($e->getMessage() . ' - ' . $e->getTraceAsString());

    // Log to database
    erLhcoreClassLog::write($e->getMessage() . ' - ' . $e->getTraceAsString() ,
        ezcLog::SUCCESS_AUDIT,
        array(
            'source' => 'lhc',
            'category' => 'store',
            'line' => $e->getLine(),
            'file' => 'addmsguser.php',
            'object_id' => $payload['id']
        )
    );
}


if (isset($payload['msg']) && trim($payload['msg']) != '' && trim(str_replace('[[msgitm]]', '',$payload['msg'])) != '' && mb_strlen($payload['msg']) <= $minLengthMessage)
{
    try {
        $db = ezcDbInstance::get();

        $db->beginTransaction();

        $chat = erLhcoreClassModelChat::fetchAndLock($payload['id']);

        // We do not want to call mobile notifications and any related database calls
        if (!isset($payload['mn']) || $chat->status != erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) {
            erLhcoreClassChatEventDispatcher::getInstance()->disableMobile = true;
        }

        $validStatuses = array(
            erLhcoreClassModelChat::STATUS_PENDING_CHAT,
            erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,
            erLhcoreClassModelChat::STATUS_BOT_CHAT,
        );

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.validstatus_chat',array('chat' => & $chat, 'valid_statuses' => & $validStatuses));

        if ($chat->hash === $payload['hash'] && (in_array($chat->status,$validStatuses)) && !in_array($chat->status_sub, array(erLhcoreClassModelChat::STATUS_SUB_SURVEY_COMPLETED, erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT, erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW, erLhcoreClassModelChat::STATUS_SUB_CONTACT_FORM))) // Allow add messages only if chat is active
        {
            $msgText = preg_replace('/\[html\](.*?)\[\/html\]/ms','',$payload['msg']);

            $messagesToStore = explode('[[msgitm]]', trim($msgText));

            foreach ($messagesToStore as $messageText)
            {
                if (trim($messageText) != '')
                {
                    $msg = new erLhcoreClassModelmsg();
                    $msg->msg = trim($messageText);
                    $msg->chat_id = $payload['id'];
                    $msg->user_id = 0;
                    $msg->time = time();

                    if ($chat->chat_locale != '' && $chat->chat_locale_to != '' && isset($chat->chat_variables_array['lhc_live_trans']) && $chat->chat_variables_array['lhc_live_trans'] === true) {
                        erLhcoreClassTranslate::translateChatMsgVisitor($chat, $msg);
                    }

                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_msg_user_saved',array('msg' => & $msg,'chat' => & $chat));

                    erLhcoreClassChat::getSession()->save($msg);
                }
            }

            if (!isset($msg)) {
                $r = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter a message, max characters').' - '.$minLengthMessage;
                echo erLhcoreClassChat::safe_json_encode(array('error' => true, 'r' => $r));
                exit;
            }

            $triggers = [];
            if ($chat->gbot_id > 0 && (!isset($chat->chat_variables_array['gbot_disabled']) || $chat->chat_variables_array['gbot_disabled'] == 0)) {
                erLhcoreClassGenericBotWorkflow::userMessageAdded($chat, $msg);
                $triggers = erLhcoreClassGenericBotWorkflow::$triggerName;
            }

            // Reset active counter if visitor send new message and now user is the last message
            if ($chat->status_sub != erLhcoreClassModelChat::STATUS_SUB_ON_HOLD && $chat->auto_responder !== false) {
                if ($chat->auto_responder->active_send_status != 0 && $chat->last_user_msg_time < $chat->last_op_msg_time) {
                    $chat->auto_responder->active_send_status = 0;
                    $chat->auto_responder->saveThis();
                }
            }

            $updateFields = array(
                'last_user_msg_time',
                'lsync',
                'last_msg_id',
                'has_unread_messages',
                'unanswered_chat',
            );

            if ($chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT) {
                $chatVariables = $chat->chat_variables_array;
                if (!isset($chatVariables['msg_v'])) {
                    $chatVariables['msg_v'] = 1;
                } else {
                    $chatVariables['msg_v']++;
                }
                $chat->chat_variables_array = $chatVariables;
                $chat->chat_variables = json_encode($chatVariables);
                $updateFields[] = 'chat_variables';
            }

            // Visitor hold should be removed on visitor message
            /*if ($chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_ON_HOLD && isset($chat->chat_variables_array['lhc_hldu'])) {
                $chat->status_sub = 0;
                $chat->operation_admin .= ";$('#hold-action-usr-".$chat->id."').removeClass('btn-outline-info')";
                $chatVariables = $chat->chat_variables_array;
                unset($chatVariables['lhc_hldu']);
                $chat->chat_variables_array = $chatVariables;
                $chat->chat_variables = json_encode($chatVariables);
                $updateFields[] = 'chat_variables';
                $updateFields[] = 'status_sub';
                $updateFields[] = 'operation_admin';
            }*/

            $chat->last_user_msg_time = $msg->time;
            $chat->lsync = time();
            $chat->last_msg_id = $chat->last_msg_id < $msg->id ? $msg->id : $chat->last_msg_id;
            $chat->has_unread_messages = ($chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT ? 0 : 1);
            $chat->unanswered_chat = ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT ? 1 : 0);
            $chat->updateThis(array('update' => $updateFields));

            if ($chat->has_unread_messages == 1 && $chat->last_user_msg_time < (time() - 5)) {
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.unread_chat',array('chat' => & $chat));
            }

            // Assign to last message all the texts
            $msg->msg = trim(implode("\n", $messagesToStore));

        } else {
            throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','You cannot send messages to this chat. Chat has been closed.'), 100);
        }

        $db->commit();

        echo erLhcoreClassChat::safe_json_encode(array('r' => $r, 't' => $triggers));

        // Try to finish request before any listers do their job
        flush();
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }

        // Log executed triggers if required
        if (!empty($triggers) && isset($chat->chat_variables_array['gbot_debug']) && $chat->chat_variables_array['gbot_debug'] == 1) {
            erLhcoreClassLog::write(json_encode(erLhcoreClassGenericBotWorkflow::$triggerNameDebug,JSON_PRETTY_PRINT),
                ezcLog::SUCCESS_AUDIT,
                array(
                    'source' => 'lhc',
                    'category' => 'bot',
                    'line' => 0,
                    'file' => 'addmsguser.php',
                    'object_id' => $chat->id
                )
            );
        }

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.addmsguser',array('chat' => & $chat, 'msg' => & $msg));
        exit;

    } catch (Exception $e) {

        if ($e->getCode() !== 100) {
            echo erLhcoreClassChat::safe_json_encode(array('error' => true, 'r' => $e->getMessage(), 'system' => true));
        } else {
            echo erLhcoreClassChat::safe_json_encode(array('error' => true, 'r' => $e->getMessage()));
        }

        if ($e->getCode() !== 100) {
            $statusString = '';

            if (isset($chat)) {
                $statusString = ' | '. $chat->status . '_' . $chat->satus_sub;
            }

            erLhcoreClassLog::write($e->getMessage() . ' - ' . $e->getTraceAsString() . $statusString,
                ezcLog::SUCCESS_AUDIT,
                array(
                    'source' => 'lhc',
                    'category' => 'store',
                    'line' => $e->getLine(),
                    'file' => 'addmsguser.php',
                    'object_id' => $payload['id']
                )
            );
        }

        exit;
    }

} else {
    $r = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter a message') . ', ' . $minLengthMessage . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','characters max.');
    echo erLhcoreClassChat::safe_json_encode(array('error' => true, 'r' => $r));
    exit;
}



?>