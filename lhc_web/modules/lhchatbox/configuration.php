<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchatbox/configuration.tpl.php');

$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chatbox.configuration', array('tpl' => & $tpl));

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/list','Chatbox')));

?>