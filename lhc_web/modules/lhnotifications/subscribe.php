<?php

header('content-type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

$subscription = $_POST['data'];

if ((string)$Params['user_parameters_unordered']['hash'] != '' && $subscription != '') {

    list($chatID,$hash) = explode('_',$Params['user_parameters_unordered']['hash']);

    $chat = erLhcoreClassModelChat::fetch($chatID);

    if ($chat instanceof erLhcoreClassModelChat && $chat->hash == $hash) {
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

        $detect = new Mobile_Detect;
        $notificationSubscriber->uagent = $detect->getUserAgent();
        $notificationSubscriber->device_type = ($detect->isMobile() ? ($detect->isTablet() ? 2 : 1) : 0);
        $notificationSubscriber->ip = erLhcoreClassIPDetect::getIP();

        $notificationSubscriber->utime = time();
        $notificationSubscriber->params = $subscription;
        $notificationSubscriber->saveThis();
    }
}

exit;
?>