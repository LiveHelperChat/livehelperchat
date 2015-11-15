<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhfile/list.tpl.php');

if (isset($_GET['doSearch'])) {
	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'filelist','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
	$filterParams['is_search'] = true;
} else {
	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'filelist','format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
	$filterParams['is_search'] = false;
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('file/list').$append;
$pages->items_total = erLhcoreClassChat::getCount($filterParams['filter'],'lh_chat_file');
$pages->setItemsPerPage(20);
$pages->paginate();

$items = array();
if ($pages->items_total > 0) {
    $items = erLhcoreClassChat::getList(array_merge(array('offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id DESC'),$filterParams['filter']),'erLhcoreClassModelChatFile','lh_chat_file');
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