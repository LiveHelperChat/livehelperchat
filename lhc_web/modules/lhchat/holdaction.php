<?php

$db = ezcDbInstance::get();

$db->beginTransaction();
try {
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

    $msgStatus = '';

    if ($chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_ON_HOLD) {

        $hold = false;

        $chat->status_sub = 0;
        $chat->last_op_msg_time = time();
        $chat->last_user_msg_time = time()-1;
        $chat->saveThis();

        if ($chat->auto_responder !== false) {
            $chat->auto_responder->active_send_status = 0;
            $chat->auto_responder->saveThis();
        }

        // Hold status change
        $tpl = erLhcoreClassTemplate::getInstance('lhchat/lists/assistance_message.tpl.php');
        $tpl->set('msg', array('msg' =>  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Hold removed!'), 'time' => time()));
        $msgStatus = $tpl->fetch();

    } else {
        $chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_ON_HOLD;
        $hold = true;

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.holdaction_defaultmsg',array('msg' => & $msgText, 'chat' => & $chat));

        if ($chat->auto_responder !== false) {
            if ($chat->auto_responder->auto_responder !== false && $chat->auto_responder->auto_responder->wait_timeout_hold != '') {
                $msgText = $chat->auto_responder->auto_responder->wait_timeout_hold;

                $currentUser = erLhcoreClassUser::instance();
                $userData = $currentUser->getUserData();

                $msg = new erLhcoreClassModelmsg();
                $msg->msg = $msgText;
                $msg->chat_id = $chat->id;
                $msg->user_id = $currentUser->getUserID();
                $msg->time = time();
                $msg->name_support = $userData->name_support;
                $msg->saveThis();

                $chat->last_msg_id = $msg->id;
            }

            $chat->auto_responder->active_send_status = 0;
            $chat->auto_responder->saveThis();
        }

        $chat->last_op_msg_time = time();
        $chat->last_user_msg_time = time()-1;
        $chat->saveThis();
    }

    echo json_encode(array('error' => false, 'hold' => $hold, 'msg' => $msgStatus));

    $db->commit();
} catch (Exception $e) {
    $db->rollback();
    echo json_encode(array('error' => true, 'msg' => $e->getMessage()));
}

exit;
?>