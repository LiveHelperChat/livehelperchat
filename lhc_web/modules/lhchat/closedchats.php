<?php

$tpl = new erLhcoreClassTemplate( 'lhchat/closedchats.tpl.php');


$pages = new lhPaginator();
$pages->items_total = erLhcoreClassChat::getClosedChatsCount();
$pages->translationContext = 'chat/closedchats';
$pages->serverURL = erLhcoreClassDesign::baseurl('chat/closedchats');
$pages->paginate();

$tpl->set('pages',$pages);
$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' =>erLhcoreClassDesign::baseurl('chat/lists'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closedchats','Chats lists')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closedchats','Closed chats')));


?>