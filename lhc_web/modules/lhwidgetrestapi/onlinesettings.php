<?php

erLhcoreClassRestAPIHandler::setHeaders();

$requestPayload = json_decode(file_get_contents('php://input'),true);

foreach ($requestPayload as $attr => $attrValue) {
    $Params['user_parameters_unordered'][$attr] = $attrValue;
}

$chat_ui = array();

$theme = false;

if (isset($requestPayload['theme']) && $requestPayload['theme'] > 0) {
    $theme = erLhAbstractModelWidgetTheme::fetch($requestPayload['theme']);
}

// Departments
$disabled_department = false;

if (is_array($Params['user_parameters_unordered']['department']) && erLhcoreClassModelChatConfig::fetch('hide_disabled_department')->current_value == 1) {
    try {
        erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['department']);
        $departments = erLhcoreClassModelDepartament::getList(array('filterin' => array('id' => $Params['user_parameters_unordered']['department'])));

        $disabledAll = true;
        foreach ($departments as $department){
            if ($department->disabled == 0) {
                $disabledAll = false;
            }
        }

        // Disable only if all provided departments are disabled
        if ($disabledAll == true) {
            $disabled_department = true;
        }

    } catch (Exception $e) {
        exit;
    }
}





if (is_array($Params['user_parameters_unordered']['department']) && count($Params['user_parameters_unordered']['department']) == 1) {
    erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['department']);
    $departament_id = array_shift($Params['user_parameters_unordered']['department']);
} else {
    $departament_id = 0;
}

// Additional javascript variables
if (is_array($Params['user_parameters_unordered']['department']) && !empty($Params['user_parameters_unordered']['department']) || is_numeric($departament_id) && $departament_id > 0) {

    $depIds = $Params['user_parameters_unordered']['department'];
    if (is_numeric($departament_id) && $departament_id > 0) {
        $depIds[] = $departament_id;
    }

    $jsVars = array();

    foreach (erLhAbstractModelChatVariable::getList(array('ignore_fields' => array('dep_id','var_name','var_identifier','type'), 'customfilter' => array('dep_id = 0 OR dep_id IN (' . implode(',',$depIds) .')'))) as $jsVar) {
        $jsVars[] = array('id' => $jsVar->id,'var' => $jsVar->js_variable);
    }

} else {
    $jsVars = array();
    foreach (erLhAbstractModelChatVariable::getList(array('ignore_fields' => array('dep_id','var_name','var_identifier','type'), 'filter' => array('dep_id' => 0))) as $jsVar) {
        $jsVars[] = array('id' => $jsVar->id, 'var' => $jsVar->js_variable);
    }
}


$departament_id_array = array();

if (is_array($Params['user_parameters_unordered']['department'])) {
    erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['department']);
    $departament_id_array = $Params['user_parameters_unordered']['department'];
}

if (is_numeric($departament_id) && $departament_id > 0 && ($startDataDepartment = erLhcoreClassModelChatStartSettings::findOne(array('filter' => array('department_id' => $departament_id)))) !== false) {
    $start_data_fields = $startDataFields = $startDataDepartment->data_array;
} else {
    // Start chat field options
    $startData = erLhcoreClassModelChatConfig::fetch('start_chat_data');
    $start_data_fields = $startDataFields = (array)$startData->data;
}

if (isset($startDataFields['requires_dep']) && $startDataFields['requires_dep'] == true && ((!isset($departament_id_array) || empty($departament_id_array)) && $departament_id == 0)) {
    $department_invalid = true;
} elseif (isset($startDataFields['requires_dep']) && $startDataFields['requires_dep'] == true && isset($startDataFields['requires_dep_lock']) && $startDataFields['requires_dep_lock'] == true) {
    if (!isset($_COOKIE['lhc_ldep'])) {
        setcookie('lhc_ldep', $departament_id > 0 ? $departament_id : implode(',',$departament_id_array),0,'/');
    } elseif (isset($_COOKIE['lhc_ldep']) && $_COOKIE['lhc_ldep'] != ($departament_id > 0 ? $departament_id : implode(',',$departament_id_array))) {
        $department_invalid = true;
    }
}

if (isset($startDataFields['hide_message_label']) && $startDataFields['hide_message_label'] == true){
    $chat_ui['hide_message_label'] = true;
}

