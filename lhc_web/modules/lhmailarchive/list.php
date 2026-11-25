<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhmailarchive/list.tpl.php');

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('chatarchive/list');
$pages->items_total = \LiveHelperChat\Models\mailConv\Archive\Range::getCount();
$pages->setItemsPerPage(20);
$pages->paginate();

$items = array();
if ($pages->items_total > 0) {
    $items = \LiveHelperChat\Models\mailConv\Archive\Range::getList(array('offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id ASC'));
}

$tpl->set('items',$items);
$tpl->set('pages',$pages);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
		array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','System configuration')),
		array('url' => erLhcoreClassDesign::baseurl('mailarchive/archive'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/archive','Mail archive')));
$Result['path'][] = array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Archives list'));

?>