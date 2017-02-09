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
        }
        
    } catch (Exception $e) {
        // Do nothing
    }

    erLhcoreClassModule::redirect('chat/cannedmsg');
    exit;
}

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('chat/cannedmsg');
$pages->items_total = erLhcoreClassModelCannedMsg::getCount($departmentParams);
$pages->setItemsPerPage(20);
$pages->paginate();

$items = array();
if ($pages->items_total > 0) {
    $items = erLhcoreClassModelCannedMsg::getList(array_merge(array('offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id ASC'),$departmentParams));
}

$tpl->set('items',$items);
$tpl->set('pages',$pages);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','System configuration')),
array('url' => erLhcoreClassDesign::baseurl('chat/cannedmsg'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Canned messages')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.cannedmsg_path',array('result' => & $Result));

?>