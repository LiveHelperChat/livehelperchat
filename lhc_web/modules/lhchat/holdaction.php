<?php

header('Content-type: application/json');

$db = ezcDbInstance::get();

$db->beginTransaction();

try {
    $chat = erLhcoreClassModelChat::fetchAndLock($Params['user_parameters']['chat_id']);

    if (!($chat instanceof erLhcoreClassModelChat)) {
        throw new Exception('Chat could not be found!');
    }

    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        throw new Exception('Invalid CSRF token!');
    }

    $msgStatus = '';

    if (in_array($chat->status_sub,array(erLhcoreClassModelChat::STATUS_SUB_SURVEY_COMPLETED, erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT, erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW, erLhcoreClassModelChat::STATUS_SUB_CONTACT_FORM))) {
        $tpl = erLhcoreClassTemplate::getInstance('lhchat/lists/assistance_message.tpl.php');
        $tpl->set('msg', array('msg' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Visitor has already left a chat. Hold can not be applied.'), 'time' => time()));
        $msgStatus = $tpl->fetch();
        echo json_encode(array('error' => false, 'hold' => false, 'msg' => $msgStatus));
        exit;
    }

    if (isset($_POST['sel']) && $_POST['sel'] == 'true') {

        $hold = false;
        $visitorHoldRemove = false;

        $chatVariables = $chat->chat_variables_array;

        if (isset($chatVariables['lhc_hldu'])) {
            unset($chatVariables['lhc_hldu']);
            $visitorHoldRemove = true;
            $chat->chat_variables = json_encode($chatVariables);
            $chat->chat_variables_array = $chatVariables;
        }

        $chat->status_sub = 0;
        $chat->last_op_msg_time = time();
        $chat->last_user_msg_time = time()-1;

        if (isset($_POST['op']) && $_POST['op'] == 'usr') {
            $chat->updateThis(array('update' => array('status_sub','last_op_msg_time','last_user_msg_time','chat_variables')));

            // Hold status change
            $tpl = erLhcoreClassTemplate::getInstance('lhchat/lists/assistance_message.tpl.php');
            $tpl->set('msg', array('msg' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Visitor hold removed!'), 'time' => time()));
            $msgStatus = $tpl->fetch();

        } else {

            $fields = array('status_sub','last_op_msg_time','last_user_msg_time');

            if ($visitorHoldRemove == true) {
                $fields[] = 'chat_variables';
            }

            $chat->updateThis(array('update' => $fields));

            if ($chat->auto_responder !== false) {
                $chat->auto_responder->active_send_status = 0;
                $chat->auto_responder->saveThis();
            }

            // Hold status change
            $tpl = erLhcoreClassTemplate::getInstance('lhchat/lists/assistance_message.tpl.php');
            $tpl->set('msg', array('msg' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Hold removed!'), 'time' => time()));
            $msgStatus = $tpl->fetch();
        }

    } else {

        $chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_ON_HOLD;
        $hold = true;

        if (isset($_POST['op']) && $_POST['op'] == 'usr') {

            $chatVariables = $chat->chat_variables_array;
            $chatVariables['lhc_hldu'] = 1;
            $chat->chat_variables = json_encode($chatVariables);
            $chat->chat_variables_array = $chatVariables;
            $chat->last_op_msg_time = time();
            $chat->last_user_msg_time = time()-1;
            $chat->updateThis(array('update' => array('status_sub','chat_variables','last_op_msg_time','last_user_msg_time')));

            $tpl = erLhcoreClassTemplate::getInstance('lhchat/lists/assistance_message.tpl.php');
            $tpl->set('msg', array('msg' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Visitor hold added!'), 'time' => time()));
            $msgStatus = $tpl->fetch();

        } else {
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.holdaction_defaultmsg',array('msg' => & $msgText, 'chat' => & $chat));

            $holdMessageSet = false;

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

                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_msg_admin_saved', array('msg' => & $msg, 'chat' => & $chat));

                    $msg->saveThis();

                    $chat->last_msg_id = $msg->id;

                    $holdMessageSet = true;
                }

                $chat->auto_responder->active_send_status = 0;
                $chat->auto_responder->saveThis();
            }

            if ($holdMessageSet === false) {
                $tpl = erLhcoreClassTemplate::getInstance('lhchat/lists/assistance_message.tpl.php');
                $tpl->set('msg', array('msg' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Hold added!'), 'time' => time()));
                $msgStatus = $tpl->fetch();
            }

            $chatVariables = $chat->chat_variables_array;

            $updateFields = array('last_msg_id','last_op_msg_time','last_user_msg_time','status_sub');

            if (isset($chatVariables['lhc_hldu'])) {
                unset($chatVariables['lhc_hldu']);
                $chat->chat_variables = json_encode($chatVariables);
                $chat->chat_variables_array = $chatVariables;
                $updateFields[] = 'chat_variables';
            }

            $chat->last_op_msg_time = time();
            $chat->last_user_msg_time = time()-1;
            $chat->updateThis(array('update' => $updateFields));
        }
    }

    $db->commit();

    echo json_encode(array('error' => false, 'hold' => $hold, 'msg' => $msgStatus));

} catch (Exception $e) {
    $db->rollback();
    echo json_encode(array('error' => true, 'msg' => $e->getMessage()));
}

exit;
?>