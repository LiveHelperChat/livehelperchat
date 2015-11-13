<?php

$tpl = erLhcoreClassTemplate::getInstance('lhuser/grouplist.tpl.php');

$pages = new lhPaginator();
$pages->items_total = erLhcoreClassModelGroup::getCount();
$pages->setItemsPerPage(10);
$pages->serverURL = erLhcoreClassDesign::baseurl('user/grouplist');
$pages->paginate();

$tpl->set('pages',$pages);

if ($pages->items_total > 0) {
    $tpl->set('groups',erLhcoreClassModelGroup::getList(array('offset' => $pages->low, 'limit' => $pages->items_per_page )));
} else {
    $tpl->set('groups',array());
}

$tpl->set('currentUser',$currentUser);
$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/grouplist','System configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/grouplist','Groups'))
);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.grouplist_path', array('result' => & $Result));
?>