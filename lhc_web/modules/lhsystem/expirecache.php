<?php


$CacheManager = erConfigClassLhCacheConfig::getInstance();
$CacheManager->expireCache();
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;

?>