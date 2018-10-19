<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

$subscriber = erLhcoreClassModelNotificationSubscriber::fetch($Params['user_parameters']['id']);
$subscriber->removeThis();

erLhcoreClassModule::redirect('notifications/list');
exit;

?>