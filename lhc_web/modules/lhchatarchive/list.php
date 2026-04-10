<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchatarchive/list.tpl.php');

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('chatarchive/list');
$pages->items_total = erLhcoreClassChat::getCount([],'lh_chat_archive_range','count(id)');
$pages->setItemsPerPage(20);
$pages->paginate();

$items = [];
if ($pages->items_total > 0) {
    $items = erLhcoreClassChat::getList(['offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id ASC'],'erLhcoreClassModelChatArchiveRange','lh_chat_archive_range');
}

$tpl->set('items',$items);
$tpl->set('pages',$pages);

$Result['content'] = $tpl->fetch();

$Result['path'] = [
	['url' => erLhcoreClassDesign::baseurl('system/configuration'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'System configuration')],
	['url' => erLhcoreClassDesign::baseurl('chatarchive/archive'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/archive', 'Chat archive')]
];
$Result['path'][] = ['title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list', 'Archives list')];