if (isset($startDataFields['show_messages_box']) && $startDataFields['show_messages_box'] == true){
    $chat_ui['show_messages_box'] = true;
}

if (isset($startDataFields['user_msg_height']) && $startDataFields['user_msg_height'] != ''){
    $chat_ui['user_msg_height'] = (int)$startDataFields['user_msg_height'];
}

$fields = array();

if ($Params['user_parameters_unordered']['online'] == '0')
{
    if (
        (($Params['user_parameters_unordered']['mode'] == 'widget' || $Params['user_parameters_unordered']['mode'] == 'embed') && isset($start_data_fields['offline_name_visible_in_page_widget']) && $start_data_fields['offline_name_visible_in_page_widget'] == true) ||
        ($Params['user_parameters_unordered']['mode'] == 'popup' && isset($start_data_fields['offline_name_visible_in_popup']) && $start_data_fields['offline_name_visible_in_popup'] == true)
    ) {
        $fields[] = array(
            'type' => (isset($start_data_fields['offline_name_hidden']) && $start_data_fields['offline_name_hidden'] == true ? 'hidden' : 'text'),
            'width' => 6,
            'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Name'),
            'class' => 'form-control form-control-sm',
            'required' => (isset($start_data_fields['offline_name_require_option']) && $start_data_fields['offline_name_require_option'] == 'required'),
            'name' => 'Username',
            'identifier' => 'username',
            'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Enter your name')
        );
    }

    $fields[] = array(
        'type' => (isset($start_data_fields['offline_email_hidden']) && $start_data_fields['offline_email_hidden'] == true ? 'hidden' : 'text'),
        'width' => 6,
        'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','E-mail'),
        'class' => 'form-control form-control-sm',
        'required' => true,
        'name' => 'Email',
        'identifier' => 'email',
        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Enter your email address'),
    );


    if (
        (($Params['user_parameters_unordered']['mode'] == 'widget' || $Params['user_parameters_unordered']['mode'] == 'embed') && isset($start_data_fields['offline_phone_visible_in_page_widget']) && $start_data_fields['offline_phone_visible_in_page_widget'] == true) ||
        ($Params['user_parameters_unordered']['mode'] == 'popup' && isset($start_data_fields['offline_phone_visible_in_popup']) && $start_data_fields['offline_phone_visible_in_popup'] == true)
    ) {
        $fields[] = array(
            'type' => (isset($start_data_fields['offline_phone_hidden']) && $start_data_fields['offline_phone_hidden'] == true ? 'hidden' : 'text'),
            'width' => 6,
            'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Phone'),
            'class' => 'form-control form-control-sm',
            'required' => (isset($start_data_fields['offline_phone_require_option']) && $start_data_fields['offline_phone_require_option'] == 'required'),
            'name' => 'Phone',
            'identifier' => 'phone',
            'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Enter your phone'),
        );
    }

    /*if (
        ($Params['user_parameters_unordered']['mode'] == 'widget' && isset($start_data_fields['offline_file_visible_in_page_widget']) && $start_data_fields['offline_file_visible_in_page_widget'] == true) ||
        ($Params['user_parameters_unordered']['mode'] == 'popup' && isset($start_data_fields['offline_file_visible_in_popup']) && $start_data_fields['offline_file_visible_in_popup'] == true)
    ) {
        $fields[] = array(
            'type' => 'file',
            'width' => 12,
            'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','File'),
            'class' => 'form-control form-control-sm',
            'required' => false,
            'name' => 'File',
            'placeholder' => null,
        );
    }*/

    if (
        ( ($Params['user_parameters_unordered']['mode'] == 'widget' || $Params['user_parameters_unordered']['mode'] == 'embed') && isset($start_data_fields['offline_message_visible_in_page_widget']) && $start_data_fields['offline_message_visible_in_page_widget'] == true) ||
        ($Params['user_parameters_unordered']['mode'] == 'popup' && isset($start_data_fields['offline_message_visible_in_popup']) && $start_data_fields['offline_message_visible_in_popup'] == true)
    ) {
        $fields[] = array(
            'type' => (isset($start_data_fields['offline_message_hidden']) && $start_data_fields['offline_message_hidden'] == true ? 'hidden' : 'textarea'),
            'width' => 12,
            'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Your question'),
            'class' => 'form-control form-control-sm',
            'required' => false,
            'name' => 'Question',
            'identifier' => 'question',
            'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Enter your message'),
        );
    }

    if (
        (($Params['user_parameters_unordered']['mode'] == 'widget' || $Params['user_parameters_unordered']['mode'] == 'embed') && isset($start_data_fields['offline_tos_visible_in_page_widget']) && $start_data_fields['offline_tos_visible_in_page_widget'] == true) ||
        ($Params['user_parameters_unordered']['mode'] == 'popup' && isset($start_data_fields['offline_tos_visible_in_popup']) && $start_data_fields['offline_tos_visible_in_popup'] == true)
    ) {
        $fields[] = array(
            'type' => 'checkbox',
            'width' => 12,
            'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'I accept my personal data will be handled according to') . ' <a target="_blank" href="' . erLhcoreClassModelChatConfig::fetch('accept_tos_link')->current_value . '">' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'our terms and to the Law') . '</a>',
            'class' => 'form-check-input',
            'required' => false,
            'name' => 'AcceptTOS',
            'identifier' => 'accept_tos',
            'default' => (isset($start_data_fields['tos_checked_offline']) && $start_data_fields['tos_checked_offline'] == true),
            'placeholder' => '',
        );
    }

} else {
    // Name field widget mode
    if (
        (($Params['user_parameters_unordered']['mode'] == 'widget' || $Params['user_parameters_unordered']['mode'] == 'embed') && isset($start_data_fields['name_visible_in_page_widget']) && $start_data_fields['name_visible_in_page_widget'] == true) ||
        ($Params['user_parameters_unordered']['mode'] == 'popup' && isset($start_data_fields['name_visible_in_popup']) && $start_data_fields['name_visible_in_popup'] == true)
    ) {
        $fields[] = array(
            'type' => (isset($start_data_fields['name_hidden']) && $start_data_fields['name_hidden'] == true ? 'hidden' : 'text'),
            'width' => 6,
            'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Name'),
            'class' => 'form-control form-control-sm',
            'required' => (isset($start_data_fields['name_require_option']) && $start_data_fields['name_require_option'] == 'required'),
            'name' => 'Username',
            'identifier' => 'username',
            'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Enter your name')
        );
    }

    if (
        (($Params['user_parameters_unordered']['mode'] == 'widget' || $Params['user_parameters_unordered']['mode'] == 'embed') && isset($start_data_fields['email_visible_in_page_widget']) && $start_data_fields['email_visible_in_page_widget'] == true) ||
        ($Params['user_parameters_unordered']['mode'] == 'popup' && isset($start_data_fields['email_visible_in_popup']) && $start_data_fields['email_visible_in_popup'] == true)
    ) {
        $fields[] = array(
            'type' => (isset($start_data_fields['email_hidden']) && $start_data_fields['email_hidden'] == true ? 'hidden' : 'text'),
            'width' => 6,
            'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'E-mail'),
            'class' => 'form-control form-control-sm',
            'required' => true,
            'name' => 'Email',
            'identifier' => 'email',
            'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Enter your email address'),
        );
    }

    if (
        (($Params['user_parameters_unordered']['mode'] == 'widget' || $Params['user_parameters_unordered']['mode'] == 'embed') && isset($start_data_fields['phone_visible_in_page_widget']) && $start_data_fields['phone_visible_in_page_widget'] == true) ||
        ($Params['user_parameters_unordered']['mode'] == 'popup' && isset($start_data_fields['phone_visible_in_popup']) && $start_data_fields['phone_visible_in_popup'] == true)
    ) {
        $fields[] = array(
            'type' => (isset($start_data_fields['phone_hidden']) && $start_data_fields['phone_hidden'] == true ? 'hidden' : 'text'),
            'width' => 6,
            'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Phone'),
            'class' => 'form-control form-control-sm',
            'required' => (isset($start_data_fields['phone_require_option']) && $start_data_fields['phone_require_option'] == 'required'),
            'name' => 'Phone',
            'identifier' => 'phone',
            'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Enter your phone'),
        );
    }

    if (
        (($Params['user_parameters_unordered']['mode'] == 'widget' || $Params['user_parameters_unordered']['mode'] == 'embed')  && isset($start_data_fields['message_visible_in_page_widget']) && $start_data_fields['message_visible_in_page_widget'] == true) ||
        ($Params['user_parameters_unordered']['mode'] == 'popup' && isset($start_data_fields['message_visible_in_popup']) && $start_data_fields['message_visible_in_popup'] == true)
    ) {

        $placeholderMessage = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Enter your message');

        if ($theme instanceof erLhAbstractModelWidgetTheme && isset($theme->bot_configuration_array['placeholder_message']) && !empty($theme->bot_configuration_array['placeholder_message'])) {
            $placeholderMessage = $chat_ui['placeholder_message'] = $theme->bot_configuration_array['placeholder_message'];
        }

        $fields[] = array(
            'type' => (isset($start_data_fields['message_hidden']) && $start_data_fields['message_hidden'] == true ? 'hidden' : 'textarea'),
            'width' => 12,
            'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Your question'),
            'class' => 'form-control form-control-sm',
            'required' => false,
            'name' => 'Question',
            'identifier' => 'question',
            'placeholder' => $placeholderMessage,
        );
    }

    if (
        (($Params['user_parameters_unordered']['mode'] == 'widget' || $Params['user_parameters_unordered']['mode'] == 'embed') && isset($start_data_fields['tos_visible_in_page_widget']) && $start_data_fields['tos_visible_in_page_widget'] == true) ||
        ($Params['user_parameters_unordered']['mode'] == 'popup' && isset($start_data_fields['tos_visible_in_popup']) && $start_data_fields['tos_visible_in_popup'] == true)
    ) {
        $fields[] = array(
            'type' => 'checkbox',
            'width' => 12,
            'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'I accept my personal data will be handled according to') . ' <a target="_blank" href="' . erLhcoreClassModelChatConfig::fetch('accept_tos_link')->current_value . '">' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'our terms and to the Law') . '</a>',
            'class' => 'form-check-input',
            'required' => false,
            'name' => 'AcceptTOS',
            'identifier' => 'accept_tos',
            'default' => (isset($start_data_fields['tos_checked_online']) && $start_data_fields['tos_checked_online'] == true),
            'placeholder' => '',
        );
    }

    if (isset($start_data_fields['auto_start_chat']) && $start_data_fields['auto_start_chat'] == true) {
        $chat_ui['auto_start'] = true;
    }
}

