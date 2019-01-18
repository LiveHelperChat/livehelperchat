<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhabstract/list.tpl.php');

$objectClass = 'erLhAbstractModel'.$Params['user_parameters']['identifier'];
$objectData = new $objectClass;
$object_trans = $objectData->getModuleTranslations();

if (isset($object_trans['permission']) && !$currentUser->hasAccessTo($object_trans['permission']['module'],$object_trans['permission']['function'])) {
	erLhcoreClassModule::redirect();
	exit;
}

$append = '';
$filterParams['filter'] = array();
if ( isset($objectData->has_filter) &&  $objectData->has_filter === true ) {
	$filterParams = erLhcoreClassSearchHandler::getParams(array('module_file' => erLhAbstractModelNewspaper::FILTER_NAME, 'format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
	$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);
	$tpl->set('filter',erLhAbstractModelNewspaper::FILTER_NAME);
}

$filterObject = array();
if ( method_exists($objectData,'getFilter') ) {
	$filterObject = $objectData->getFilter();
}

$tpl->set('filterObject',$filterObject);
		
$pages = new lhPaginator();
$pages->items_total = call_user_func('erLhAbstractModel'.$Params['user_parameters']['identifier'].'::getCount',array_merge($filterParams['filter'],$filterObject));
$pages->translationContext = 'abstract/list';
$pages->serverURL = erLhcoreClassDesign::baseurl('abstract/list').'/'.$Params['user_parameters']['identifier'].$append;
$pages->setItemsPerPage(20);
$pages->paginate();

$tpl->set('pages',$pages);
$tpl->set('identifier',$Params['user_parameters']['identifier']);

$tpl->set('object_trans',$object_trans);
$tpl->set('fields',$objectData->getFields());
$tpl->set('filter_params',$filterParams['filter']);

if ( method_exists($objectData,'defaultSort') ) {
    $tpl->set('sort',$objectData->defaultSort());
}

if ($objectData->hide_add === true) {
    $tpl->set('hide_add',true);
}

if ($objectData->hide_delete === true) {
    $tpl->set('hide_delete',true);
}

$Result['content'] = $tpl->fetch();

if (isset($object_trans['path'])){
	$Result['path'][] =  $object_trans['path'];
	$Result['path'][] = array('title' => $object_trans['name']);	
} else {
	$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','System configuration')),
			array('title' => $object_trans['name'])
	);
};

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('abstract.list_'.strtolower($Params['user_parameters']['identifier']).'_path', array('result' => & $Result));
?>