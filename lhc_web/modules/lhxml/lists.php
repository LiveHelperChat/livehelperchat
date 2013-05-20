<?php

$currentUser = erLhcoreClassUser::instance();
if (!$currentUser->isLogged() && !$currentUser->authenticate($_POST['username'],$_POST['password']))
{
    exit;
}

$activeChats = erLhcoreClassChat::getActiveChats(10);
$closedChats = erLhcoreClassChat::getClosedChats(10);
$pendingChats = erLhcoreClassChat::getPendingChats(10);
$transferedChats = erLhcoreClassTransfer::getTransferChats();
$onlineUsers = erLhcoreClassModelChatOnlineUser::getList();

$columnsToHide = array('user_id','status','mail_send','dep_id','last_msg_id','hash','user_status','support_informed','support_informed','country_code','user_typing','operator_typing','has_unread_messages','last_user_msg_time','additional_data');
$columnsName = array('id' => 'ID','nick' => 'Nick','time' => 'Time','referrer' => 'Referrer','ip' => 'IP','country_name' => 'Country','email' => 'E-mail','name' => 'Department','phone' => 'Phone');

$onlineuserscolumnsToHide = array('vid','user_country_code','current_page', 'chat_id', 'operator_user_id', 'message_seen');
$onlineuserscolumnsNames = array('id' => 'ID','operator_message' => 'Operator message', 'ip' => 'IP','user_agent' => 'Browser','last_visit' => 'Last visit','first_visit' => 'First visit','user_country_name' => 'Country','pages_count' => 'Pages viewed');

echo json_encode(array(
'active_chats' => array('rows' => $activeChats, 'size' => count($activeChats), 'hidden_columns' => $columnsToHide, 'timestamp_delegate' => array('time'),'column_names' => $columnsName),
'online_users' => array('rows' => $onlineUsers, 'size' => count($onlineUsers), 'hidden_columns' => $onlineuserscolumnsToHide,'column_names' => $onlineuserscolumnsNames, 'timestamp_delegate' => array('last_visit','first_visit')),
'closed_chats' => array('rows' => $closedChats, 'size' => count($closedChats), 'hidden_columns' => $columnsToHide, 'timestamp_delegate' => array('time'),'column_names' => $columnsName),
'pending_chats' => array('rows' => $pendingChats, 'size' => count($pendingChats), 'hidden_columns' => $columnsToHide, 'timestamp_delegate' => array('time'),'column_names' => $columnsName),
'transfered_chats' => array('rows' => $transferedChats, 'size' => count($transferedChats), 'hidden_columns' => array_merge($columnsToHide,array('transfer_id')), 'timestamp_delegate' => array('time'),'column_names' => $columnsName),
));

$currentUser->updateLastVisit();
exit;
?>