<?php 

header('Content-Type: application/json');

$departmentParams = array();
$userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID(), $currentUser->cache_version);
if ($userDepartments !== true) {
    $departmentParams['filterin']['id'] = $userDepartments;
    if (!$currentUser->hasAccessTo('lhchat','sees_all_online_visitors')) {
        $filter['filterin']['dep_id'] = $userDepartments;
    }
}

$departmentParams['sort'] = 'sort_priority ASC, name ASC';
$departmentParams['filter']['archive'] = 0;

$departmentNames = array();
$departmentList = array();

// Always include selected departments
$dwFilters = json_decode(erLhcoreClassModelUserSetting::getSetting('dw_filters', '{}', false, false, true),true);
$filterDep = [];

foreach (['actived','departmentd','unreadd','pendingd','operatord','closedd','mcd','botd','subjectd','pendingmd','activemd','alarmmd','mmd','department_online'] as $list) {
    if (isset($dwFilters[$list]) && !empty($dwFilters[$list])) {
        $filterDep = array_unique(array_merge($filterDep,explode("/",$dwFilters[$list])));
    }
}

if (!empty($filterDep)) {
    $departmentParams['filterin']['id'] = $filterDep;
} else {
    $departmentParams['limit'] = 20;
}

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
        'slc' => in_array($department->id, $filterDep)
    );

    $filterProducts[] = $department->id;
}

$depGroupsList = array();
$depGroups = erLhcoreClassModelDepartamentGroup::getList(erLhcoreClassUserDep::conditionalDepartmentGroupFilter());
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
        $userDataTemp->always_on = $userData->always_on;

        erLhcoreClassUserDep::setHideOnlineStatus($userDataTemp);

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.operator_inactivemode_changed', array('user' => & $userData, 'reason' => 'page_reload'));
    }
    
    erLhcoreClassUser::getSession()->update($userData);
}

$activityTimeout = erLhcoreClassModelUserSetting::getSetting('trackactivitytimeout',-1);

if ($activityTimeout > 1296000) {
    $activityTimeout = 1296000;
}

// If there is no individual setting user global one
if ($activityTimeout == -1) {
    $activityTimeout = (int)erLhcoreClassModelChatConfig::fetchCache('activity_timeout')->current_value*60;
}

// Perhaps it's set at global level
$trackActivity = (int)erLhcoreClassModelChatConfig::fetchCache('activity_track_all')->current_value;

if ($trackActivity == 0) {
    $trackActivity = (int)erLhcoreClassModelUserSetting::getSetting('trackactivity',0);
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
        if (erLhcoreClassChat::hasAccessToRead($chat) && (
                $chat->status != erLhcoreClassModelChat::STATUS_CLOSED_CHAT ||
                ($chat->user_id != $userData->id && (int)erLhcoreClassModelUserSetting::getSetting('remove_closed_chats_remote',0) == 0) ||
                $chat->cls_time > (time() - (int)erLhcoreClassModelUserSetting::getSetting('remove_close_timeout',5) * 60) ||
                erLhcoreClassModelUserSetting::getSetting('remove_closed_chats',0) == 0
            )
        ) {
            $chatOpen[] = array(
                'id' => $chat->id,
                'nick' => erLhcoreClassDesign::shrt($chat->nick,10,'...',30,ENT_QUOTES)
            );
        } else {
            $chatDel[] = (int)$chat->id;
        }
    }
}

$chatgDel = array();
$chatgOpen = array();

if (is_array($Params['user_parameters_unordered']['chatgopen']) && !empty($Params['user_parameters_unordered']['chatgopen'])) {

    $originalIds = $Params['user_parameters_unordered']['chatgopen'];

    erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['chatgopen']);
    $chats = erLhcoreClassModelGroupChat::getList(array('filterin' => array('id' => $Params['user_parameters_unordered']['chatgopen'])));

    // Delete any old chat if it exists
    $deleteKeys = array_diff($originalIds, array_keys($chats));
    foreach ($deleteKeys as $chat_id) {
        $chatgDel[] = (int)$chat_id;
    }

    foreach ($chats as $chat) {
        $chatgOpen[] = array(
            'id' => $chat->id,
            'nick' => erLhcoreClassDesign::shrt($chat->name,10,'...',30,ENT_QUOTES)
        );
    }
}

$chatmDel = array();
$chatmOpen = array();

