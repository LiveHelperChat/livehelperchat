<?php

$currentUser = erLhcoreClassUser::instance();

$onlineTimeout = (int)erLhcoreClassModelChatConfig::fetchCache('sync_sound_settings')->data['online_timeout'];
erLhcoreClassChat::$trackActivity = (int)erLhcoreClassModelChatConfig::fetchCache('track_activity')->current_value == 1;
erLhcoreClassChat::$trackTimeout = (int)erLhcoreClassModelChatConfig::fetchCache('checkstatus_timeout')->current_value;
erLhcoreClassChat::$onlineCondition = (int)erLhcoreClassModelChatConfig::fetchCache('online_if')->current_value;

$canListOnlineUsers = false;
$canListOnlineUsersAll = false;

if (erLhcoreClassModelChatConfig::fetchCache('list_online_operators')->current_value == 1) {
	$canListOnlineUsers = $currentUser->hasAccessTo('lhuser','userlistonline');
	$canListOnlineUsersAll = $currentUser->hasAccessTo('lhuser','userlistonlineall');
}

// We do not need a session anymore
session_write_close();

$ReturnMessages = array();

$pendingTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_pending_list',1);
$activeTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_active_list',1);
$closedTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_close_list',0);
$unreadTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_unread_list',1);
$showAllPending = erLhcoreClassModelUserSetting::getSetting('show_all_pending',1);
$showDepartmentsStats = $currentUser->hasAccessTo('lhuser','canseedepartmentstats');
$showDepartmentsStatsAll = $currentUser->hasAccessTo('lhuser','canseealldepartmentstats');

$chatsList = array();

if ($showDepartmentsStats == true) {
    /**
     * Departments stats
     * */
    $limitList = is_numeric($Params['user_parameters_unordered']['limitd']) ? (int)$Params['user_parameters_unordered']['limitd'] : 10;
    $filter = array('ignore_fields' => erLhcoreClassChat::$chatListIgnoreField);
    
    $filter['limit'] = $limitList;
    
    if (is_array($Params['user_parameters_unordered']['departmentd']) && !empty($Params['user_parameters_unordered']['departmentd'])) {
        erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['departmentd']);
        $filter['filterin']['id'] = $Params['user_parameters_unordered']['departmentd'];
    }
    
    // Add permission check if operator does not have permission to see all departments stats
    if ($showDepartmentsStatsAll === false) {
        $userData = $currentUser->getUserData(true);
        if ( $userData->all_departments == 0 )
        {
            $userDepartaments = erLhcoreClassUserDep::getUserDepartaments($currentUser->getUserID());
            if (!empty($userDepartaments)) {
                if ( isset( $filter['filterin']['id']) ) {
                    $validDepartments = array_intersect($userDepartaments, $filter['filterin']['id']);
                    if (!empty($validDepartments)) {
                        $filter['filterin']['id'] = $validDepartments;
                    } else {
                        $filter['filterin']['id'] = array(-1);
                    }
                } else {
                    $filter['filterin']['id'] = $userDepartaments;
                }
            } else {
                $filter['filterin']['id'] = array(-1); // No departments
            }
        }
    }
    
    $filter['sort'] = 'pending_chats_counter DESC';
    
    $departments = erLhcoreClassModelDepartament::getList($filter);
      
    erLhcoreClassChat::prefillGetAttributes($departments,array('id','name','pending_chats_counter','active_chats_counter'),array(),array('remove_all' => true));
    
    $ReturnMessages['departments_stats'] = array('list' => array_values($departments));
}


if ($activeTabEnabled == true) {
	/**
	 * Active chats
	 * */
    $limitList = is_numeric($Params['user_parameters_unordered']['limita']) ? (int)$Params['user_parameters_unordered']['limita'] : 10;
    $filter = array('ignore_fields' => erLhcoreClassChat::$chatListIgnoreField);
    
    if (is_array($Params['user_parameters_unordered']['actived']) && !empty($Params['user_parameters_unordered']['actived'])) {
        erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['actived']);
        $filter['filterin']['dep_id'] = $Params['user_parameters_unordered']['actived'];
    }

    if (is_array($Params['user_parameters_unordered']['activedprod']) && !empty($Params['user_parameters_unordered']['activedprod'])) {
        erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['activedprod']);
        $filter['filterin']['product_id'] = $Params['user_parameters_unordered']['activedprod'];
    }

	$chats = erLhcoreClassChat::getActiveChats($limitList,0,$filter);
	erLhcoreClassChat::prefillGetAttributes($chats,array('time_created_front','department_name','plain_user_name','product_name','can_view_chat'),array('product_id','product','department','time','status','user_id','user'));	
	$ReturnMessages['active_chats'] = array('list' => array_values($chats));	
	$chatsList[] = & $ReturnMessages['active_chats']['list'];
}

