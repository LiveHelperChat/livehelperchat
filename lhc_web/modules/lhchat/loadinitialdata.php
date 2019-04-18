<?php 

header('Content-Type: application/json');

$departmentParams = array();
$userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID());
if ($userDepartments !== true) {
    $departmentParams['filterin']['id'] = $userDepartments;
    if (!$currentUser->hasAccessTo('lhchat','sees_all_online_visitors')) {
        $filter['filterin']['dep_id'] = $userDepartments;
    }
}

$departmentParams['sort'] = 'sort_priority ASC, name ASC';

$departmentNames = array();
$departmentList = array();
$departments = erLhcoreClassModelDepartament::getList($departmentParams);

$loggedDepartments = erLhcoreClassChat::getLoggedDepartmentsIds(array_keys($departments), false);
$loggedDepartmentsExplicit = erLhcoreClassChat::getLoggedDepartmentsIds(array_keys($departments), true);

// Filter products
$filterProducts = array();
 
foreach ($departments as $department) {
    $departmentNames[$department->id] = $department->name;
    $departmentList[] = array(
        'id' => $department->id,
        'name' => $department->name,
        'hidden' => $department->hidden,
        'disabled' => $department->disabled == 1,
        'ogen' => in_array($department->id, $loggedDepartments),            // Online general
        'oexp' => in_array($department->id, $loggedDepartmentsExplicit),    // Online explicit
    );

    $filterProducts[] = $department->id;
}

$depGroupsList = array();
$depGroups = erLhcoreClassModelDepartamentGroup::getList();
foreach ($depGroups as $departmentGroup) {
    $depGroupsList[] = array(
        'id' => $departmentGroup->id,
        'name' => $departmentGroup->name,
    );
}

$productsFilter = array();

// Add products
if (!empty($departments)) {
    $productsFilter['filterin']['departament_id'] = array_keys($departments);
}

$productsNames = array();
$products = erLhAbstractModelProduct::getList($productsFilter);

foreach ($products as $product) {
    $productsNames[] = array (
        'name' => $product->name,
        'id' => $product->id
    );
}

// Handle inactivity on page reload without closing modal window
$userData = $currentUser->getUserData(true);

if ($userData->inactive_mode == 1) {
    $userData->inactive_mode = 0;
    
    if ($userData->hide_online == 0) { // change status only if he's not offline manually  
        
        $userDataTemp = new stdClass();
        $userDataTemp->id = $userData->id;
        $userDataTemp->hide_online = 0;
        
        erLhcoreClassUserDep::setHideOnlineStatus($userDataTemp);
    }
    
    erLhcoreClassUser::getSession()->update($userData);
}

$activityTimeout = erLhcoreClassModelUserSetting::getSetting('trackactivitytimeout',-1);

// If there is no individual setting user global one
if ($activityTimeout == -1) {
    $activityTimeout = (int)erLhcoreClassModelChatConfig::fetchCache('activity_timeout')->current_value*60;
}

// Perhaps it's set at global level
$trackActivity = (int)erLhcoreClassModelChatConfig::fetchCache('activity_track_all')->current_value;

if ($trackActivity == 0) {
    $trackActivity = erLhcoreClassModelUserSetting::getSetting('trackactivity',0);
}

$chatDel = array();
$chatOpen = array();

if (is_array($Params['user_parameters_unordered']['chatopen']) && !empty($Params['user_parameters_unordered']['chatopen'])) {

    $originalIds = $Params['user_parameters_unordered']['chatopen'];

    erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['chatopen']);
    $chats = erLhcoreClassChat::getList(array('filterin' => array('id' => $Params['user_parameters_unordered']['chatopen'])));

    // Delete any old chat if it exists
    $deleteKeys = array_diff($originalIds, array_keys($chats));
    foreach ($deleteKeys as $chat_id) {
        $chatDel[] = (int)$chat_id;
    }

    foreach ($chats as $chat) {
        if (erLhcoreClassChat::hasAccessToRead($chat)){
            $chatOpen[] = array(
                'id' => $chat->id,
                'nick' => erLhcoreClassDesign::shrt($chat->nick,10,'...',30,ENT_QUOTES),
            );
        } else {
            $chatDel[] = (int)$chat->id;
        }
    }
}

$userList = erLhcoreClassModelUser::getUserList(array('sort' => 'name ASC'));
erLhcoreClassChat::prefillGetAttributes($userList,array('id','name_official'),array(),array('remove_all' => true));

$columns = erLhAbstractModelChatColumn::getList(array('ignore_fields' => array('position','conditions','enabled','variable'),'sort' => 'position ASC, id ASC','filter' => array('enabled' => 1)));

$columnsAdd = array();
foreach ($columns as $column) {
    $columnsAdd[$column->column_identifier]['items'][] = 'cc_' . $column->id;
    $columnsAdd[$column->column_identifier]['name'] = $column->column_name;
    $columnsAdd[$column->column_identifier]['icon'] = $column->column_icon;
    $columnsAdd[$column->column_identifier]['cenabl'] = $column->chat_enabled == 1;
    $columnsAdd[$column->column_identifier]['oenabl'] = $column->online_enabled == 1;
}

$soundData = erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data;

$soundMessageEnabled = erLhcoreClassModelUserSetting::getSetting('chat_message',(int)($soundData['new_message_sound_admin_enabled']));
$soundNewChatEnabled = erLhcoreClassModelUserSetting::getSetting('new_chat_sound',(int)($soundData['new_chat_sound_enabled']));

$response = array(
    'n_msg_snd' => $soundMessageEnabled,
    'n_chat_snd' => $soundNewChatEnabled,
    'col' => array_values($columnsAdd), 'v' => erLhcoreClassUpdate::LHC_RELEASE, 'ho' => $userData->hide_online == 1, 'im' => $userData->invisible_mode == 1, 'user_list' => array_values($userList), 'user_groups' => array_values(erLhcoreClassModelGroup::getList(array('sort' => 'name ASC', 'filter' => array('disabled' => 0)))), 'track_activity' => $trackActivity, 'cdel' => $chatDel, 'copen' => $chatOpen, 'timeout_activity' => $activityTimeout, 'pr_names' => $productsNames, 'dp_groups' => $depGroupsList, 'dp_names' => $departmentNames, 'dep_list' => $departmentList);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.loadinitialdata',array('lists' => & $response));

echo json_encode($response);
exit;

?>