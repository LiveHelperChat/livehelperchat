<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhuser/userlist.tpl.php');

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'user','module_file' => 'user_list','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'user','module_file' => 'user_list','format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = false;
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('user/userlist') . $append;
$pages->items_total = erLhcoreClassModelUser::getUserCount($filterParams['filter']);
$pages->setItemsPerPage(20);
$pages->paginate();

$userlist = erLhcoreClassModelUser::getUserList(array_merge($filterParams['filter'],array('offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'email ASC')));

$tpl->set('userlist',$userlist);
$tpl->set('pages',$pages);
$tpl->set('currentUser',$currentUser);

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('user/userlist');
$tpl->set('input',$filterParams['input_form']);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','System configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Users')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.userlist_path',array('result' => & $Result));

?>