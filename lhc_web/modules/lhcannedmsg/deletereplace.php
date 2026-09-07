<?php

$item = erLhcoreClassModelCannedMsgReplace::fetch($Params['user_parameters']['id']);

$currentUser = erLhcoreClassUser::instance();

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSRF Token');
    exit;
}

if (!empty($item->configuration_array['restricted_access']) && !erLhcoreClassUser::instance()->hasAccessTo('lhcannedmsg','use_replace_sensitive')) {
    erLhcoreClassModule::redirect('cannedmsg/listreplace');
    exit;
}

$item->removeThis();

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;

?>