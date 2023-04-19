<?php

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('abstract.list_'.strtolower($Params['user_parameters']['identifier']).'_general', array());

$tpl = erLhcoreClassTemplate::getInstance( 'lhabstract/list.tpl.php');

$objectClass = 'erLhAbstractModel'.$Params['user_parameters']['identifier'];

if (!class_exists($objectClass)) {
    $objectClass = '\LiveHelperChat\Models\Abstract\\'.$Params['user_parameters']['identifier'];
}

$objectData = new $objectClass;
$object_trans = $objectData->getModuleTranslations();

if (isset($object_trans['permission']) && !$currentUser->hasAccessTo($object_trans['permission']['module'],$object_trans['permission']['function'])) {
    erLhcoreClassModule::redirect();
    exit;
}

$append = '';
$filterParams['filter'] = array();

if ( isset($objectData->has_filter) &&  $objectData->has_filter === true ) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'abstract', 'module_file' => $objectData->filter_name, 'format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);
    $tpl->set('filter', $objectData->filter_name);
}

$filterObject = array();
if ( method_exists($objectData,'getFilter') ) {
	$filterObject = $objectData->getFilter();
}

$tpl->set('filterObject',$filterObject);

if (isset($filterParams['input_form'])) {
    $tpl->set('input_form',$filterParams['input_form']);
}

$filterParamsCount = array_merge($filterParams['filter'],$filterObject);

$rowsNumber = null;

$db = ezcDbInstance::get();

try {
    $db->query("SET SESSION wait_timeout=4");
} catch (Exception $e){
    //
}

try {
    $db->query("SET SESSION interactive_timeout=10");} catch (Exception $e){
} catch (Exception $e) {
    //
}

try {
    $db->query("SET SESSION innodb_lock_wait_timeout=10");
} catch (Exception $e) {
    //
}

try {
    $db->query("SET SESSION max_execution_time=10000;");
} catch (Exception $e) {
    //
}

try {
    $db->query("SET SESSION max_statement_time=10;");
} catch (Exception $e) {
    // Ignore we try to limit how long query can run
}

if (empty($filterParamsCount)) {
    $rowsNumber = method_exists($objectClass,'estimateRows') && ($rowsNumber = call_user_func($objectClass.'::estimateRows')) && $rowsNumber > 10000 ? $rowsNumber : null;
}

try {

    $tpl->set('object_trans',$object_trans);
    $tpl->set('identifier',$Params['user_parameters']['identifier']);

    $pages = new lhPaginator();
    $pages->items_total = is_numeric($rowsNumber) ? $rowsNumber : call_user_func($objectClass.'::getCount',$filterParamsCount);
    $pages->translationContext = 'abstract/list';
    $pages->serverURL = erLhcoreClassDesign::baseurl('abstract/list').'/'.$Params['user_parameters']['identifier'].$append;
    $pages->setItemsPerPage(20);
    $pages->paginate();

    $tpl->set('pages',$pages);
    $tpl->set('fields',$objectData->getFields());
    $tpl->set('filter_params',$filterParams['filter']);
    $tpl->set('object_class',$objectClass);
    
    if ( method_exists($objectData,'defaultSort') ) {
        $tpl->set('sort',$objectData->defaultSort());
    }

} catch (Exception $e) {
    $tpl->set('takes_to_long',erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) === true ? $e->getMessage() : true);
    $pages = new lhPaginator();
    $pages->items_total = 0;
    $pages->translationContext = 'chat/pendingchats';
    $pages->serverURL = erLhcoreClassDesign::baseurl('abstract/list').'/'.$Params['user_parameters']['identifier'].$append;
    $pages->paginate();
    $tpl->set('pages',$pages);
}

if ($objectData->hide_add === true) {
    $tpl->set('hide_add',true);
}

if ($objectData->hide_delete === true) {
    $tpl->set('hide_delete',true);
}

$Result['content'] = $tpl->fetch();

if (isset($object_trans['path'])){

    if (isset($object_trans['path']['url'])) {
        $Result['path'][] = $object_trans['path'];
    } else {
        $Result['path'] = $object_trans['path'];
    }

    $Result['path'][] = array('title' => $object_trans['name']);

} else {
	$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','System configuration')),
			array('title' => $object_trans['name'])
	);
};

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('abstract.list_'.strtolower($Params['user_parameters']['identifier']).'_path', array('result' => & $Result));
?>