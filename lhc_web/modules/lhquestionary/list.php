<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhquestionary/list.tpl.php');

$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('questionary.list', array('tpl' => & $tpl));

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('questionary/list');
$pages->items_total = erLhcoreClassQuestionary::getCount();
$pages->setItemsPerPage(20);
$pages->paginate();

$items = array();
if ($pages->items_total > 0) {
    $items = erLhcoreClassQuestionary::getList(array('offset' => $pages->low, 'limit' => $pages->items_per_page));
}

$tpl->set('items',$items);
$tpl->set('pages',$pages);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('questionary/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/list','Questionary')))

?>