<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/unreadchats.tpl.php');

if (isset($_GET['doSearch'])) {
	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'chat_search','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
	$filterParams['is_search'] = true;
} else {
	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'chat_search','format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
	$filterParams['is_search'] = false;
}

if ($Params['user_parameters_unordered']['print'] == 1){
	$tpl = erLhcoreClassTemplate::getInstance('lhchat/printchats.tpl.php');
	$items = erLhcoreClassChat::getUnreadMessagesChats(10000,0,$filterParams['filter']);
	$tpl->set('items',$items);
	$Result['content'] = $tpl->fetch();
	$Result['pagelayout'] = 'popup';
	return;
}

if (in_array($Params['user_parameters_unordered']['xls'], array(1,2))) {
	erLhcoreClassChatExport::chatListExportXLS(erLhcoreClassChat::getUnreadMessagesChats(10000,0,$filterParams['filter']),array('type' => (int)$Params['user_parameters_unordered']['xls']));
	exit;
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

$pages = new lhPaginator();
$pages->items_total = erLhcoreClassChat::getUnreadMessagesChatsCount($filterParams['filter']);
$pages->translationContext = 'chat/unreadchats';
$pages->serverURL = erLhcoreClassDesign::baseurl('chat/unreadchats').$append;
$pages->paginate();
$tpl->set('pages',$pages);

if ($pages->items_total > 0) {
	$items = erLhcoreClassChat::getUnreadMessagesChats($pages->items_per_page, $pages->low,$filterParams['filter']);
	$tpl->set('items',$items);
}
$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('chat/unreadchats');
$tpl->set('input',$filterParams['input_form']);
$tpl->set('can_close_global',$currentUser->hasAccessTo('lhchat','allowcloseremote'));
$tpl->set('can_delete_global',$currentUser->hasAccessTo('lhchat','deleteglobalchat'));
$tpl->set('can_delete_general',$currentUser->hasAccessTo('lhchat','deletechat'));
$tpl->set('current_user_id',$currentUser->getUserID());

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' =>erLhcoreClassDesign::baseurl('chat/lists'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closedchats','Chats list')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/unreadchats','Unread chats list')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.unreadchats_path',array('result' => & $Result));
?>