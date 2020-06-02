<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhgroupchat/list.tpl.php');

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'group_chat_search','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'group_chat_search','format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = false;
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

$pages = new lhPaginator();
$pages->items_total = erLhcoreClassModelGroupChat::getCount($filterParams['filter']);
$pages->translationContext = 'groupchat/list';
$pages->serverURL = erLhcoreClassDesign::baseurl('groupchat/list').$append;
$pages->paginate();
$tpl->set('pages',$pages);

if ($pages->items_total > 0) {
    $items = erLhcoreClassModelGroupChat::getList(array_merge($filterParams['filter'],array('limit' => $pages->items_per_page,'offset' => $pages->low)));
    $tpl->set('items',$items);
}

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('groupchat/list');
$tpl->set('input',$filterParams['input_form']);
$tpl->set('inputAppend',$append);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('groupchat/group','System configuration')),
    array('url' =>erLhcoreClassDesign::baseurl('chat/list'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('groupchat/group','Group chats list'))
);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('group_chat.list_path',array('result' => & $Result));
?>
