<?php

$currentUser = erLhcoreClassUser::instance();

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

$CacheManager = erConfigClassLhCacheConfig::getInstance();
$CacheManager->expireCache(true);
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;

?>