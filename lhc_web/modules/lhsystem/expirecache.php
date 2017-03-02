<?php


$CacheManager = erConfigClassLhCacheConfig::getInstance();
$CacheManager->expireCache(true);
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;

?>