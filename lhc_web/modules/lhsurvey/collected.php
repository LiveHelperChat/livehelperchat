<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhsurvey/collected.tpl.php');

$survey = erLhAbstractModelSurvey::fetch((int)$Params['user_parameters']['survey_id']);

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'survey','module_file' => 'survey_search','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'survey','module_file' => 'survey_search','format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = false;
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

$filterParams['filter']['filter']['survey_id'] = $survey->id;

if ($Params['user_parameters_unordered']['print'] == 1) {
    $tpl = erLhcoreClassTemplate::getInstance('lhsurvey/printsurvey.tpl.php');
    $items = erLhAbstractModelSurveyItem::getList(array_merge($filterParams['filter'],array('offset' => 0, 'limit' => 100000)));        
    $tpl->set('items',$items);
    $Result['content'] = $tpl->fetch();
    $Result['pagelayout'] = 'print';
    return;
}

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('survey/collected').'/'.$survey->id . $append;
$pages->items_total = erLhAbstractModelSurveyItem::getCount($filterParams['filter']);
$pages->setItemsPerPage(20);
$pages->paginate();

$items = array();
if ($pages->items_total > 0) {
	$items = erLhAbstractModelSurveyItem::getList(array_merge($filterParams['filter'],array('offset' => $pages->low, 'limit' => $pages->items_per_page)));
}

$tpl->set('items',$items);
$tpl->set('pages',$pages);
$tpl->set('survey',$survey);

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('survey/collected') . '/' . $survey->id;

$tpl->set('input',$filterParams['input_form']);
$tpl->set('inputAppend',$append);

$Result['content'] = $tpl->fetch();

$object_trans = $survey->getModuleTranslations();

$Result['path'][] = array('url' => erLhcoreClassDesign::baseurl('abstract/list').'/Survey','title' => $object_trans['name']);
$Result['path'][] = array('title' => (string)$survey);

?>