<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/pendingchats.tpl.php');

if (isset($_GET['doSearch'])) {
	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'chat_search','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
	$filterParams['is_search'] = true;
} else {
	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'chat_search','format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
	$filterParams['is_search'] = false;
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

$pages = new lhPaginator();
$pages->items_total = erLhcoreClassChat::getPendingChatsCount($filterParams['filter']);
$pages->translationContext = 'chat/pendingchats';
$pages->serverURL = erLhcoreClassDesign::baseurl('chat/pendingchats').$append;
$pages->paginate();
$tpl->set('pages',$pages);

if ($pages->items_total > 0) {
	$items = erLhcoreClassChat::getPendingChats($pages->items_per_page, $pages->low,$filterParams['filter']);
	$tpl->set('items',$items);
}
$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('chat/pendingchats');
$tpl->set('input',$filterParams['input_form']);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' =>erLhcoreClassDesign::baseurl('chat/lists'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Chats list')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Pending chats'))
);

?>