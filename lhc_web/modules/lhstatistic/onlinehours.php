<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhstatistic/onlinehours.tpl.php');

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'onlinehours_list','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'onlinehours_list','format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = false;
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

if ($Params['user_parameters_unordered']['export'] == 1) {
    $tpl = erLhcoreClassTemplate::getInstance('lhstatistic/onlinehours_report.tpl.php');
    $tpl->set('items',erLhcoreClassModelUserOnlineSession::getList(array_merge($filterParams['filter'],array('offset' => 0, 'limit' => 1000, 'sort' => 'id ASC'))));
    $tpl->set('filter',$filterParams['filter']);
    echo $tpl->fetch();
    exit;
}

$rowsNumber = null;

if (empty($filterParams['filter'])) {
    $rowsNumber = ($rowsNumber = erLhcoreClassModelUserOnlineSession::estimateRows()) && $rowsNumber > 100 ? $rowsNumber : null;
}

$pages = new lhPaginator();
$pages->items_total = is_numeric($rowsNumber) ? $rowsNumber : erLhcoreClassModelUserOnlineSession::getCount($filterParams['filter']);
$pages->serverURL = erLhcoreClassDesign::baseurl('statistic/onlinehours') . $append;
$pages->setItemsPerPage(20);
$pages->paginate();

$userlist = erLhcoreClassModelUserOnlineSession::getList(array_merge($filterParams['filter'],array('offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id DESC')));

erLhcoreClassModelUserOnlineSession::setChatsBySessions($userlist, $filterParams['filter']);

$tpl->set('items',$userlist);
$tpl->set('pages',$pages);
$tpl->set('currentUser',$currentUser);

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('statistic/onlinehours');
$tpl->set('input',$filterParams['input_form']);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','System configuration')),
array('url' => erLhcoreClassDesign::baseurl('statistic/statistic'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Statistic')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/onlinehours','Online Hours')));

?>