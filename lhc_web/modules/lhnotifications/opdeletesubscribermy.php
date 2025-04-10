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

$subscriber->removeThis();

erLhcoreClassModule::redirect('user/account','/(tab)/notifications');
exit;

?>