if (is_array($Params['user_parameters_unordered']['chatmopen']) && !empty($Params['user_parameters_unordered']['chatmopen'])) {

    $originalIds = $Params['user_parameters_unordered']['chatmopen'];

    erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['chatmopen']);
    $chats = erLhcoreClassModelMailconvConversation::getList(array('filterin' => array('id' => $Params['user_parameters_unordered']['chatmopen'])));

    // Delete any old chat if it exists
    $deleteKeys = array_diff($originalIds, array_keys($chats));
    foreach ($deleteKeys as $chat_id) {
        $chatmDel[] = (int)$chat_id;
    }

    foreach ($chats as $chat) {
        $chatmOpen[] = array(
            'id' => $chat->id,
            'subject' => erLhcoreClassDesign::shrt($chat->subject_front,10,'...',30,ENT_QUOTES)
        );
    }
}

$columns = erLhAbstractModelChatColumn::getList(array('ignore_fields' => array('popup_content','position','conditions','enabled','variable'), 'sort' => 'position ASC, id ASC','filter' => array('enabled' => 1)));

$columnsAdd = array();
foreach ($columns as $column) {
    $columnsAdd[$column->column_identifier]['items'][] = 'cc_' . $column->id;
    $columnsAdd[$column->column_identifier]['name'] = $column->column_name;
    $columnsAdd[$column->column_identifier]['icon'] = $column->column_icon != "" && strpos($column->column_icon,'"') !== false ? json_decode($column->column_icon,true) : $column->column_icon;
    $columnsAdd[$column->column_identifier]['cenabl'] = $column->chat_enabled == 1;
    $columnsAdd[$column->column_identifier]['oenabl'] = $column->online_enabled == 1;
    $columnsAdd[$column->column_identifier]['iconm'] = $column->icon_mode == 1;
    $columnsAdd[$column->column_identifier]['iconp'] = $column->has_popup == 1;

    if ($column->sort_enabled == 1) {
        $columnsAdd[$column->column_identifier]['sorten'] = true;
    }

    if ($columnsAdd[$column->column_identifier]['iconp'] === true) {
        $columnsAdd[$column->column_identifier]['id'] = $column->id;
    }
}

$groupListParams = erLhcoreClassGroupUser::getConditionalUserFilter(false, true);
$groupListParams['sort'] = 'name ASC';
$groupListParams['filter']['disabled'] =0;

$widgets = json_decode(erLhcoreClassModelUserSetting::getSetting('dwo',''),true);

if (!is_array($widgets)) {
    $widgets = json_decode(erLhcoreClassModelChatConfig::fetch('dashboard_order')->current_value,true);
}

$widgets = erLhcoreClassChat::array_flatten($widgets);

$dwic = json_decode(erLhcoreClassModelUserSetting::getSetting('dwic', ''),true);
$not_ic = json_decode(erLhcoreClassModelUserSetting::getSetting('dw_nic', ''),true);

$response = array(
    'widgets' => $widgets,
    'exc_ic' => ($dwic === null ? [] : $dwic),
    'not_ic' => ($not_ic === null ? [] : $not_ic),
    'col' => array_values($columnsAdd),
    'v' => erLhcoreClassUpdate::LHC_RELEASE,
    'ho' => $userData->hide_online == 1,
    'a_on' => ($userData->always_on == 1),
    'im' => ($userData->invisible_mode == 1),
    'user_groups' => array_values(erLhcoreClassModelGroup::getList($groupListParams)),
    'track_activity' => $trackActivity,
    'cgdel' => $chatgDel,
    'cgopen' => $chatgOpen,
    'cmdel' => $chatmDel,
    'cmopen' => $chatmOpen,
    'cdel' => $chatDel,
    'copen' => $chatOpen,
    'timeout_activity' => $activityTimeout,
    'pr_names' => $productsNames,
    'dp_groups' => $depGroupsList,
    'dp_names' => $departmentNames,
    'dep_list' => $departmentList,
    'dw_filters' => $dwFilters,
    'bot_st' => array(
        'msg_nm' =>  (int)erLhcoreClassModelUserSetting::getSetting('bot_msg_nm',3),
        'bot_notifications' => (int)erLhcoreClassModelUserSetting::getSetting('bot_notifications',0)
    )
);

$noticeOptions = erLhcoreClassModelChatConfig::fetch('notice_message');
$data = (array)$noticeOptions->data;

if (!empty($data['message'])) {
    $response['notice']['message'] = $data['message'];
    $response['notice']['level'] = !empty($data['level']) ? $data['level'] : 'primary';
}

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.loadinitialdata',array('lists' => & $response));

echo json_encode($response);
exit;

?>