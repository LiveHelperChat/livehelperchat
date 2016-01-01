<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhpaidchat/invalidhash.tpl.php');

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'widget';
$Result['dynamic_height'] = true;

$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Invalid hash')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('paidchat.expired_path',array('result' => & $Result));

?>