<?php

$item = erLhcoreClassModelSpeechLanguageDialect::fetch($Params['user_parameters']['id']);

$currentUser = erLhcoreClassUser::instance();

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSRF Token');
    exit;
}

$item->removeThis();

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;


?>