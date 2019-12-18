<?php

erLhcoreClassRestAPIHandler::setHeaders();

if (isset($_POST['data'])) {
    $subscription = $_POST['data'];
} else {
    $subscription = json_decode(file_get_contents('php://input'),true)['data'];
}

if ((string)$Params['user_parameters_unordered']['hash'] != '' && $subscription != '') {

    list($chatID,$hash) = explode('_',$Params['user_parameters_unordered']['hash']);

    $chat = erLhcoreClassModelChat::fetch($chatID);

    if ($chat instanceof erLhcoreClassModelChat && $chat->hash == $hash) {

        $db = ezcDbInstance::get();

        try {
            $db->beginTransaction();

            $subscriberHash = md5($subscription);

            $notificationSubscriber = erLhcoreClassModelNotificationSubscriber::findOne(array('filter' => array('subscriber_hash' => $subscriberHash)));

            if ($Params['user_parameters_unordered']['action'] == 'unsub') {
                if ($notificationSubscriber instanceof erLhcoreClassModelNotificationSubscriber) {
                    $notificationSubscriber->removeThis();
                }
                exit;
            }

            if (!($notificationSubscriber instanceof erLhcoreClassModelNotificationSubscriber)) {
                $notificationSubscriber = new erLhcoreClassModelNotificationSubscriber();
                $notificationSubscriber->subscriber_hash = $subscriberHash;
                $notificationSubscriber->ctime = time();
            }

            $notificationSubscriber->chat_id = $chat->id;
            $notificationSubscriber->dep_id = $chat->dep_id;

            if (is_numeric($Params['user_parameters_unordered']['theme']) && $Params['user_parameters_unordered']['theme'] > 0) {
                $notificationSubscriber->theme_id = (int)$Params['user_parameters_unordered']['theme'];
            }

            if (!empty($Params['user_parameters_unordered']['vid'])){
                $onlineUser = erLhcoreClassModelChatOnlineUser::fetchByVid($Params['user_parameters_unordered']['vid']);
                if ($onlineUser instanceof erLhcoreClassModelChatOnlineUser) {
                    $notificationSubscriber->online_user_id = $onlineUser->id;
                }
            }

            // Inform user that he has subscribed to notifications
            $msg = new erLhcoreClassModelmsg();
            $msg->msg = "You have subscribed to new messages notifications!";
            $msg->chat_id = $chat->id;
            $msg->user_id = -2;
            $msg->time = time();
            $msg->name_support = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support');

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_msg_user_saved',array('msg' => & $msg, 'chat' => & $chat));

            erLhcoreClassChat::getSession()->save($msg);

            $stmt = $db->prepare('UPDATE lh_chat SET last_user_msg_time = :last_user_msg_time, lsync = :lsync, last_msg_id = :last_msg_id, has_unread_messages = :has_unread_messages, unanswered_chat = :unanswered_chat WHERE id = :id');
            $stmt->bindValue(':id', $chat->id, PDO::PARAM_INT);
            $stmt->bindValue(':has_unread_messages', ($chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT ? 0 : 1),PDO::PARAM_INT);
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

            // Finish saving subscription
            $detect = new Mobile_Detect;
            $notificationSubscriber->uagent = $detect->getUserAgent();
            $notificationSubscriber->device_type = ($detect->isMobile() ? ($detect->isTablet() ? 2 : 1) : 0);
            $notificationSubscriber->ip = erLhcoreClassIPDetect::getIP();

            $notificationSubscriber->utime = time();
            $notificationSubscriber->params = $subscription;
            $notificationSubscriber->saveThis();

            $db->commit();

        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }
}

exit;
?>