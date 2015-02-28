<?php

$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('browseoffer.index', array());

if ($response === erLhcoreClassChatEventDispatcher::STOP_WORKFLOW) {
    return;
}

$tpl = erLhcoreClassTemplate::getInstance( 'lhbrowseoffer/index.tpl.php');

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('browseoffer/index','Browse offers')));

?>