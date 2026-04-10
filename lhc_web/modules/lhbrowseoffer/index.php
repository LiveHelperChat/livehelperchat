<?php

$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('browseoffer.index', []);

$tpl = erLhcoreClassTemplate::getInstance( 'lhbrowseoffer/index.tpl.php');

$Result['content'] = $tpl->fetch();
$Result['path'] = [['title' => erTranslationClassLhTranslation::getInstance()->getTranslation('browseoffer/index','Browse offers')]];
