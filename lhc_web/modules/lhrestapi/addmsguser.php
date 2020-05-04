<?php

erLhcoreClassRestAPIHandler::setHeaders();

$definition = array(
    'msg' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
    )
);

$form = new ezcInputForm( INPUT_POST, $definition );
$r = '';
$error = false;

if ($form->hasValidData( 'msg' ) && trim($form->msg) != '' && trim(str_replace('[[msgitm]]', '',$form->msg)) != '' && mb_strlen($form->msg) < (int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value)
{
    try {
        $db = ezcDbInstance::get();

        $db->beginTransaction();

        $chat = erLhcoreClassModelChat::fetchAndLock($_POST['chat_id']);

        $validStatuses = array(
            erLhcoreClassModelChat::STATUS_PENDING_CHAT,
            erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,
            erLhcoreClassModelChat::STATUS_BOT_CHAT,
        );

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.validstatus_chat',array('chat' => & $chat, 'valid_statuses' => & $validStatuses));

        if (((isset($_POST['hash']) && $chat->hash == $_POST['hash']) || erLhcoreClassRestAPIHandler::validateRequest()) && (in_array($chat->status,$validStatuses)) && !in_array($chat->status_sub, array(erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW,erLhcoreClassModelChat::STATUS_SUB_CONTACT_FORM))) // Allow add messages only if chat is active
        {
            $messagesToStore = explode('[[msgitm]]', trim($form->msg));

            foreach ($messagesToStore as $messageText)
            {
                if (trim($messageText) != '')
                {
                    $msg = new erLhcoreClassModelmsg();
                    $msg->msg = trim($messageText);
                    $msg->chat_id = $_POST['chat_id'];
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
                $error = true;
                $r = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter a message, max characters').' - '.(int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value;
                http_response_code(400);
                echo erLhcoreClassChat::safe_json_encode(array('error' => $error, 'r' => $r));
                exit;
            }

            $stmt = $db->prepare('UPDATE lh_chat SET last_user_msg_time = :last_user_msg_time, lsync = :lsync, last_msg_id = :last_msg_id, has_unread_messages = 1, unanswered_chat = :unanswered_chat WHERE id = :id');
            $stmt->bindValue(':id', $chat->id, PDO::PARAM_INT);
            $stmt->bindValue(':lsync', time(), PDO::PARAM_INT);
            $stmt->bindValue(':last_user_msg_time', $msg->time, PDO::PARAM_INT);
            $stmt->bindValue(':unanswered_chat',($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT ? 1 : 0), PDO::PARAM_INT);

            // Set last message ID
            if ($chat->last_msg_id < $msg->id) {
                $stmt->bindValue(':last_msg_id',$msg->id,PDO::PARAM_INT);
            } else {
                $stmt->bindValue(':last_msg_id',$chat->last_msg_id,PDO::PARAM_INT);
            }

            $stmt->execute();

            if ($chat->has_unread_messages == 1 && $chat->last_user_msg_time < (time() - 5)) {
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.unread_chat',array('chat' => & $chat));
            }

            // Assign to last message all the texts
            $msg->msg = trim(implode("\n", $messagesToStore));

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.addmsguser',array('chat' => & $chat, 'msg' => & $msg, 'init' => 'restapi'));
        } else {
            throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','You cannot send messages to this chat. Please refresh your browser.'));
        }

        $db->commit();
        echo erLhcoreClassChat::safe_json_encode(array('error' => $error, 'r' => $r, 'msg' => $msg->getState()));
        exit;

    } catch (Exception $e) {
        $db->rollback();
        http_response_code(400);
        echo erLhcoreClassChat::safe_json_encode(array('error' => true, 'r' => $e->getMessage()));
        exit;
    }

} else {
    http_response_code(400);
    $r = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter a message, max characters').' - '.(int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value;
    echo erLhcoreClassChat::safe_json_encode(array('error' => true, 'r' => $r));
    exit;
}



?>