<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhspeech/languages.tpl.php' );

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('speech/languages');
$pages->items_total = erLhcoreClassModelSpeechLanguage::getCount();
$pages->setItemsPerPage(20);
$pages->paginate();

$items = [];
if ($pages->items_total > 0) {
    $items = erLhcoreClassModelSpeechLanguage::getList(array_merge(['offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id ASC'],[]));
}

$tpl->set('items',$items);
$tpl->set('pages',$pages);

$Result['content'] = $tpl->fetch();
$Result['path'] = [
    ['url' => erLhcoreClassDesign::baseurl('system/configuration'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg', 'System configuration')],
    ['title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg', 'Languages')]
];
