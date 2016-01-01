<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhpaidchat/removedpaidchat.tpl.php');

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'widget';
$Result['dynamic_height'] = true;

$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Chat was removed')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('paidchat.removedpaidchat_path',array('result' => & $Result));

?>