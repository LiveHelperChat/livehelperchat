<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

$condition = \LiveHelperChat\Models\Bot\Condition::fetch($Params['user_parameters']['id']);
$condition->removeThis();

erLhcoreClassModule::redirect('genericbot/conditions');
exit;

?>