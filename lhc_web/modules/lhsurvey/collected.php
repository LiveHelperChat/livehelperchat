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

$filterSearch = $filterParams['filter'];

if ($filterParams['input_form']->group_results == true) {
    $filterSearch['group'] = 'user_id';
    $filterSearch['sort'] = 'total_stars DESC';
    $filterSearch['select_columns'] = array('count(id) as chats_number','SUM(max_stars_1) as total_stars');
    
    if (is_numeric($filterParams['input_form']->minimum_chats) && $filterParams['input_form']->minimum_chats > 0) {
        $filterSearch['having'] = 'count(id) > ' . (int)$filterParams['input_form']->minimum_chats ;
    }
}

if ($Params['user_parameters_unordered']['xls'] == 1) {
    erLhcoreClassSurveyExporter::exportXLS(erLhAbstractModelSurveyItem::getList(array_merge($filterSearch,array('offset' => 0, 'limit' => 100000))));
	exit;
}

if ($Params['user_parameters_unordered']['print'] == 1) {
    $tpl = erLhcoreClassTemplate::getInstance('lhsurvey/printsurvey.tpl.php');
    $items = erLhAbstractModelSurveyItem::getList(array_merge($filterSearch,array('offset' => 0, 'limit' => 100000)));        
    $tpl->set('items',$items);
    $tpl->set('survey',$survey);
    $Result['content'] = $tpl->fetch();
    $Result['pagelayout'] = 'print';
    return;
}

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('survey/collected') . '/' . $survey->id . $append;

if ($filterParams['input_form']->group_results == true) {
   $filtercount = $filterSearch;
   unset($filtercount['group']);
   $pages->items_total = erLhAbstractModelSurveyItem::getCount($filterSearch,false, false, 'count(distinct user_id)');
} else {
   $pages->items_total = erLhAbstractModelSurveyItem::getCount($filterSearch);
}
 
$pages->setItemsPerPage(20);
$pages->paginate();

$items = array();
if ($pages->items_total > 0) {
	$items = erLhAbstractModelSurveyItem::getList(array_merge($filterSearch,array('offset' => $pages->low, 'limit' => $pages->items_per_page)));
}


$tpl->set('items',$items);
$tpl->set('pages',$pages);
$tpl->set('survey',$survey);
$tpl->set('tab','');

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('survey/collected') . '/' . $survey->id;

$tpl->set('input',$filterParams['input_form']);
$tpl->set('inputAppend',$append);

$Result['content'] = $tpl->fetch();
$Result['additional_header_js'] = '<script type="text/javascript" src="'.erLhcoreClassDesign::design('js/Chart.bundle.min.js').'"></script>';

$object_trans = $survey->getModuleTranslations();

$Result['path'][] = array('url' => erLhcoreClassDesign::baseurl('abstract/list').'/Survey','title' => $object_trans['name']);
$Result['path'][] = array('title' => (string)$survey);

?>