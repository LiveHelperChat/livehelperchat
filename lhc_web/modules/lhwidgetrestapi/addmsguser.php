<?php

erLhcoreClassRestAPIHandler::setHeaders();
erTranslationClassLhTranslation::$htmlEscape = false;

$payload = json_decode(file_get_contents('php://input'),true);

$r = '';
$error = 'f';

if (isset($payload['msg']) && trim($payload['msg']) != '' && trim(str_replace('[[msgitm]]', '',$payload['msg'])) != '' && mb_strlen($payload['msg']) < (int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value)
{
    try {
        $db = ezcDbInstance::get();

        $db->beginTransaction();

        $chat = erLhcoreClassModelChat::fetchAndLock($payload['id']);

        $validStatuses = array(
            erLhcoreClassModelChat::STATUS_PENDING_CHAT,
            erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,
            erLhcoreClassModelChat::STATUS_BOT_CHAT,
        );

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.validstatus_chat',array('chat' => & $chat, 'valid_statuses' => & $validStatuses));

        if ($chat->hash == $payload['hash'] && (in_array($chat->status,$validStatuses)) && !in_array($chat->status_sub, array(erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW,erLhcoreClassModelChat::STATUS_SUB_CONTACT_FORM))) // Allow add messages only if chat is active
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

                    if ($chat->chat_locale != '' && $chat->chat_locale_to != '') {
                        erLhcoreClassTranslate::translateChatMsgVisitor($chat, $msg);
                    }

                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_msg_user_saved',array('msg' => & $msg,'chat' => & $chat));

                    erLhcoreClassChat::getSession()->save($msg);
                }
            }

            if (!isset($msg)){
                $error = 't';
                $r = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter a message, max characters').' - '.(int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value;
                echo erLhcoreClassChat::safe_json_encode(array('error' => $error, 'r' => $r));
                exit;
            }

            if ($chat->gbot_id > 0 && (!isset($chat->chat_variables_array['gbot_disabled']) || $chat->chat_variables_array['gbot_disabled'] == 0)) {
                erLhcoreClassGenericBotWorkflow::userMessageAdded($chat, $msg);
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

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.addmsguser',array('chat' => & $chat, 'msg' => & $msg));
        } else {
            throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','You cannot send messages to this chat. Please refresh your browser.'));
        }

        $db->commit();
        echo erLhcoreClassChat::safe_json_encode(array('error' => $error, 'r' => $r));
        exit;

    } catch (Exception $e) {
        $db->rollback();
        echo erLhcoreClassChat::safe_json_encode(array('error' => 't', 'r' => $e->getMessage()));
        exit;
    }

} else {
    $error = 't';
    $r = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter a message') . ', ' . (int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','characters max.');
    echo erLhcoreClassChat::safe_json_encode(array('error' => $error, 'r' => $r));
    exit;
}



?>