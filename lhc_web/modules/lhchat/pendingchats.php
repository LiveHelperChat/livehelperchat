<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/pendingchats.tpl.php');

if ( isset($_POST['doDelete']) ) {
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('chat/pendingchats');
		exit;
	}

	$definition = array(
			'ChatID' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'int', null, FILTER_REQUIRE_ARRAY
			),
	);

	$form = new ezcInputForm( INPUT_POST, $definition );
	$Errors = array();

	if ( $form->hasValidData( 'ChatID' ) ) {
		$chats = erLhcoreClassChat::getList(array('filterin' => array('id' => $form->ChatID)));
		foreach ($chats as $chatToDelete){
			CSCacheAPC::getMem()->removeFromArray('lhc_open_chats', $chatToDelete->id);
			if ($currentUser->hasAccessTo('lhchat','deleteglobalchat') || ($currentUser->hasAccessTo('lhchat','deletechat') && $chatToDelete->user_id == $currentUser->getUserID()))
			{
				$chatToDelete->removeThis();
			}
		}
	}
}

if (isset($_GET['doSearch'])) {
	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'chat_search','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
	$filterParams['is_search'] = true;
} else {
	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'chat_search','format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
	$filterParams['is_search'] = false;
}

if ($Params['user_parameters_unordered']['print'] == 1){
	$tpl = erLhcoreClassTemplate::getInstance('lhchat/printchats.tpl.php');
	$items = erLhcoreClassChat::getPendingChats(10000,0,$filterParams['filter']);
	$tpl->set('items',$items);
	$Result['content'] = $tpl->fetch();
	$Result['pagelayout'] = 'popup';
	return;
}

if (in_array($Params['user_parameters_unordered']['xls'], array(1,2))) {
	erLhcoreClassChatExport::chatListExportXLS(erLhcoreClassChat::getPendingChats(10000,0,$filterParams['filter']),array('type' => (int)$Params['user_parameters_unordered']['xls']));
	exit;
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
$tpl->set('inputAppend',$append);
$tpl->set('can_delete_global',$currentUser->hasAccessTo('lhchat','deleteglobalchat'));
$tpl->set('can_delete_general',$currentUser->hasAccessTo('lhchat','deletechat'));
$tpl->set('current_user_id',$currentUser->getUserID());

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' =>erLhcoreClassDesign::baseurl('chat/lists'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Chats list')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Pending chats'))
);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.pendingchats_path',array('result' => & $Result));
?>