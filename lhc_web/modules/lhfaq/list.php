<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhfaq/list.tpl.php');

$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('faq.list', ['tpl' => & $tpl]);

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('faq/list');
$pages->items_total = erLhcoreClassModelFaq::getCount();
$pages->setItemsPerPage(10);
$pages->paginate();

$items = [];
if ($pages->items_total > 0) {
	$items = erLhcoreClassModelFaq::getList(['offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id DESC']);
}

$tpl->set('items',$items);
$tpl->set('pages',$pages);

$Result['content'] = $tpl->fetch();
$Result['path'] = [['url' => erLhcoreClassDesign::baseurl('faq/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('faq/list','FAQ')]];
