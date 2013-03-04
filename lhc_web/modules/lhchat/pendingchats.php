<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/pendingchats.tpl.php');

$pages = new lhPaginator();
$pages->items_total = erLhcoreClassChat::getPendingChatsCount();
$pages->translationContext = 'chat/pendingchats';
$pages->serverURL = erLhcoreClassDesign::baseurl('chat/pendingchats');
$pages->paginate();

$tpl->set('pages',$pages);
$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' =>erLhcoreClassDesign::baseurl('chat/lists'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Chats lists')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Pending chats'))
);

?>