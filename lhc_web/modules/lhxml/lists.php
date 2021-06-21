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
$unreadChats = array(); // erLhcoreClassChat::getUnreadMessagesChats(10,0);

foreach ($activeChats as $index => $activeChat) {
    $activeChats[$index]->owner = $activeChat->n_off_full;
}

foreach ($pendingChats as $index => $pendingChat) {
    $pendingChats[$index]->owner = $pendingChat->n_off_full;
}

foreach ($closedChats as $index => $closedChat) {
    $closedChats[$index]->owner = $closedChat->n_off_full;
}

erLhcoreClassChat::prefillGetAttributes($activeChats,array('department_name','user_status_front'),array('updateIgnoreColumns','department','user'));
erLhcoreClassChat::prefillGetAttributes($closedChats,array('department_name','user_status_front'),array('updateIgnoreColumns','department','user'));
erLhcoreClassChat::prefillGetAttributes($pendingChats,array('department_name','user_status_front'),array('updateIgnoreColumns','department','user'));
erLhcoreClassChat::prefillGetAttributes($unreadChats,array('department_name'),array('updateIgnoreColumns','department','user'));

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

$columnsToHide = array('user_closed_ts','lsync','uagent','user_status_front','pnd_time','unanswered_chat','tslasign','reinform_timeout','unread_messages_informed','wait_timeout','wait_timeout_send','status_sub','timeout_message','nc_cb_executed','fbst','user_id','transfer_timeout_ts','operator_typing_id','transfer_timeout_ac','transfer_if_na','na_cb_executed','status','remarks','operation','operation_admin','screenshot_id','mail_send','online_user_id','dep_id','last_msg_id','hash','user_status','support_informed','support_informed','country_code','user_typing','user_typing_txt','lat','lon','chat_initiator','chat_variables','chat_duration','operator_typing','has_unread_messages','last_user_msg_time','additional_data');
$columnsName = array('id' => 'ID','chat_locale' => 'Visitor language','user_tz_identifier' => 'User time zone','department_name' => 'Department','nick' => 'Nick','time' => 'Time','referrer' => 'Referrer','session_referrer' => 'Original referrer','ip' => 'IP','country_name' => 'Country','email' => 'E-mail','priority' => 'Priority','name' => 'Department','phone' => 'Phone','city' => 'City','wait_time' => 'Waited');

$onlineuserscolumnsToHide = array('requires_phone','lat_check_time','dep_id','requires_email','requires_username','invitation_seen_count','screenshot_id','operation','reopen_chat','vid','user_country_code','invitation_assigned','current_page', 'chat_id', 'operator_user_id', 'message_seen','operator_user_proactive','message_seen_ts','lat','lon','invitation_id','time_on_site','tt_time_on_site','invitation_count','store_chat');
$onlineuserscolumnsNames = array('last_check_time' => 'Last online check', 'notes' => 'Notes','referrer' => 'Referrer', 'page_title' => 'Page title', 'visitor_tz' => 'Visitor time zone','online_attr' => 'Attributes','id' => 'ID','operator_message' => 'Operator message', 'ip' => 'IP','identifier' => 'Identifier','user_agent' => 'Browser','last_visit' => 'Last visit','first_visit' => 'First visit','total_visits' => 'Total visits','user_country_name' => 'Country','city' => 'City','pages_count' => 'Pages viewed','tt_pages_count' => 'Total pages viewed');

$onlineOperators = erLhcoreClassModelUserDep::getOnlineOperators($currentUser,true, array(),50,7 * 24 * 3600);
erLhcoreClassChat::prefillGetAttributes($onlineOperators,array('lastactivity_ago','offline_since','user_id','id','name_official','pending_chats','inactive_chats','active_chats','departments_names','hide_online','avatar'),array(),array('filter_function' => true, 'remove_all' => true));

foreach ($onlineOperators as $key => $value) {
    $onlineOperators[$key]->departments_names = erLhcoreClassDesign::shrt(implode(', ',$value->departments_names),10,'...',30,ENT_QUOTES);
}

