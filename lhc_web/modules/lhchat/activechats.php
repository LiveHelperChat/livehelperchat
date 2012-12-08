<?php

$tpl = new erLhcoreClassTemplate('lhchat/activechats.tpl.php');

$pages = new lhPaginator();
$pages->items_total = erLhcoreClassChat::getActiveChatsCount();
$pages->translationContext = 'chat/activechats';
$pages->paginate();

$tpl->set('pages',$pages);
$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' =>erLhcoreClassDesign::baseurl('chat/lists'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Chats lists')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Active chats'))
);

?>