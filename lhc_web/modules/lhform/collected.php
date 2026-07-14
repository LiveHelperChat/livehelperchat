<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhform/collected.tpl.php');

$form = erLhAbstractModelForm::fetch((int)$Params['user_parameters']['form_id']);

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'form','module_file' => 'collected', 'format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'form','module_file' => 'collected', 'format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = false;
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

$filter = $filterParams['filter'];
$filter['filter']['form_id'] = $form->id;

$departmentIds = $filterParams['input_form']->department_ids;
$userIds = $filterParams['input_form']->user_ids;

$needsJoin = !empty($departmentIds) || !empty($userIds);

if ($needsJoin) {
    $filter['leftjoin']['lh_chat'] = array('lh_chat.id', 'lh_abstract_form_collected.chat_id');
}

if (is_numeric($Params['user_parameters_unordered']['id']) && $Params['user_parameters_unordered']['action'] == 'delete'){

	// Delete selected canned message
	if ($currentUser->hasAccessTo('lhform', 'delete_collected')) {
		try {
			if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
				die('Invalid CSRF Token');
				exit;
			}
			$collected = erLhAbstractModelFormCollected::fetch((int)$Params['user_parameters_unordered']['id']);
			$collected->removeThis();
		} catch (Exception $e) {
			// Do nothing
		}
	}

	erLhcoreClassModule::redirect('form/collected','/'.$form->id);
	exit;
}

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('form/collected').'/'.$form->id . $append;
$pages->items_total = erLhAbstractModelFormCollected::getCount($filter);
$pages->setItemsPerPage(20);
$pages->paginate();

$items = array();
if ($pages->items_total > 0) {
	$items = erLhAbstractModelFormCollected::getList(array_merge($filter, array('offset' => $pages->low, 'limit' => $pages->items_per_page, 'sort' => 'id DESC')));
}

$tpl->set('items',$items);
$tpl->set('pages',$pages);

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('form/collected').'/'.$form->id;
$tpl->set('input',$filterParams['input_form']);
$tpl->set('inputAppend',$append);

$tpl->set('form',$form);
$Result['content'] = $tpl->fetch();

$object_trans = $form->getModuleTranslations();
$Result['path'][] =  $object_trans['path'];
$Result['path'][] = array('url' => erLhcoreClassDesign::baseurl('abstract/list').'/Form','title' => $object_trans['name']);
$Result['path'][] = array('title' => (string)$form);

?>