<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhuser/userlist.tpl.php');

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('user/userlist');
$pages->items_total = erLhcoreClassModelUser::getUserCount();
$pages->setItemsPerPage(20);
$pages->paginate();

$userlist = erLhcoreClassModelUser::getUserList(array('offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'email ASC'));

$tpl->set('userlist',$userlist);
$tpl->set('pages',$pages);
$tpl->set('currentUser',$currentUser);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','System configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Users')));

?>