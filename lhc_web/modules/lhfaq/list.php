<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhfaq/list.tpl.php');

$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('faq.list', array('tpl' => & $tpl));

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('faq/list');
$pages->items_total = erLhcoreClassModelFaq::getCount();
$pages->setItemsPerPage(10);
$pages->paginate();

$items = array();
if ($pages->items_total > 0) {
	$items = erLhcoreClassModelFaq::getList(array('offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id DESC'));
}

$tpl->set('items',$items);
$tpl->set('pages',$pages);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('faq/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('faq/list','FAQ')));

?>