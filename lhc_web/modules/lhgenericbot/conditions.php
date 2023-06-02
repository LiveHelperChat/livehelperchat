<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhgenericbot/conditions/conditions.tpl.php');

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'genericbot','module_file' => 'conditions', 'format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'genericbot','module_file' => 'conditions', 'format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = false;
}

erLhcoreClassChatStatistic::formatUserFilter($filterParams);

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

$pages = new lhPaginator();
$pages->items_total = \LiveHelperChat\Models\Bot\Condition::getCount($filterParams['filter']);
$pages->translationContext = 'chat/pendingchats';
$pages->serverURL = erLhcoreClassDesign::baseurl('genericbot/conditions').$append;
$pages->paginate();
$tpl->set('pages',$pages);

if ($pages->items_total > 0) {
    $items = \LiveHelperChat\Models\Bot\Condition::getList(array_merge($filterParams['filter'],array('limit' => $pages->items_per_page,'offset' => $pages->low)));
    $tpl->set('items',$items);
}

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('genericbot/conditions');
$tpl->set('input',$filterParams['input_form']);
$tpl->set('inputAppend',$append);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/new','System configuration')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Conditions'))
);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('bot.list_conditions',array('result' => & $Result));
?>
