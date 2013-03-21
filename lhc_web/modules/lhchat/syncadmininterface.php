<?php

$currentUser = erLhcoreClassUser::instance();
$canListOnlineUsers = false;

if (erLhcoreClassModelChatConfig::fetch('list_online_operators')->current_value == 1) {
	$canListOnlineUsers = $currentUser->hasAccessTo('lhuser','userlistonline');
}

$tpl = erLhcoreClassTemplate::getInstance();
$ReturnMessages = array();

/**
 * Active chats
 * */
$chats = erLhcoreClassChat::getActiveChats(10);
$tpl->set('chats',$chats);
$ReturnMessages[] = array('dom_id' => '#active-chat-list,#right-active-chats', 'content' => trim($tpl->fetch( 'lhchat/lists/activechats.tpl.php')));

/**
 * Closed chats
 * */
$tpl->set('chats',erLhcoreClassChat::getClosedChats(10));
$ReturnMessages[] = array('dom_id' => '#closed-chat-list', 'content' => trim($tpl->fetch( 'lhchat/lists/closedchats.tpl.php')));

/**
 * Pending chats
 * */
$pendingChats = erLhcoreClassChat::getPendingChats(10);
$tpl->set('chats',$pendingChats);

/**
 * Get last pending chat
 * */
$lastPendingChatID = 0;
if (!empty($pendingChats)){
    reset($pendingChats);
    $chatPending = current($pendingChats);
    $lastPendingChatID = $chatPending->id;
}

$ReturnMessages[] = array('dom_id' => '#right-pending-chats,#pending-chat-list', 'last_id_identifier' => 'pending_chat', 'last_id' => $lastPendingChatID, 'content' => trim($tpl->fetch('lhchat/lists/pendingchats.tpl.php')));

// Transfered chats
$tpl->set('transferchats',erLhcoreClassTransfer::getTransferChats());
$ReturnMessages[] = array('dom_id' => '#right-transfer-chats', 'content' => trim($tpl->fetch('lhchat/lists/transferedchats.tpl.php')));

if ($canListOnlineUsers == true) {
	$tpl->set('current_user',$currentUser);
	$ReturnMessages[] = array('dom_id' => '#online-operator-list', 'content' => trim($tpl->fetch('lhchat/lists/onlineoperators.tpl.php')));
}

// Unread chats
$unreadChats = erLhcoreClassChat::getUnreadMessagesChats();
$tpl->set('chats',$unreadChats);
$ReturnMessages[] = array('dom_id' => '#unread-chat-list,#right-unread-chats', 'content' => trim($tpl->fetch('lhchat/lists/unread-chat-list.tpl.php')));

// Update last visit
$currentUser->updateLastVisit();

echo json_encode(array('error' => 'false', 'result' => $ReturnMessages ));
exit;
?>