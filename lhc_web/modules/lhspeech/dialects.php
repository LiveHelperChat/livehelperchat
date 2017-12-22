<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhspeech/dialects.tpl.php' );

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('speech/dialects');
$pages->items_total = erLhcoreClassModelSpeechLanguageDialect::getCount();
$pages->setItemsPerPage(20);
$pages->paginate();

$items = array();
if ($pages->items_total > 0) {
    $items = erLhcoreClassModelSpeechLanguageDialect::getList(array_merge(array('offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id ASC'),array()));
}

$tpl->set('items',$items);
$tpl->set('pages',$pages);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','System configuration')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Dialects')));

?>