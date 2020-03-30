<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhfile/list.tpl.php');

if (isset($_GET['doSearch'])) {
	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'filelist','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
	$filterParams['is_search'] = true;
} else {
	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'filelist','format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
	$filterParams['is_search'] = false;
}

// I should see files only from users i can see.
// @todo this is partially wrong because i won't be able to see files uploaded from visitors.
$userFilterDefault = erLhcoreClassGroupUser::getConditionalUserFilter();

if (!empty($userFilterDefault)) {
    $filterParams['filter']['filterin']['user_id'] = $userFilterDefault['filterin']['id'];
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('file/list').$append;
$pages->items_total = erLhcoreClassModelChatFile::getCount($filterParams['filter']);
$pages->setItemsPerPage(20);
$pages->paginate();

$items = array();
if ($pages->items_total > 0) {
    $items = erLhcoreClassModelChatFile::getList(array_merge(array('offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id DESC'),$filterParams['filter']));
}

$tpl->set('items',$items);
$tpl->set('pages',$pages);

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('file/list');
$tpl->set('input',$filterParams['input_form']);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','System configuration')),
array('url' => erLhcoreClassDesign::baseurl('file/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','List of files')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.list_path', array('result' => & $Result));

?>