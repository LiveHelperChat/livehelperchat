<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/unreadchats.tpl.php');

$pages = new lhPaginator();
$pages->items_total = erLhcoreClassChat::getUnreadMessagesChatsCount();
$pages->translationContext = 'chat/unreadchats';
$pages->serverURL = erLhcoreClassDesign::baseurl('chat/unreadchats');
$pages->paginate();

$tpl->set('pages',$pages);
$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' =>erLhcoreClassDesign::baseurl('chat/lists'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closedchats','Chats lists')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closedchats','Unread chats')));


?>