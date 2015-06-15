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
$unreadChats = erLhcoreClassChat::getUnreadMessagesChats(10,0);

erLhcoreClassChat::prefillGetAttributes($activeChats,array('department_name'),array('updateIgnoreColumns','department'));
erLhcoreClassChat::prefillGetAttributes($closedChats,array('department_name'),array('updateIgnoreColumns','department'));
erLhcoreClassChat::prefillGetAttributes($pendingChats,array('department_name'),array('updateIgnoreColumns','department'));
erLhcoreClassChat::prefillGetAttributes($unreadChats,array('department_name'),array('updateIgnoreColumns','department'));

$onlineUsers = array();
if ($currentUser->hasAccessTo('lhchat','use_onlineusers')) {
    
    $filter = array('offset' => 0, 'limit' => 50, 'sort' => 'last_visit DESC','filtergt' => array('last_visit' => (time()-3600)));
    
    $departmentParams = array();
    $userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID());
    if ($userDepartments !== true) {
        $departmentParams['filterin']['id'] = $userDepartments;
        if (!$currentUser->hasAccessTo('lhchat','sees_all_online_visitors')) {
            $filter['filterin']['dep_id'] = $userDepartments;
        }
    }
    
	$onlineUsers = erLhcoreClassModelChatOnlineUser::getList($filter);	
}

$columnsToHide = array('user_closed_ts','tslasign','reinform_timeout','unread_messages_informed','wait_timeout','wait_timeout_send','status_sub','timeout_message','nc_cb_executed','fbst','user_id','transfer_timeout_ts','operator_typing_id','transfer_timeout_ac','transfer_if_na','na_cb_executed','status','remarks','operation','operation_admin','screenshot_id','mail_send','online_user_id','dep_id','last_msg_id','hash','user_status','support_informed','support_informed','country_code','user_typing','user_typing_txt','lat','lon','chat_initiator','chat_variables','chat_duration','operator_typing','has_unread_messages','last_user_msg_time','additional_data');
$columnsName = array('id' => 'ID','user_tz_identifier' => 'User time zone','department_name' => 'Department','nick' => 'Nick','time' => 'Time','referrer' => 'Referrer','session_referrer' => 'Original referrer','ip' => 'IP','country_name' => 'Country','email' => 'E-mail','priority' => 'Priority','name' => 'Department','phone' => 'Phone','city' => 'City','wait_time' => 'Waited');

$onlineuserscolumnsToHide = array('requires_phone','lat_check_time','dep_id','requires_email','requires_username','invitation_seen_count','screenshot_id','operation','reopen_chat','vid','user_country_code','invitation_assigned','current_page', 'chat_id', 'operator_user_id', 'message_seen','operator_user_proactive','message_seen_ts','lat','lon','invitation_id','time_on_site','tt_time_on_site','invitation_count','store_chat');
$onlineuserscolumnsNames = array('last_check_time' => 'Last online check', 'notes' => 'Notes','referrer' => 'Referrer', 'page_title' => 'Page title', 'visitor_tz' => 'Visitor time zone','online_attr' => 'Attributes','id' => 'ID','operator_message' => 'Operator message', 'ip' => 'IP','identifier' => 'Identifier','user_agent' => 'Browser','last_visit' => 'Last visit','first_visit' => 'First visit','total_visits' => 'Total visits','user_country_name' => 'Country','city' => 'City','pages_count' => 'Pages viewed','tt_pages_count' => 'Total pages viewed');

echo json_encode(array(
'active_chats' => array('rows' => $activeChats, 'size' => count($activeChats), 'hidden_columns' => $columnsToHide, 'timestamp_delegate' => array('time'),'column_names' => $columnsName),
'unread_chats' => array('rows' => $unreadChats, 'size' => count($unreadChats), 'hidden_columns' => $columnsToHide, 'timestamp_delegate' => array('time'),'column_names' => $columnsName),
'online_users' => array('rows' => $onlineUsers, 'size' => count($onlineUsers), 'hidden_columns' => $onlineuserscolumnsToHide,'column_names' => $onlineuserscolumnsNames, 'timestamp_delegate' => array('last_check_time','last_visit','first_visit')),
'closed_chats' => array('rows' => $closedChats, 'size' => count($closedChats), 'hidden_columns' => $columnsToHide, 'timestamp_delegate' => array('time'),'column_names' => $columnsName),
'pending_chats' => array('rows' => $pendingChats, 'size' => count($pendingChats), 'hidden_columns' => $columnsToHide, 'timestamp_delegate' => array('time'),'column_names' => $columnsName),
'transfered_chats' => array('rows' => $transferedChats, 'size' => count($transferedChats), 'hidden_columns' => array_merge($columnsToHide,array('transfer_id')), 'timestamp_delegate' => array('time'),'column_names' => $columnsName),
));

$currentUser->updateLastVisit();
exit;
?>