if ($closedTabEnabled == true) {

    $limitList = is_numeric($Params['user_parameters_unordered']['limitc']) ? (int)$Params['user_parameters_unordered']['limitc'] : 10;

    $filter = array('ignore_fields' => erLhcoreClassChat::$chatListIgnoreField);

    if (is_array($Params['user_parameters_unordered']['closedd']) && !empty($Params['user_parameters_unordered']['closedd'])) {
        erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['closedd']);
        $filter['filterin']['dep_id'] = $Params['user_parameters_unordered']['closedd'];
    }

    if (is_array($Params['user_parameters_unordered']['closeddprod']) && !empty($Params['user_parameters_unordered']['closeddprod'])) {
        erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['closeddprod']);
        $filter['filterin']['product_id'] = $Params['user_parameters_unordered']['closeddprod'];
    }

	/**
	 * Closed chats
	 * */
	$chats = erLhcoreClassChat::getClosedChats($limitList,0,$filter);
	erLhcoreClassChat::prefillGetAttributes($chats,array('time_created_front','department_name','plain_user_name','product_name'),array('product_id','product','department','time','status','user_id','user'));
	$ReturnMessages['closed_chats'] = array('list' => array_values($chats));
	
	$chatsList[] = & $ReturnMessages['closed_chats']['list'];
}

if ($pendingTabEnabled == true) {
	
	$additionalFilter = array('ignore_fields' => erLhcoreClassChat::$chatListIgnoreField);
	
	if ($showAllPending == 0) {
		$additionalFilter['filter']['user_id'] = $currentUser->getUserID();
	}
	
	if (is_array($Params['user_parameters_unordered']['pendingd']) && !empty($Params['user_parameters_unordered']['pendingd'])) {
	    erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['pendingd']);
	    $additionalFilter['filterin']['dep_id'] = $Params['user_parameters_unordered']['pendingd'];
	}
	
	if (is_array($Params['user_parameters_unordered']['pendingdprod']) && !empty($Params['user_parameters_unordered']['pendingdprod'])) {
	    erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['pendingdprod']);
	    $additionalFilter['filterin']['product_id'] = $Params['user_parameters_unordered']['pendingdprod'];
	}

	$limitList = is_numeric($Params['user_parameters_unordered']['limitp']) ? (int)$Params['user_parameters_unordered']['limitp'] : 10;

	$filterAdditionalMainAttr = array();
	if ($Params['user_parameters_unordered']['psort'] == 'asc') {
	    $filterAdditionalMainAttr['sort'] = 'priority DESC, id ASC';
	}

	/**
	 * Pending chats
	 * */
	$pendingChats = erLhcoreClassChat::getPendingChats($limitList, 0, $additionalFilter, $filterAdditionalMainAttr);

	/**
	 * Get last pending chat
	 * */
	$lastPendingChatID = 0;
	$lastChatNick = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Visitor');
	$lastMessage = erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','New chat request');
	if (!empty($pendingChats)) {
		$lastPendingChatID = max(array_keys($pendingChats));
		$chatRecent = reset($pendingChats);
		$lastChatNick = $chatRecent->nick.' | '.$chatRecent->department;
		$lastMessage = erLhcoreClassChat::getGetLastChatMessagePending($chatRecent->id);
	}

	erLhcoreClassChat::prefillGetAttributes($pendingChats,array('time_created_front','product_name','department_name','wait_time_pending','wait_time_seconds','plain_user_name'), array('product_id','product','department','time','status','user_id','user'));
	$ReturnMessages['pending_chats'] = array('list' => array_values($pendingChats),'nick' => $lastChatNick,'msg' => $lastMessage, 'last_id_identifier' => 'pending_chat', 'last_id' => $lastPendingChatID);

	$chatsList[] = & $ReturnMessages['pending_chats']['list'];
}

// Transfered chats
$transferchatsUser = erLhcoreClassTransfer::getTransferChats();
$lastPendingTransferID = 0;
if (!empty($transferchatsUser)){
	    reset($transferchatsUser);
	    $chatPending = current($transferchatsUser);
	    $lastPendingTransferID = $chatPending['transfer_id'];
	    
	    foreach ($transferchatsUser as & $transf){
	    	$transf['time_front'] = date(erLhcoreClassModule::$dateDateHourFormat,$transf['time']);
	    }
}

