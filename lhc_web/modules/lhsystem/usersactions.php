<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhsystem/usersactions.tpl.php');

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'user','module_file' => 'user_list','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'user','module_file' => 'user_list','format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = false;
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

if (isset($_GET['ustats']) && is_numeric($_GET['ustats'])) {
    $user = erLhcoreClassModelUser::fetch($_GET['ustats']);
    
    if ($user instanceof erLhcoreClassModelUser) {
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.update_stats',array('user' => $user));
        erLhcoreClassChat::updateActiveChats((int)$_GET['ustats']);
    }
}

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('system/usersactions') . $append;
$pages->items_total = erLhcoreClassModelUser::getUserCount($filterParams['filter']);
$pages->setItemsPerPage(100);
$pages->paginate();

$userlist = erLhcoreClassModelUser::getUserList(array_merge($filterParams['filter'],array('offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'email ASC')));

$userStats = erLhcoreClassUserUtils::updateStats($userlist);

$tpl->set('userlist',$userlist);
$tpl->set('userlist_stats',$userStats);
$tpl->set('pages',$pages);
$tpl->set('currentUser',$currentUser);

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('system/usersactions');
$tpl->set('input',$filterParams['input_form']);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','System configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Users Actions')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.userlist_path',array('result' => & $Result));

?>