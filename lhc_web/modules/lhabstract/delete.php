<?php

$ObjectData = erLhcoreClassAbstract::getSession()->load( 'erLhAbstractModel'.$Params['user_parameters']['identifier'], (int)$Params['user_parameters']['object_id'] );
$ObjectData->removeThis();

$cache = CSCacheAPC::getMem();
$cache->increaseCacheVersion('site_attributes_version');

erLhcoreClassModule::redirect('abstract/list','/'.$Params['user_parameters']['identifier']);
exit;

?>