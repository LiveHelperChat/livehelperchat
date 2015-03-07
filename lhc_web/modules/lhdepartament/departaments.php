<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhdepartament/departaments.tpl.php');

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

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('departament/departaments');
$pages->items_total = erLhcoreClassModelDepartament::getCount($departmentParams);
$pages->setItemsPerPage(20);
$pages->paginate();

$items = array();
if ($pages->items_total > 0) {
    $items = erLhcoreClassModelDepartament::getList(array_merge($departmentParams,array('offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id ASC')));
}

$tpl->set('items',$items);
$tpl->set('pages',$pages);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','System configuration')),
array('url' => erLhcoreClassDesign::baseurl('department/departments'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Departments')))

?>