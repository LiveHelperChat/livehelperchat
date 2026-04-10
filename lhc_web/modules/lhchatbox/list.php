<?php

$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chatbox.list', []);

$tpl = erLhcoreClassTemplate::getInstance('lhchatbox/list.tpl.php');

$pages = new lhPaginator();
$pages->items_total = erLhcoreClassChatbox::getCount();
$pages->serverURL = erLhcoreClassDesign::baseurl('chatbox/list');
$pages->paginate();

$tpl->set('pages',$pages);
$Result['content'] = $tpl->fetch();

$Result['path'] = [
    ['url' => erLhcoreClassDesign::baseurl('chatbox/configuration'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/configuration', 'Chatbox')],
    ['title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/list', 'Chatbox list')]
];
