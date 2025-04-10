<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

$subscriber = \LiveHelperChat\Models\Notifications\OperatorSubscriber::fetch($Params['user_parameters']['id']);

if ($subscriber->user_id != $currentUser->getUserID()) {
    die('You are not an owner of subscription');
    exit;
}

$report = erLhcoreClassNotifications::sendNotificationOpMessage($currentUser->getUserData()->name_official, $subscriber, ['ignore_active' => true]);

if (!$report->isSuccess()) {
    erLhcoreClassModule::redirect('user/account','/(tab)/notifications?notification=fail&notification_reason=' . urlencode($report->getReason()));
} else {
    erLhcoreClassModule::redirect('user/account','/(tab)/notifications?notification=success');
}

exit;

?>