// Transfered chats to departments
$transferchatsDep = erLhcoreClassTransfer::getTransferChats(array('department_transfers' => true));
if (!empty($transferchatsDep)){
	reset($transferchatsDep);
	$chatPending = current($transferchatsDep);
	if ($chatPending['transfer_id'] > $lastPendingTransferID) {
		$lastPendingTransferID = $chatPending['transfer_id'];
	}
	foreach ($transferchatsDep as & $transf){
		$transf['time_front'] = date(erLhcoreClassModule::$dateDateHourFormat,$transf['time']);
	}
}

$ReturnMessages['transfer_chats'] = array('list' => array_values($transferchatsUser),'last_id_identifier' => 'transfer_chat','last_id' => $lastPendingTransferID);
$ReturnMessages['transfer_dep_chats'] = array('list' => array_values($transferchatsDep),'last_id_identifier' => 'transfer_chat','last_id' => $lastPendingTransferID);

if ($canListOnlineUsers == true || $canListOnlineUsersAll == true) {
    
    $filter = array();
    
    if (is_array($Params['user_parameters_unordered']['operatord']) && !empty($Params['user_parameters_unordered']['operatord'])) {
        erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['operatord']);
        $filter['customfilter'][] = '(dep_id = 0 OR dep_id IN ('.implode(",", $Params['user_parameters_unordered']['operatord']).'))';
    }
    
	$onlineOperators = erLhcoreClassModelUserDep::getOnlineOperators($currentUser,$canListOnlineUsersAll,$filter,is_numeric($Params['user_parameters_unordered']['limito']) ? (int)$Params['user_parameters_unordered']['limito'] : 10,$onlineTimeout);
	
	erLhcoreClassChat::prefillGetAttributes($onlineOperators,array('lastactivity_ago','user_id','id','name_support','active_chats','departments_names'),array(),array('remove_all' => true));
	
	$ReturnMessages['online_op'] = array('list' => array_values($onlineOperators));
}

if ($unreadTabEnabled == true) {

    $filter = array('ignore_fields' => erLhcoreClassChat::$chatListIgnoreField);

    $limitList = is_numeric($Params['user_parameters_unordered']['limitu']) ? (int)$Params['user_parameters_unordered']['limitu'] : 10;

    if (is_array($Params['user_parameters_unordered']['unreadd']) && !empty($Params['user_parameters_unordered']['unreadd'])) {
        erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['unreadd']);
        $filter['filterin']['dep_id'] = $Params['user_parameters_unordered']['unreadd'];
    }

    if (is_array($Params['user_parameters_unordered']['unreaddprod']) && !empty($Params['user_parameters_unordered']['unreaddprod'])) {
        erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['unreaddprod']);
        $filter['filterin']['product_id'] = $Params['user_parameters_unordered']['unreaddprod'];
    }

	// Unread chats
	$unreadChats = erLhcoreClassChat::getUnreadMessagesChats($limitList,0,$filter);

	$lastPendingChatID = 0;
	$lastChatNick = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Visitor');
	$lastMessage = erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','New unread message');	
	if (!empty($unreadChats)) {
		$lastPendingChatID = max(array_keys($unreadChats));
		$chatRecent = reset($unreadChats);
		$lastChatNick = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Unread message') . ' | ' .$chatRecent->nick . ' | ' . $chatRecent->department;
		$lastMessage = erLhcoreClassChat::getGetLastChatMessagePending($chatRecent->id);
	}
	
	erLhcoreClassChat::prefillGetAttributes($unreadChats,array('time_created_front','product_name','department_name','unread_time','plain_user_name'),array('product_id','product','department','time','status','user_id','user'));
	$ReturnMessages['unread_chats'] = array('msg' => $lastMessage, 'nick' => $lastChatNick, 'last_id' => $lastPendingChatID, 'last_id_identifier' => 'unread_chat', 'list' => array_values($unreadChats));
	
	$chatsList[] = & $ReturnMessages['unread_chats']['list'];
}

if (!empty($chatsList)) {
    erLhcoreClassChat::setOnlineStatus($chatsList);
}

// Update last visit
$currentUser->updateLastVisit();

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.syncadmininterface',array('lists' => & $ReturnMessages));

echo erLhcoreClassChat::safe_json_encode(array('error' => 'false', 'result' => $ReturnMessages ));

exit;
?>
