<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhgenericbot/listtranslations.tpl.php');

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'genericbot','module_file' => 'list_translations', 'format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'genericbot','module_file' => 'list_translations', 'format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = false;
}

erLhcoreClassChatStatistic::formatUserFilter($filterParams);

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

$pages = new lhPaginator();
$pages->items_total = erLhcoreClassModelGenericBotTrGroup::getCount($filterParams['filter']);
$pages->translationContext = 'chat/pendingchats';
$pages->serverURL = erLhcoreClassDesign::baseurl('genericbot/listtranslations').$append;
$pages->paginate();
$tpl->set('pages',$pages);

if ($pages->items_total > 0) {
    $items = erLhcoreClassModelGenericBotTrGroup::getList(array_merge($filterParams['filter'],array('limit' => $pages->items_per_page,'offset' => $pages->low)));
    $tpl->set('items',$items);
}

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('genericbot/listtranslations');
$tpl->set('input',$filterParams['input_form']);
$tpl->set('inputAppend',$append);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/new','System configuration')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Translations groups'))
);

?>