$db = ezcDbInstance::get();
$groupChats = array();
foreach ($onlineOperators as $onlineOperator) {
   $sql = "SELECT DISTINCT `lh_group_chat`.`id`,count(`lh_group_chat_member`.`id`) as `tm_live` FROM `lh_group_chat`
    INNER JOIN lh_group_chat_member ON `lh_group_chat_member`.`group_id` = `lh_group_chat`.`id`
    WHERE
    `lh_group_chat_member`.`user_id` IN (". implode(',',[$currentUser->getUserID(), $onlineOperator->user_id]) . ") AND
    `lh_group_chat`.`type` = 1 AND
    `lh_group_chat`.`tm` = 2
    GROUP BY `lh_group_chat`.`id`
    HAVING
    `tm_live` = 2";

    $stmt = $db->prepare($sql);
    $stmt->execute();
    $chatId = $stmt->fetch(PDO::FETCH_COLUMN);
    if (is_numeric($chatId)) {
        $groupChatsID[] = $chatId;
    }
}

foreach ($onlineOperators as $index => $onlineOperator) {
    $onlineOperators[$index]->last_msg_time = 0;
    $onlineOperators[$index]->last_msg = "";
    $onlineOperators[$index]->has_unread = 0;
    if ($onlineOperator->user_id == $currentUser->getUserID()){
        unset($onlineOperators[$index]);
    }
}

if (!empty($groupChatsID)) {
    $groupChats = erLhcoreClassModelGroupChat::getList(array('filterin' => array('id' => $groupChatsID)));
    $myMembers = erLhcoreClassModelGroupChatMember::getList(array('filterin' => array('group_id' => $groupChatsID)));

    $membersRegrouped = array();
    $membersRegroupedMy = array();
    foreach ($myMembers as $member) {
        $membersRegrouped[$member->user_id] = $member;
        $membersRegroupedMy[$member->group_id][$member->user_id] = $member;
    }

    foreach ($onlineOperators as $index => $onlineOperator) {
        if (isset($membersRegrouped[$onlineOperator->user_id]) && isset($groupChats[$membersRegrouped[$onlineOperator->user_id]->group_id])) {
            $onlineOperators[$index]->last_msg = $groupChats[$membersRegrouped[$onlineOperator->user_id]->group_id]->last_msg;
            $onlineOperators[$index]->last_msg_time = $groupChats[$membersRegrouped[$onlineOperator->user_id]->group_id]->last_user_msg_time;
            if (isset($membersRegroupedMy[$membersRegrouped[$onlineOperator->user_id]->group_id][$currentUser->getUserID()])) {
                $onlineOperators[$index]->has_unread = $groupChats[$membersRegrouped[$onlineOperator->user_id]->group_id]->last_msg_id > $membersRegroupedMy[$membersRegrouped[$onlineOperator->user_id]->group_id][$currentUser->getUserID()]->last_msg_id ? 1 : 0;
            }
        }
    }
}

$response = array(
    'active_chats' => array('rows' => $activeChats, 'size' => count($activeChats), 'hidden_columns' => $columnsToHide, 'timestamp_delegate' => array('time'),'column_names' => $columnsName),
    'unread_chats' => array('rows' => $unreadChats, 'size' => count($unreadChats), 'hidden_columns' => $columnsToHide, 'timestamp_delegate' => array('time'),'column_names' => $columnsName),
    'online_users' => array('rows' => $onlineUsers, 'size' => count($onlineUsers), 'hidden_columns' => $onlineuserscolumnsToHide,'column_names' => $onlineuserscolumnsNames, 'timestamp_delegate' => array('last_check_time','last_visit','first_visit')),
    'closed_chats' => array('rows' => $closedChats, 'size' => count($closedChats), 'hidden_columns' => $columnsToHide, 'timestamp_delegate' => array('time'),'column_names' => $columnsName),
    'pending_chats' => array('rows' => $pendingChats, 'size' => count($pendingChats), 'hidden_columns' => $columnsToHide, 'timestamp_delegate' => array('time'),'column_names' => $columnsName),
    'transfered_chats' => array('rows' => $transferedChats, 'size' => count($transferedChats), 'hidden_columns' => array_merge($columnsToHide,array('transfer_id')), 'timestamp_delegate' => array('time'),'column_names' => $columnsName),
    'operators_chats' => array('rows' => $onlineOperators, 'size' => count($onlineOperators), 'hidden_columns' => array(), 'timestamp_delegate' => array('time'),'column_names' => $columnsName),
);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('xml.lists', array('list' => & $response));

echo json_encode($response);

$currentUser->updateLastVisit();
exit;
?>