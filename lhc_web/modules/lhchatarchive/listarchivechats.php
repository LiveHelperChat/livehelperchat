<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchatarchive/listarchivechats.tpl.php');

$archive = erLhcoreClassModelChatArchiveRange::fetch($Params['user_parameters']['id']);

if (isset($_GET['doSearch'])) {
	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'chat_search','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
	$filterParams['is_search'] = true;
} else {
	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'chat_search','format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
	$filterParams['is_search'] = false;
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

// Set correct archive tables
$archive->setTables();

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('chatarchive/listarchivechats').'/'.$archive->id.$append;
$pages->items_total = erLhcoreClassModelChatArchive::getCount($filterParams['filter']);
$pages->setItemsPerPage(20);
$pages->paginate();

$items = array();
if ($pages->items_total > 0) {
	try {
    	$items = erLhcoreClassModelChatArchive::getList(array_merge(array('offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id ASC'),$filterParams['filter']));
	} catch (Exception $e) {
		print_r($e->getMessage());
	}
}

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('chatarchive/listarchivechats').'/'.$archive->id;
$tpl->set('input',$filterParams['input_form']);
$tpl->set('items',$items);
$tpl->set('archive',$archive);
$tpl->set('pages',$pages);

$Result['content'] = $tpl->fetch();


$Result['path'] = array(
		array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','System configuration')),
		array('url' => erLhcoreClassDesign::baseurl('chatarchive/archive'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/archive','Chat archive')),
		array('url' => erLhcoreClassDesign::baseurl('chatarchive/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Archives list')));
$Result['path'][] = array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Archived chats'));




?>