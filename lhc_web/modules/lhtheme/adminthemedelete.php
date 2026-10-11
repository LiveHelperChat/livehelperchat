<?php

$currentUser = erLhcoreClassUser::instance();

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSRF Token');
    exit;
}

$form = erLhAbstractModelAdminTheme::fetch((int)$Params['user_parameters']['id']);
$form->removeThis();

header('Location: ' . erLhcoreClassChatValidator::getSafeReferer());
exit;

?>