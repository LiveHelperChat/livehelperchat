<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

$subscriber = \LiveHelperChat\Models\Notifications\OperatorSubscriber::fetch($Params['user_parameters']['id']);
$subscriber->removeThis();

erLhcoreClassModule::redirect('notifications/oplist');
exit;

?>