// Handle departments
if (is_numeric($departament_id) && $departament_id > 0) {
    $departmentsOptions = array('departments' => array(array('value' => $departament_id)), 'settings' => array());
} else {
    $filter = array('filter' => array('disabled' => 0, 'hidden' => 0));

    if (!empty($departament_id_array)) {
        $filter['filterin']['id'] = $departament_id_array;
    }

    $filter['sort'] = 'sort_priority ASC, name ASC';

    $departments = erLhcoreClassModelDepartament::getList($filter);

    $departmentsOptions = array('departments' => array(), 'settings' => array());

    if (count($departments) > 1) {
        $departments = erLhcoreClassDepartament::sortByStatus($departments);
        foreach ($departments as $departament) {
            $isOnline = erLhcoreClassChat::isOnline($departament->id, false, array('ignore_user_status' => (int)erLhcoreClassModelChatConfig::fetch('ignore_user_status')->current_value, 'online_timeout' => (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout']));
            if (($departament->visible_if_online == 1 && $isOnline === true) || $departament->visible_if_online == 0) {
                $departmentItem = array(
                    'online' => $isOnline,
                    'value' => $departament->id,
                    'name' => $departament->name
                );
                $departmentsOptions['departments'][] = $departmentItem;
            }
        }

        if ($theme !== false && $theme->department_select != '') {
            $departmentsOptions['settings']['optional'] = $theme->department_select;
        }

        if ($theme !== false && $theme->department_title != '') {
            $departmentsOptions['settings']['label'] = htmlspecialchars($theme->department_title);
        } else {
            $departmentsOptions['settings']['label'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Department');
        }
    }
}

$outputResponse = array(
    'fields' => $fields,
    'js_vars' => $jsVars,
    'chat_ui' => $chat_ui,
    'department' => $departmentsOptions
);

$outputResponse['disabled'] = $disabled_department === true || (isset($department_invalid) && $department_invalid === true);

erLhcoreClassRestAPIHandler::outputResponse($outputResponse);
exit();