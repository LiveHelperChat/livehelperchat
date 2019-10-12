<?php

erLhcoreClassRestAPIHandler::setHeaders();

// Set theme
$theme = false;
if (isset($Params['user_parameters_unordered']['theme']) && (int)$Params['user_parameters_unordered']['theme'] > 0){
    try {
        $theme = erLhAbstractModelWidgetTheme::fetch($Params['user_parameters_unordered']['theme']);
    } catch (Exception $e) {

    }
} else {
    $defaultTheme = erLhcoreClassModelChatConfig::fetch('default_theme_id')->current_value;
    if ($defaultTheme > 0) {
        try {
            $theme = erLhAbstractModelWidgetTheme::fetch($defaultTheme);
        } catch (Exception $e) {

        }
    }
}

// Departments
$disabled_department = false;

if (is_array($Params['user_parameters_unordered']['department']) && erLhcoreClassModelChatConfig::fetch('hide_disabled_department')->current_value == 1){
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
        if ($disabledAll == true){
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

$fields = array();

if (isset($start_data_fields['offline_name_visible_in_page_widget']) && $start_data_fields['offline_name_visible_in_page_widget'] == true) {
    $fields[] = array(
        'type' => (isset($start_data_fields['offline_name_hidden']) && $start_data_fields['offline_name_hidden'] == true ? 'hidden' : 'text'),
        'width' => 6,
        'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Name'),
        'class' => 'form-control form-control-sm',
        'required' => (isset($start_data_fields['offline_name_require_option']) && $start_data_fields['offline_name_require_option'] == 'required'),
        'name' => 'Username',
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
    'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Enter your email address'),
);

if (isset($start_data_fields['offline_phone_visible_in_page_widget']) && $start_data_fields['offline_phone_visible_in_page_widget'] == true){
    $fields[] = array(
        'type' => (isset($start_data_fields['offline_phone_hidden']) && $start_data_fields['offline_phone_hidden'] == true ? 'hidden' : 'text'),
        'width' => 6,
        'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Phone'),
        'class' => 'form-control form-control-sm',
        'required' => (isset($start_data_fields['offline_phone_require_option']) && $start_data_fields['offline_phone_require_option'] == 'required'),
        'name' => 'Phone',
        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Enter your phone'),
    );
}

if (isset($start_data_fields['offline_file_visible_in_page_widget']) && $start_data_fields['offline_file_visible_in_page_widget'] == true){
    $fields[] = array(
        'type' => 'file',
        'width' => 12,
        'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','File'),
        'class' => 'form-control form-control-sm',
        'required' => false,
        'name' => 'File',
        'placeholder' => null,
    );
}

if (isset($start_data_fields['offline_message_visible_in_page_widget']) && $start_data_fields['offline_message_visible_in_page_widget'] == true){
    $fields[] = array(
        'type' => (isset($start_data_fields['offline_message_hidden']) && $start_data_fields['offline_message_hidden'] == true ? 'hidden' : 'textarea'),
        'width' => 12,
        'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Your question'),
        'class' => 'form-control form-control-sm',
        'required' => false,
        'name' => 'Question',
        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Enter your message'),
    );
}

$outputResponse = array(
    'fields' => $fields,
    'department' => $departament_id
);

erLhcoreClassRestAPIHandler::outputResponse($outputResponse);
exit();