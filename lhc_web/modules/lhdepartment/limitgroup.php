<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhdepartment/limitgroup.tpl.php');

$departmentParams = array();

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('department/limitgroup');
$pages->items_total = erLhcoreClassModelDepartamentLimitGroup::getCount($departmentParams);
$pages->setItemsPerPage(20);
$pages->paginate();

$items = array();
if ($pages->items_total > 0) {
    $items = erLhcoreClassModelDepartamentLimitGroup::getList(array_merge($departmentParams,array('offset' => $pages->low, 'limit' => $pages->items_per_page, 'sort' => 'id ASC')));
}

$tpl->set('items',$items);
$tpl->set('pages',$pages);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','System configuration')),
array('url' => erLhcoreClassDesign::baseurl('department/index'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Departments')),
array('url' => erLhcoreClassDesign::baseurl('department/departments'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Departments limit groups'))
)

?>