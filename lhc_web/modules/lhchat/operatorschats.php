<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/operatorschats.tpl.php');


$pages = new lhPaginator();
$pages->items_total = erLhcoreClassChat::getOperatorsChatsCount();
$pages->translationContext = 'chat/closedchats';
$pages->serverURL = erLhcoreClassDesign::baseurl('chat/operatorschats');
$pages->paginate();

$tpl->set('pages',$pages);
$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' =>erLhcoreClassDesign::baseurl('chat/lists'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closedchats','Chats list')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/operatorschats','Operators chats')));


?>