<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhpaidchat/expiredchat.tpl.php');

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'widget';
$Result['dynamic_height'] = true;

$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Chat expired')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('paidchat.expired_path',array('result' => & $Result));

?>