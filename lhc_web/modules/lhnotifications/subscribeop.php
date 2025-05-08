<?php

erLhcoreClassRestAPIHandler::setHeaders();

$subscription = file_get_contents('php://input');

$db = ezcDbInstance::get();

try {
    $db->beginTransaction();

    $subscriberHash = md5($subscription);

    $notificationSubscriber = \LiveHelperChat\Models\Notifications\OperatorSubscriber::findOne(array('filter' => array('subscriber_hash' => $subscriberHash)));

    if ($Params['user_parameters_unordered']['action'] == 'unsub') {
        if ($notificationSubscriber instanceof \LiveHelperChat\Models\Notifications\OperatorSubscriber) {
            $notificationSubscriber->removeThis();
        }
        exit;
    }

    if (!($notificationSubscriber instanceof \LiveHelperChat\Models\Notifications\OperatorSubscriber)) {
        $notificationSubscriber = new \LiveHelperChat\Models\Notifications\OperatorSubscriber();
        $notificationSubscriber->subscriber_hash = $subscriberHash;
        $notificationSubscriber->ctime = time();
    }

    // Finish saving subscription
    $detect = new Mobile_Detect;
    $notificationSubscriber->user_id = $currentUser->getUserID();
    $notificationSubscriber->uagent = mb_substr((string)$detect->getUserAgent(), 0, 250);
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

exit;
?>