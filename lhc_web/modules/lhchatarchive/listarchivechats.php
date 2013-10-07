<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchatarchive/listarchivechats.tpl.php');

$archive = erLhcoreClassModelChatArchiveRange::fetch($Params['user_parameters']['id']);

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('chatarchive/listarchivechats').'/'.$archive->id;
$pages->items_total = $archive->chats_in_archive;
$pages->setItemsPerPage(20);
$pages->paginate();

$items = array();
if ($pages->items_total > 0) {
    $items = erLhcoreClassChat::getList(array('offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id ASC'),'erLhcoreClassModelChatArchive', erLhcoreClassModelChatArchiveRange::$archiveTable);
}

$tpl->set('items',$items);
$tpl->set('archive',$archive);
$tpl->set('pages',$pages);

$Result['content'] = $tpl->fetch();


$Result['path'] = array(
		array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','System configuration')),
		array('url' => erLhcoreClassDesign::baseurl('chatarchive/archive'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/archive','Chat archive')),
		array('url' => erLhcoreClassDesign::baseurl('chatarchive/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Archives list')));
$Result['path'][] = array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Archived chats'));




?>