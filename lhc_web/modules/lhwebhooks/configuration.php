<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhwebhooks/configuration.tpl.php');

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'webhooks', 'format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'webhooks', 'format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = false;
}

erLhcoreClassChatStatistic::formatUserFilter($filterParams);

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

$pages = new lhPaginator();
$pages->items_total = erLhcoreClassModelChatWebhook::getCount($filterParams['filter']);
$pages->translationContext = 'chat/pendingchats';
$pages->serverURL = erLhcoreClassDesign::baseurl('webhooks/configuration').$append;
$pages->paginate();
$tpl->set('pages',$pages);

if ($pages->items_total > 0) {
    $items = erLhcoreClassModelChatWebhook::getList(array_merge($filterParams['filter'],array('limit' => $pages->items_per_page,'offset' => $pages->low)));
    $tpl->set('items',$items);
}

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('webhooks/configuration');
$tpl->set('input',$filterParams['input_form']);
$tpl->set('inputAppend',$append);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('notifications/index'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module', 'Webhooks'))
);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('webhooks.list_path',array('result' => & $Result));
?>
