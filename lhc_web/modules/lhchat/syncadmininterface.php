<?php

$currentUser = erLhcoreClassUser::instance();

// We do not need a session anymore
session_write_close();

$canListOnlineUsers = false;
$canListOnlineUsersAll = false;

if (erLhcoreClassModelChatConfig::fetch('list_online_operators')->current_value == 1) {
	$canListOnlineUsers = $currentUser->hasAccessTo('lhuser','userlistonline');
	$canListOnlineUsersAll = $currentUser->hasAccessTo('lhuser','userlistonlineall');
}

$ReturnMessages = array();

$pendingTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_pending_list',1);
$activeTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_active_list',1);
$closedTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_close_list',0);
$unreadTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_unread_list',1);
$showAllPending = erLhcoreClassModelUserSetting::getSetting('show_all_pending',1);


if ($activeTabEnabled == true) {
	/**
	 * Active chats
	 * */
	$chats = erLhcoreClassChat::getActiveChats(10,0,array('ignore_fields' => erLhcoreClassChat::$chatListIgnoreField));
	erLhcoreClassChat::prefillGetAttributes($chats,array('time_created_front','department_name','user_name'),array('department','time','status','dep_id','user_id','user'));	
	$ReturnMessages['active_chats'] = array('list' => array_values($chats));
}

if ($closedTabEnabled == true) {
	/**
	 * Closed chats
	 * */
	$chats = erLhcoreClassChat::getClosedChats(10,0,array('ignore_fields' => erLhcoreClassChat::$chatListIgnoreField));
	erLhcoreClassChat::prefillGetAttributes($chats,array('time_created_front','department_name'),array('department','time','status','dep_id','user_id'));
	$ReturnMessages['closed_chats'] = array('list' => array_values($chats));
}

if ($pendingTabEnabled == true) {
	
	$additionalFilter = array('ignore_fields' => erLhcoreClassChat::$chatListIgnoreField);
	
	if ($showAllPending == 0) {
		$additionalFilter['filter']['user_id'] = $currentUser->getUserID();
	}
	/**
	 * Pending chats
	 * */
	$pendingChats = erLhcoreClassChat::getPendingChats(10,0,$additionalFilter);


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
	
	erLhcoreClassChat::prefillGetAttributes($pendingChats,array('time_created_front','department_name'),array('department','time','status','dep_id','user_id'));
	$ReturnMessages['pending_chats'] = array('list' => array_values($pendingChats),'nick' => $lastChatNick,'msg' => $lastMessage, 'last_id_identifier' => 'pending_chat', 'last_id' => $lastPendingChatID);
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
	$onlineOperators = erLhcoreClassModelUserDep::getOnlineOperators($currentUser,$canListOnlineUsersAll);
		
	foreach ($onlineOperators as & $onlineOperator) {
		erLhcoreClassChat::prefillGetAttributesObject($onlineOperator->user,array('lastactivity_ago'),array('username','password','email','filepath','filename','job_title','skype','xmpp_username'));
	}	
	
	$ReturnMessages['online_op'] = array('list' => array_values($onlineOperators));
}

if ($unreadTabEnabled == true) {
	// Unread chats
	$unreadChats = erLhcoreClassChat::getUnreadMessagesChats(10,0,array('ignore_fields' => erLhcoreClassChat::$chatListIgnoreField));

	erLhcoreClassChat::prefillGetAttributes($unreadChats,array('time_created_front','department_name','unread_time'),array('department','time','status','dep_id','user_id'));
	$ReturnMessages['unread_chats'] = array('list' => array_values($unreadChats));
}

// Update last visit
$currentUser->updateLastVisit();

echo json_encode(array('error' => 'false', 'result' => $ReturnMessages ));
exit;
?>