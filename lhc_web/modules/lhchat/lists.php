<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/lists.tpl.php');

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists','Chat lists')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.lists_path',array('result' => & $Result));
?>