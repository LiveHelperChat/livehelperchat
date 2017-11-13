<?php
header ( 'content-type: application/json; charset=utf-8' );
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

// Update last visit
$currentUser->updateLastVisit();

// We do not need a session anymore
session_write_close();

$userData = $currentUser->getUserData(true);

$ReturnMessages = array();

$pendingTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_pending_list',1);
$activeTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_active_list',1);
$closedTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_close_list',0);
$unreadTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_unread_list',1);
$showAllPending = erLhcoreClassModelUserSetting::getSetting('show_all_pending',1);
$showDepartmentsStats = $currentUser->hasAccessTo('lhuser','canseedepartmentstats');
$showDepartmentsStatsAll = $currentUser->hasAccessTo('lhuser','canseealldepartmentstats');
$myChatsEnabled = erLhcoreClassModelUserSetting::getSetting('enable_mchats_list',0);


$chatsList = array();
$chatsListAll = array();

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

$chatsForced = array();

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
    
    if (is_numeric($Params['user_parameters_unordered']['activeu'])) {
    	$filter['filter']['user_id'] = $Params['user_parameters_unordered']['activeu'];
    }    

    $sortArray = array(
        'op_asc' => 'user_id ASC',
        'op_dsc' => 'user_id DESC',
        'dep_asc' => 'dep_id ASC',
        'dep_dsc' => 'dep_id DESC',
        'id_asc' => 'id ASC',
        'id_dsc' => 'id DESC',
        'loc_dsc' => 'country_code DESC',
        'loc_asc' => 'country_code ASC',
        'u_dsc' => 'nick DESC',
        'u_asc' => 'nick ASC'
    );

    if (!empty($Params['user_parameters_unordered']['acs']) && key_exists($Params['user_parameters_unordered']['acs'], $sortArray)) {
        $filter['sort'] = $sortArray[$Params['user_parameters_unordered']['acs']];
    }
    
	$chats = erLhcoreClassChat::getActiveChats($limitList,0,$filter);

    $chatsListAll = $chatsListAll+$chats;

	// Collect chats which were transfered
	$lastTransferedForceId = 0;
	$transferedArray = array();
	foreach ($chats as $chat)
	{
	   if ($chat->status_sub_sub == erLhcoreClassModelChat::STATUS_SUB_SUB_TRANSFERED && $chat->user_id == $userData->id) {
	       $chat->status_sub_sub = erLhcoreClassModelChat::STATUS_SUB_SUB_DEFAULT;
	       $chatsForced[] = array(
	           'id' => $chat->id,
	           'nick' => $chat->nick,
	       );
	       $transferedArray[] = $chat->id;
	   }
	}

	if (!empty($transferedArray)) {
	    $db = ezcDbInstance::get();
	    $db->query('UPDATE `lh_chat` SET `status_sub_sub` = 0 WHERE `id` IN (' . implode(',', $transferedArray) . ')');
	}

	erLhcoreClassChat::prefillGetAttributes($chats,array('time_created_front','department_name','plain_user_name','product_name'),array('product_id','product','department','time','status','user_id','user'));
	$ReturnMessages['active_chats'] = array('list' => array_values($chats));	
	$chatsList[] = & $ReturnMessages['active_chats']['list'];
}



if ($myChatsEnabled == true) {
    /**
     * My chats chats
     * */
    $limitList = is_numeric($Params['user_parameters_unordered']['limitmc']) ? (int)$Params['user_parameters_unordered']['limitmc'] : 10;
    
    $filter = array('ignore_fields' => erLhcoreClassChat::$chatListIgnoreField);

    if (is_array($Params['user_parameters_unordered']['mcd']) && !empty($Params['user_parameters_unordered']['mcd'])) {
        erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['mcd']);
        $filter['filterin']['dep_id'] = $Params['user_parameters_unordered']['mcd'];
    }
    
    if (is_array($Params['user_parameters_unordered']['mcdprod']) && !empty($Params['user_parameters_unordered']['mcdprod'])) {
        erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['mcdprod']);
        $filter['filterin']['product_id'] = $Params['user_parameters_unordered']['mcdprod'];
    }

    $filter['filter']['user_id'] = (int)$currentUser->getUserID();
    
    $myChats = erLhcoreClassChat::getMyChats($limitList,0,$filter);

    $chatsListAll = $chatsListAll+$myChats;

    /**
     * Get last pending chat
     * */
    erLhcoreClassChat::prefillGetAttributes($myChats,array('time_created_front','product_name','department_name','wait_time_pending','wait_time_seconds','plain_user_name'), array('product_id','product','department','time','user'));
    
    $ReturnMessages['my_chats'] = array('list' => array_values($myChats));
    
    $chatsList[] = & $ReturnMessages['my_chats']['list'];
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

	$chatsListAll = $chatsListAll+$chats;

	erLhcoreClassChat::prefillGetAttributes($chats,array('time_created_front','department_name','plain_user_name','product_name'),array('product_id','product','department','time','status','user_id','user'));
	$ReturnMessages['closed_chats'] = array('list' => array_values($chats));
	
	$chatsList[] = & $ReturnMessages['closed_chats']['list'];
}

