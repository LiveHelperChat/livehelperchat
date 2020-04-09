<?php

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.cannedmsg', array());

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/cannedmsg.tpl.php');


/**
 * Append user departments filter
 * */
$departmentParams = array();
$userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID());
if ($userDepartments !== true){
    $departmentParams['filterin']['department_id'] = $userDepartments;

   if ($currentUser->hasAccessTo('lhcannedmsg','see_global')) {
       $departmentParams['filterin']['department_id'][] = 0;
   }
}

if (is_numeric($Params['user_parameters_unordered']['id']) && $Params['user_parameters_unordered']['action'] == 'delete') {
	
    // Delete selected canned message
    try {

    	if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    		die('Invalid CSRF Token');
    		exit;
    	}

        $Msg = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelCannedMsg', (int)$Params['user_parameters_unordered']['id']);        
        if ($userDepartments === true || in_array($Msg->department_id, $userDepartments)) {
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.cannedmsg_before_remove',array('msg' => & $Msg));
        	$Msg->removeThis();

            erLhcoreClassLog::logObjectChange(array(
                'object' => $Msg,
                'check_log' => true,
                'action' => 'Delete',
                'msg' => array(
                    'delete' => $Msg->getState(),
                    'user_id' => $currentUser->getUserID()
                )
            ));
        }
        
    } catch (Exception $e) {
        // Do nothing
    }

    erLhcoreClassModule::redirect('chat/cannedmsg');
    exit;
}

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'canned_search','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'canned_search','format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = false;
}

erLhcoreClassChatStatistic::formatUserFilter($filterParams);

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('chat/cannedmsg') . $append;
$pages->items_total = erLhcoreClassModelCannedMsg::getCount(array_merge_recursive($filterParams['filter'],$departmentParams));
$pages->setItemsPerPage(20);
$pages->paginate();

$items = array();
if ($pages->items_total > 0) {
    $items = erLhcoreClassModelCannedMsg::getList(array_merge_recursive($filterParams['filter'],array('offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id ASC'),$departmentParams));
}

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('chat/cannedmsg');

$tpl->set('items',$items);
$tpl->set('pages',$pages);
$tpl->set('input',$filterParams['input_form']);
$tpl->set('inputAppend',$append);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','System configuration')),
array('url' => erLhcoreClassDesign::baseurl('chat/cannedmsg'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Canned messages')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.cannedmsg_path',array('result' => & $Result));

?>