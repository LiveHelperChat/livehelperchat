<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhdocshare/list.tpl.php');

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('docshare/list');
$pages->items_total = erLhcoreClassModelDocShare::getCount();
$pages->setItemsPerPage(10);
$pages->paginate();

$items = array();
if ($pages->items_total > 0) {
	$items = erLhcoreClassModelDocShare::getList(array('offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id DESC'));
}

$tpl->set('items',$items);
$tpl->set('pages',$pages);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('docshare/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('faq/list','Documents sharer')));

?>