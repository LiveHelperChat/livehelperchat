<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchatsettings/eventlist.tpl.php');

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array(
        'module' => 'chat',
        'module_file' => 'event_list',
        'format_filter' => true,
        'use_override' => true,
        'uparams' => $Params['user_parameters_unordered']
    ));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array(
        'module' => 'chat',
        'module_file' => 'event_list',
        'format_filter' => true,
        'uparams' => $Params['user_parameters_unordered']
    ));
    $filterParams['is_search'] = false;
}

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('chatsettings/eventlist');
$pages->items_total = erLhcoreClassModelChatEventTrack::getCount($filterParams['filter']);
$pages->setItemsPerPage(20);
$pages->paginate();

$items = array();
if ($pages->items_total > 0) {
    $items = erLhcoreClassModelChatEventTrack::getList(array_merge($filterParams['filter'], array(
        'offset' => $pages->low,
        'limit' => $pages->items_per_page,
        'sort' => 'id ASC'
    )));
}

$tpl->set('items', $items);
$tpl->set('pages', $pages);

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('chatsettings/eventlist');

$tpl->set('input', $filterParams['input_form']);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array(
        'url' => erLhcoreClassDesign::baseurl('system/configuration'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'System configuration')
    ),
    array(
        'url' => erLhcoreClassDesign::baseurl('chatsettings/eventindex'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Events tracking')
    ),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Events tracking by department')
    )
);
?>