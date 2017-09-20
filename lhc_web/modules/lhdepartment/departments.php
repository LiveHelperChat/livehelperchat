<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhdepartment/departments.tpl.php');

/**
 * Append user departments filter
 * */
$departmentParams = array();
if ($currentUser->hasAccessTo('lhdepartment','manageall') !== true)
{
    $userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID());
    if ($userDepartments !== true){
    	$departmentParams['filterin']['id'] = $userDepartments;
    }
}

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'departament','module_file' => 'dep_list','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'departament','module_file' => 'dep_list','format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = false;
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('department/departments') . $append;
$pages->items_total = erLhcoreClassModelDepartament::getCount(array_merge($filterParams['filter'],$departmentParams));
$pages->setItemsPerPage(20);
$pages->paginate();

$items = array();
if ($pages->items_total > 0) {
    $items = erLhcoreClassModelDepartament::getList(array_merge($filterParams['filter'],$departmentParams,array('offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id ASC')));
}

$tpl->set('items',$items);
$tpl->set('pages',$pages);

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('department/departments');
$tpl->set('input',$filterParams['input_form']);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','System configuration')),
array('url' => erLhcoreClassDesign::baseurl('department/departments'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Departments')))

?>