if ($pendingTabEnabled == true) {
	
	$additionalFilter = array('ignore_fields' => erLhcoreClassChat::$chatListIgnoreField);
	
	if (is_numeric($Params['user_parameters_unordered']['pendingu'])) {
		$additionalFilter['filter']['user_id'] = $Params['user_parameters_unordered']['pendingu'];
	} elseif ($showAllPending == 0) {
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

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.syncadmininterface.pendingchats',array('additional_filter' => & $additionalFilter));

	/**
	 * Pending chats
	 * */
	$pendingChats = erLhcoreClassChat::getPendingChats($limitList, 0, $additionalFilter, $filterAdditionalMainAttr);

    $chatsListAll = $chatsListAll+$pendingChats;

	/**
	 * Get last pending chat
	 * */
	erLhcoreClassChat::prefillGetAttributes($pendingChats,array('time_created_front','product_name','department_name','wait_time_pending','wait_time_seconds','plain_user_name'), array('product_id','product','department','time','status','user'));
	$ReturnMessages['pending_chats'] = array('list' => array_values($pendingChats), 'last_id_identifier' => 'pending_chat');

	$chatsList[] = & $ReturnMessages['pending_chats']['list'];
}

// Transfered chats
$transferchatsUser = erLhcoreClassTransfer::getTransferChats();

// How many chat's there is for operator assigned. Operators Chats
$operatorsCount = 0;

// What operators has send a messages
$operatorsSend = array();

if (!empty($transferchatsUser)) {    
    foreach ($transferchatsUser as & $transf) {

        if ($transf['status'] == erLhcoreClassModelChat::STATUS_OPERATORS_CHAT) {
            $operatorsCount++;
            $operatorsSend[] = (int)$transf['transfer_user_id'];
        }

    	$transf['time_front'] = date(erLhcoreClassModule::$dateDateHourFormat,$transf['time']);
    }
}

// Transfered chats to departments
$transferchatsDep = erLhcoreClassTransfer::getTransferChats(array('department_transfers' => true));
if (!empty($transferchatsDep)) {
	foreach ($transferchatsDep as & $transf){
		$transf['time_front'] = date(erLhcoreClassModule::$dateDateHourFormat,$transf['time']);
	}
}

$ReturnMessages['transfer_chats'] = array('list' => array_values($transferchatsUser),'last_id_identifier' => 'transfer_chat');
$ReturnMessages['transfer_dep_chats'] = array('list' => array_values($transferchatsDep),'last_id_identifier' => 'transfer_chat_dep');

if ($canListOnlineUsers == true || $canListOnlineUsersAll == true) {
    
    $filter = array();
    
    if (is_array($Params['user_parameters_unordered']['operatord']) && !empty($Params['user_parameters_unordered']['operatord'])) {
        erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['operatord']);
        $filter['customfilter'][] = '(dep_id = 0 OR dep_id IN ('.implode(",", $Params['user_parameters_unordered']['operatord']).'))';
    }
    
    $validSort = array(
        'onl_dsc' => 'hide_online DESC, active_chats DESC',
        'onl_asc' => 'hide_online ASC, active_chats DESC',
        'ac_dsc' => 'active_chats DESC, hide_online ASC',
        'ac_asc' => 'active_chats ASC, hide_online ASC',
    );

    if (key_exists($Params['user_parameters_unordered']['onop'], $validSort)) {
        $filter['sort'] = $validSort[$Params['user_parameters_unordered']['onop']];
    }
    
	$onlineOperators = erLhcoreClassModelUserDep::getOnlineOperators($currentUser,$canListOnlineUsersAll,$filter,is_numeric($Params['user_parameters_unordered']['limito']) ? (int)$Params['user_parameters_unordered']['limito'] : 10,$onlineTimeout);
	
	erLhcoreClassChat::prefillGetAttributes($onlineOperators,array('lastactivity_ago','offline_since','user_id','id','name_official','active_chats','departments_names','hide_online'),array(),array('remove_all' => true));
	
	$ReturnMessages['online_op'] = array('list' => array_values($onlineOperators), 'op_cc' => $operatorsCount, 'op_sn' => $operatorsSend);
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

    $chatsListAll = $chatsListAll+$unreadChats;

	erLhcoreClassChat::prefillGetAttributes($unreadChats, array('time_created_front','product_name','department_name','unread_time','plain_user_name'), array('product_id','product','department','time','status','user'));
	$ReturnMessages['unread_chats'] = array('last_id_identifier' => 'unread_chat', 'list' => array_values($unreadChats));
	
	$chatsList[] = & $ReturnMessages['unread_chats']['list'];
}

if (!empty($chatsList)) {
    erLhcoreClassChat::setOnlineStatus($chatsList, $chatsListAll);
}

$my_active_chats = array();

if ($activeTabEnabled == true && isset($Params['user_parameters_unordered']['topen']) && $Params['user_parameters_unordered']['topen'] == 'true') {
    $activeMyChats = erLhcoreClassChat::getActiveChats(10, 0, array('filter' => array('user_id' => $currentUser->getUserID())));

    $chatsListAll = $chatsListAll+$activeMyChats;

    erLhcoreClassChat::prefillGetAttributes($activeMyChats,array('id','nick'),array(),array('remove_all' => true));
    
    $my_active_chats = array_values($activeMyChats);
}

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.syncadmininterface',array('lists' => & $ReturnMessages));

$ou = '';
if ($userData->operation_admin != '') {
    $ou = $userData->operation_admin;
    $userData->operation_admin = '';
    erLhcoreClassUser::getSession()->update($userData);
}

$responseSync = array('error' => 'false', 'mac' => $my_active_chats, 'ou' => $ou, 'result' => $ReturnMessages );

if (!empty($chatsForced)) {
     $responseSync['fs'] = $chatsForced;
}

echo erLhcoreClassChat::safe_json_encode($responseSync);

exit;
?>