<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhdepartment/group.tpl.php');

$departmentParams = [];

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('department/group');
$pages->items_total = erLhcoreClassModelDepartamentGroup::getCount($departmentParams);
$pages->setItemsPerPage(20);
$pages->paginate();

$items = [];
if ($pages->items_total > 0) {
    $items = erLhcoreClassModelDepartamentGroup::getList(array_merge($departmentParams, ['offset' => $pages->low, 'limit' => $pages->items_per_page, 'sort' => 'id ASC']));
}

$tpl->set('items',$items);
$tpl->set('pages',$pages);

$Result['content'] = $tpl->fetch();
$Result['path'] = [
    ['url' => erLhcoreClassDesign::baseurl('system/configuration'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'System configuration')],
    ['url' => erLhcoreClassDesign::baseurl('department/index'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Departments')],
    ['url' => erLhcoreClassDesign::baseurl('department/departments'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Departments groups')]
];
