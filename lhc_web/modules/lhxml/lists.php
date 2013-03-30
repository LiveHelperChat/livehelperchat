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

$columnsToHide = array('user_id','status','dep_id','hash','user_status','support_informed','support_informed','country_code','user_typing','operator_typing','has_unread_messages','last_user_msg_time');
$columnsName = array('id' => 'ID','nick' => 'Nick','time' => 'Time','referrer' => 'Referrer','ip' => 'IP','country_name' => 'Country','email' => 'E-mail','name' => 'Department','phone' => 'Phone');

echo json_encode(

array(

'active_chats' => array('rows' => $activeChats, 'size' => count($activeChats), 'hidden_columns' => $columnsToHide, 'timestamp_delegate' => array('time'),'column_names' => $columnsName),
'closed_chats' => array('rows' => $closedChats, 'size' => count($closedChats), 'hidden_columns' => $columnsToHide, 'timestamp_delegate' => array('time'),'column_names' => $columnsName),
'pending_chats' => array('rows' => $pendingChats, 'size' => count($pendingChats), 'hidden_columns' => $columnsToHide, 'timestamp_delegate' => array('time'),'column_names' => $columnsName),
'transfered_chats' => array('rows' => $transferedChats, 'size' => count($transferedChats), 'hidden_columns' => array_merge($columnsToHide,array('transfer_id')), 'timestamp_delegate' => array('time'),'column_names' => $columnsName),

));

$currentUser->updateLastVisit();
exit;
?>