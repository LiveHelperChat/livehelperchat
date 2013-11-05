<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/blockedusers.tpl.php');

if (is_numeric($Params['user_parameters_unordered']['remove_block'])){
    try {

    	if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    		die('Invalid CSRF Token');
    		exit;
    	}

        $block = erLhcoreClassModelChatBlockedUser::fetch($Params['user_parameters_unordered']['remove_block']);
        $block->removeThis();
    } catch (Exception $e) {
        // Do nothing
    }
}

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('chat/blockedusers');
$pages->items_total = erLhcoreClassModelChatBlockedUser::getCount();
$pages->setItemsPerPage(20);
$pages->paginate();

$items = array();

if ($pages->items_total > 0) {
    $items = erLhcoreClassModelChatBlockedUser::getList(array('offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id ASC'));
}

$tpl->set('items',$items);
$tpl->set('pages',$pages);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','System configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Blocked users')))

?>