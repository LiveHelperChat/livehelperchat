<?php

erLhcoreClassRestAPIHandler::setHeaders();

erTranslationClassLhTranslation::$htmlEscape = false;

$requestPayload = json_decode(file_get_contents('php://input'),true);

foreach ($requestPayload as $attr => $attrValue) {
    $Params['user_parameters_unordered'][$attr] = $attrValue;
}

if (!isset($Params['user_parameters_unordered']['online'])) {
    $Params['user_parameters_unordered']['online'] = 1;
}

$chat_ui = array();
$paidSettings = array();

$theme = false;

if (isset($requestPayload['theme']) && $requestPayload['theme'] > 0) {
    $theme = erLhAbstractModelWidgetTheme::fetch($requestPayload['theme']);
    if ($theme instanceof erLhAbstractModelWidgetTheme){
        $theme->translate();
    } else {
        $theme = false;
    }
}

// Departments
$disabled_department = false;

if (is_array($Params['user_parameters_unordered']['department']) && !empty($Params['user_parameters_unordered']['department']) && erLhcoreClassModelChatConfig::fetch('hide_disabled_department')->current_value == 1) {
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

// Fetch correct start chat form settings
if (!(is_numeric($departament_id) && $departament_id > 0)) {
    if (isset($Params['user_parameters_unordered']['dep_default']) && is_numeric($Params['user_parameters_unordered']['dep_default']) && $Params['user_parameters_unordered']['dep_default'] > 0) {
        $department_id_form = (int)$Params['user_parameters_unordered']['dep_default'];
    } else {
        $filter = array('filter' => array('disabled' => 0, 'hidden' => 0));
        if (!empty($departament_id_array)) {
            $filter['filterin']['id'] = $departament_id_array;
        }
        $filter['sort'] = 'sort_priority ASC, name ASC';
        $departmentStartChat = erLhcoreClassModelDepartament::findOne($filter);
        $department_id_form = $departmentStartChat->id;
    }
} else {
    $department_id_form = $departament_id;
}

if (is_numeric($department_id_form) && $department_id_form > 0 && ($startDataDepartment = erLhcoreClassModelChatStartSettings::findOne(array('filter' => array('department_id' => $department_id_form)))) !== false) {
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

if (isset($startDataFields['np_border']) && $startDataFields['np_border'] == true) {
    $chat_ui['np_border'] = true;
}

if (isset($startDataFields['show_messages_box']) && $startDataFields['show_messages_box'] == true){
    $chat_ui['show_messages_box'] = true;
}

if (isset($startDataFields['user_msg_height']) && $startDataFields['user_msg_height'] != ''){
    $chat_ui['user_msg_height'] = (int)$startDataFields['user_msg_height'];
}

if (isset($startDataFields['hide_start_button']) && $startDataFields['hide_start_button'] == true) {
    $chat_ui['hstr_btn'] = true;
}

if ((int)erLhcoreClassModelChatConfig::fetch('bbc_button_visible')->value != 1) {
    $chat_ui['bbc_btnh'] = true;
}

if ($Params['user_parameters_unordered']['online'] == '0') {

    if (isset($start_data_fields['pre_offline_chat_html']) && $start_data_fields['pre_offline_chat_html'] != '') {
        $chat_ui['operator_profile'] = $start_data_fields['pre_offline_chat_html'];
        $chat_ui['offline_intro'] = '';
    } else {
        if ($theme instanceof erLhAbstractModelWidgetTheme && $theme->noonline_operators_offline) {
            $chat_ui['offline_intro'] = $theme->noonline_operators_offline;
        } else {
            $chat_ui['offline_intro'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','There are no online operators at the moment, please leave a message');
        }
        $chat_ui['operator_profile'] = '';
    }

    if ($Params['user_parameters_unordered']['online'] == '0') {

        if ($theme instanceof erLhAbstractModelWidgetTheme){
            if (isset($theme->bot_configuration_array['thank_feedback']) && !empty($theme->bot_configuration_array['thank_feedback'])) {
                $chat_ui['thank_feedback'] = $theme->bot_configuration_array['thank_feedback'];
            }

            if (isset($theme->bot_configuration_array['chat_unavailable']) && !empty($theme->bot_configuration_array['chat_unavailable'])) {
                $chat_ui['chat_unavailable'] = $theme->bot_configuration_array['chat_unavailable'];
            }
        }

        if (!isset($chat_ui['chat_unavailable'])) {
            $chat_ui['chat_unavailable'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Chat is currently unavailable') . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please try again later.');
        }
    }

} else {
    if (isset($start_data_fields['pre_chat_html']) && $start_data_fields['pre_chat_html'] != '') {
        $chat_ui['operator_profile'] = $start_data_fields['pre_chat_html'];
    } else {
        $chat_ui['operator_profile'] = '';
    }
}

$fields = array();

if ($Params['user_parameters_unordered']['online'] == '0')
{
    if (
        (($Params['user_parameters_unordered']['mode'] == 'widget' || $Params['user_parameters_unordered']['mode'] == 'embed') && isset($start_data_fields['offline_name_visible_in_page_widget']) && $start_data_fields['offline_name_visible_in_page_widget'] == true) ||
        ($Params['user_parameters_unordered']['mode'] == 'popup' && isset($start_data_fields['offline_name_visible_in_popup']) && $start_data_fields['offline_name_visible_in_popup'] == true)
    ) {

        $label = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Name');
        if (isset($theme->bot_configuration_array['formf_name']) && $theme->bot_configuration_array['formf_name'] != '') {
            $label = erLhcoreClassBBCode::make_clickable(htmlspecialchars($theme->bot_configuration_array['formf_name']));
        }

        $fields[] = array(
            'type' => (isset($start_data_fields['offline_name_hidden']) && $start_data_fields['offline_name_hidden'] == true ? 'hidden' : 'text'),
            'width' => (isset($start_data_fields['offline_name_width']) && $start_data_fields['offline_name_width'] > 0 ? (int)$start_data_fields['offline_name_width'] : 6),
            'label' => $label,
            'class' => 'form-control form-control-sm',
            'required' => (isset($start_data_fields['offline_name_require_option']) && $start_data_fields['offline_name_require_option'] == 'required'),
            'hide_prefilled' => (isset($start_data_fields['offline_name_hidden_prefilled']) && $start_data_fields['offline_name_hidden_prefilled'] == true),
            'name' => 'Username',
            'identifier' => 'username',
            'priority' => (isset($start_data_fields['offline_name_priority']) && is_numeric($start_data_fields['offline_name_priority']) ? (int)$start_data_fields['offline_name_priority'] : 0),
            'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Enter your name')
        );

    }

    if (
        (($Params['user_parameters_unordered']['mode'] == 'widget' || $Params['user_parameters_unordered']['mode'] == 'embed') && isset($start_data_fields['offline_email_visible_in_page_widget']) && $start_data_fields['offline_email_visible_in_page_widget'] == true) ||
        ($Params['user_parameters_unordered']['mode'] == 'popup' && isset($start_data_fields['offline_email_visible_in_popup']) && $start_data_fields['offline_email_visible_in_popup'] == true)
    ) {
        $label = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'E-mail');

        if (isset($theme->bot_configuration_array['formf_email']) && $theme->bot_configuration_array['formf_email'] != '') {
            $label = erLhcoreClassBBCode::make_clickable(htmlspecialchars($theme->bot_configuration_array['formf_email']));
        }

        $fields[] = array(
            'type' => (isset($start_data_fields['offline_email_hidden']) && $start_data_fields['offline_email_hidden'] == true ? 'hidden' : 'text'),
            'width' => (isset($start_data_fields['offline_email_width']) && $start_data_fields['offline_email_width'] > 0 ? (int)$start_data_fields['offline_email_width'] : 6),
            'label' => $label,
            'hide_prefilled' => (isset($start_data_fields['offline_email_hidden_prefilled']) && $start_data_fields['offline_email_hidden_prefilled'] == true),
            'class' => 'form-control form-control-sm',
            'priority' => (isset($start_data_fields['offline_email_priority']) && is_numeric($start_data_fields['offline_email_priority']) ? (int)$start_data_fields['offline_email_priority'] : 0),
            'required' => (!isset($start_data_fields['offline_email_require_option']) || $start_data_fields['offline_email_require_option'] == 'required'),
            'name' => 'Email',
            'identifier' => 'email',
            'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Enter your email address'),
        );
    }

    if (
        (($Params['user_parameters_unordered']['mode'] == 'widget' || $Params['user_parameters_unordered']['mode'] == 'embed') && isset($start_data_fields['offline_phone_visible_in_page_widget']) && $start_data_fields['offline_phone_visible_in_page_widget'] == true) ||
        ($Params['user_parameters_unordered']['mode'] == 'popup' && isset($start_data_fields['offline_phone_visible_in_popup']) && $start_data_fields['offline_phone_visible_in_popup'] == true)
    ) {
        $label = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Phone');

        if (isset($theme->bot_configuration_array['formf_phone']) && $theme->bot_configuration_array['formf_phone'] != '') {
            $label = erLhcoreClassBBCode::make_clickable(htmlspecialchars($theme->bot_configuration_array['formf_phone']));
        }

        $fields[] = array(
            'type' => (isset($start_data_fields['offline_phone_hidden']) && $start_data_fields['offline_phone_hidden'] == true ? 'hidden' : 'text'),
            'width' => (isset($start_data_fields['offline_name_width']) && $start_data_fields['offline_phone_width'] > 0 ? (int)$start_data_fields['offline_phone_width'] : 6),
            'label' => $label,
            'class' => 'form-control form-control-sm',
            'required' => (isset($start_data_fields['offline_phone_require_option']) && $start_data_fields['offline_phone_require_option'] == 'required'),
            'name' => 'Phone',
            'hide_prefilled' => (isset($start_data_fields['offline_phone_hidden_prefilled']) && $start_data_fields['offline_phone_hidden_prefilled'] == true),
            'priority' => (isset($start_data_fields['offline_phone_priority']) && is_numeric($start_data_fields['offline_phone_priority']) ? (int)$start_data_fields['offline_phone_priority'] : 0),
            'identifier' => 'phone',
            'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Enter your phone'),
        );
    }

    if (
        ($Params['user_parameters_unordered']['mode'] == 'widget' && isset($start_data_fields['offline_file_visible_in_page_widget']) && $start_data_fields['offline_file_visible_in_page_widget'] == true) ||
        ($Params['user_parameters_unordered']['mode'] == 'popup' && isset($start_data_fields['offline_file_visible_in_popup']) && $start_data_fields['offline_file_visible_in_popup'] == true)
    ) {
        $label = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','File');

        if (isset($theme->bot_configuration_array['formf_file']) && $theme->bot_configuration_array['formf_file'] != '') {
            $label = erLhcoreClassBBCode::make_clickable(htmlspecialchars($theme->bot_configuration_array['formf_file']));
        }

        $fileData = (array)erLhcoreClassModelChatConfig::fetch('file_configuration')->data;
        $fields[] = array(
            'type' => 'file',
            'width' => 12,
            'label' => $label,
            'class' => 'd-block fs14',
            'required' => false,
            'name' => 'File',
            'placeholder' => null,
            'priority' => (isset($start_data_fields['offline_file_priority']) && is_numeric($start_data_fields['offline_file_priority']) ? (int)$start_data_fields['offline_file_priority'] : 0),
            'fs' => $fileData['fs_max']*1024,
            'ft_us' => $fileData['ft_us'],
        );
    }

    if (
        ( ($Params['user_parameters_unordered']['mode'] == 'widget' || $Params['user_parameters_unordered']['mode'] == 'embed') && isset($start_data_fields['offline_message_visible_in_page_widget']) && $start_data_fields['offline_message_visible_in_page_widget'] == true) ||
        ($Params['user_parameters_unordered']['mode'] == 'popup' && isset($start_data_fields['offline_message_visible_in_popup']) && $start_data_fields['offline_message_visible_in_popup'] == true)
    ) {

        $label = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Your question');

        if (isset($theme->bot_configuration_array['formf_question']) && $theme->bot_configuration_array['formf_question'] != '') {
            $label = erLhcoreClassBBCode::make_clickable(htmlspecialchars($theme->bot_configuration_array['formf_question']));
        }

        $fields[] = array(
            'type' => (isset($start_data_fields['offline_message_hidden']) && $start_data_fields['offline_message_hidden'] == true ? 'hidden' : 'textarea'),
            'width' => 12,
            'label' => $label,
            'class' => 'form-control form-control-sm',
            'required' => (isset($start_data_fields['offline_message_require_option']) && $start_data_fields['offline_message_require_option'] == 'required'),
            'hide_prefilled' => (isset($start_data_fields['offline_message_hidden_prefilled']) && $start_data_fields['offline_message_hidden_prefilled'] == true),
            'name' => 'Question',
            'identifier' => 'question',
            'priority' => (isset($start_data_fields['offline_message_priority']) && is_numeric($start_data_fields['offline_message_priority']) ? (int)$start_data_fields['offline_message_priority'] : 0),
            'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Enter your message'),
        );
    }

    if (
        (($Params['user_parameters_unordered']['mode'] == 'widget' || $Params['user_parameters_unordered']['mode'] == 'embed') && isset($start_data_fields['offline_tos_visible_in_page_widget']) && $start_data_fields['offline_tos_visible_in_page_widget'] == true) ||
        ($Params['user_parameters_unordered']['mode'] == 'popup' && isset($start_data_fields['offline_tos_visible_in_popup']) && $start_data_fields['offline_tos_visible_in_popup'] == true)
    ) {

        $label = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'I accept my personal data will be handled according to') . ' <a target="_blank" href="' . erLhcoreClassModelChatConfig::fetch('accept_tos_link')->current_value . '">' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'our terms and to the Law') . '</a>';

        if (isset($theme->bot_configuration_array['custom_tos_text']) && $theme->bot_configuration_array['custom_tos_text'] != '') {
            $label = erLhcoreClassBBCode::make_clickable(htmlspecialchars($theme->bot_configuration_array['custom_tos_text']));
        }

        $fields[] = array(
            'type' => 'checkbox',
            'width' => 12,
            'label' => $label,
            'class' => 'form-check-input',
            'required' => false,
            'name' => 'AcceptTOS',
            'priority' => (isset($start_data_fields['offline_tos_priority']) && is_numeric($start_data_fields['offline_tos_priority']) ? (int)$start_data_fields['offline_tos_priority'] : 0),
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

        $label = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Name');

        if (isset($theme->bot_configuration_array['formf_name']) && $theme->bot_configuration_array['formf_name'] != '') {
            $label = erLhcoreClassBBCode::make_clickable(htmlspecialchars($theme->bot_configuration_array['formf_name']));
        }

        $fields[] = array(
            'type' => (isset($start_data_fields['name_hidden']) && $start_data_fields['name_hidden'] == true ? 'hidden' : 'text'),
            'width' => (isset($start_data_fields['offline_name_width']) && $start_data_fields['name_width'] > 0 ? (int)$start_data_fields['name_width'] : 6),
            'label' => $label,
            'class' => 'form-control form-control-sm',
            'required' => (isset($start_data_fields['name_require_option']) && $start_data_fields['name_require_option'] == 'required'),
            'name' => 'Username',
            'identifier' => 'username',
            'priority' => (isset($start_data_fields['name_priority']) && is_numeric($start_data_fields['name_priority']) ? (int)$start_data_fields['name_priority'] : 0),
            'hide_prefilled' => (isset($start_data_fields['name_hidden_prefilled']) && $start_data_fields['name_hidden_prefilled'] == true),
            'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Enter your name')
        );
    }

    if (
        (($Params['user_parameters_unordered']['mode'] == 'widget' || $Params['user_parameters_unordered']['mode'] == 'embed') && isset($start_data_fields['email_visible_in_page_widget']) && $start_data_fields['email_visible_in_page_widget'] == true) ||
        ($Params['user_parameters_unordered']['mode'] == 'popup' && isset($start_data_fields['email_visible_in_popup']) && $start_data_fields['email_visible_in_popup'] == true)
    ) {

        $label = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'E-mail');

        if (isset($theme->bot_configuration_array['formf_email']) && $theme->bot_configuration_array['formf_email'] != '') {
            $label = erLhcoreClassBBCode::make_clickable(htmlspecialchars($theme->bot_configuration_array['formf_email']));
        }

        $fields[] = array(
            'type' => (isset($start_data_fields['email_hidden']) && $start_data_fields['email_hidden'] == true ? 'hidden' : 'text'),
            'width' => (isset($start_data_fields['offline_name_width']) && $start_data_fields['email_width'] > 0 ? (int)$start_data_fields['email_width'] : 6),
            'label' => $label,
            'class' => 'form-control form-control-sm',
            'required' => (isset($start_data_fields['email_require_option']) && $start_data_fields['email_require_option'] == 'required' ? true : false),
            'name' => 'Email',
            'priority' => (isset($start_data_fields['email_priority']) && is_numeric($start_data_fields['email_priority']) ? (int)$start_data_fields['email_priority'] : 0),
            'hide_prefilled' => (isset($start_data_fields['email_hidden_prefilled']) && $start_data_fields['email_hidden_prefilled'] == true),
            'identifier' => 'email',
            'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Enter your email address'),
        );
    }

    if (
        (($Params['user_parameters_unordered']['mode'] == 'widget' || $Params['user_parameters_unordered']['mode'] == 'embed') && isset($start_data_fields['phone_visible_in_page_widget']) && $start_data_fields['phone_visible_in_page_widget'] == true) ||
        ($Params['user_parameters_unordered']['mode'] == 'popup' && isset($start_data_fields['phone_visible_in_popup']) && $start_data_fields['phone_visible_in_popup'] == true)
    ) {

        $label = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Phone');

        if (isset($theme->bot_configuration_array['formf_phone']) && $theme->bot_configuration_array['formf_phone'] != '') {
            $label = erLhcoreClassBBCode::make_clickable(htmlspecialchars($theme->bot_configuration_array['formf_phone']));
        }

        $fields[] = array(
            'type' => (isset($start_data_fields['phone_hidden']) && $start_data_fields['phone_hidden'] == true ? 'hidden' : 'text'),
            'width' => (isset($start_data_fields['phone_width']) && $start_data_fields['phone_width'] > 0 ? (int)$start_data_fields['phone_width'] : 6),
            'label' => $label,
            'class' => 'form-control form-control-sm',
            'required' => (isset($start_data_fields['phone_require_option']) && $start_data_fields['phone_require_option'] == 'required'),
            'name' => 'Phone',
            'hide_prefilled' => (isset($start_data_fields['phone_hidden_prefilled']) && $start_data_fields['phone_hidden_prefilled'] == true),
            'identifier' => 'phone',
            'priority' => (isset($start_data_fields['phone_priority']) && is_numeric($start_data_fields['phone_priority']) ? (int)$start_data_fields['phone_priority'] : 0),
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

        $label = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Your question');

        if (isset($theme->bot_configuration_array['formf_question']) && $theme->bot_configuration_array['formf_question'] != '') {
            $label = erLhcoreClassBBCode::make_clickable(htmlspecialchars($theme->bot_configuration_array['formf_question']));
        }

        $fields[] = array(
            'type' => (isset($start_data_fields['message_hidden']) && $start_data_fields['message_hidden'] == true ? 'hidden' : 'textarea'),
            'width' => 12,
            'label' => $label,
            'class' => 'form-control form-control-sm',
            'required' => (isset($start_data_fields['message_require_option']) && $start_data_fields['message_require_option'] == 'required'),
            'name' => 'Question',
            'identifier' => 'question',
            'priority' => (isset($start_data_fields['message_priority']) && is_numeric($start_data_fields['message_priority']) ? (int)$start_data_fields['message_priority'] : 0),
            'hide_prefilled' => (isset($start_data_fields['message_hidden_prefilled']) && $start_data_fields['message_hidden_prefilled'] == true),
            'placeholder' => $placeholderMessage,
        );
    }

    if (
        (($Params['user_parameters_unordered']['mode'] == 'widget' || $Params['user_parameters_unordered']['mode'] == 'embed') && isset($start_data_fields['tos_visible_in_page_widget']) && $start_data_fields['tos_visible_in_page_widget'] == true) ||
        ($Params['user_parameters_unordered']['mode'] == 'popup' && isset($start_data_fields['tos_visible_in_popup']) && $start_data_fields['tos_visible_in_popup'] == true)
    ) {

        $label = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'I accept my personal data will be handled according to') . ' <a target="_blank" href="' . erLhcoreClassModelChatConfig::fetch('accept_tos_link')->current_value . '">' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'our terms and to the Law') . '</a>';

        if (isset($theme->bot_configuration_array['custom_tos_text']) && $theme->bot_configuration_array['custom_tos_text'] != '') {
            $label = erLhcoreClassBBCode::make_clickable(htmlspecialchars($theme->bot_configuration_array['custom_tos_text']));
        }

        $fields[] = array(
            'type' => 'checkbox',
            'width' => 12,
            'label' => $label,
            'class' => 'form-check-input',
            'required' => false,
            'name' => 'AcceptTOS',
            'identifier' => 'accept_tos',
            'priority' => (isset($start_data_fields['tos_priority']) && is_numeric($start_data_fields['tos_priority']) ? (int)$start_data_fields['tos_priority'] : 0),
            'default' => (isset($start_data_fields['tos_checked_online']) && $start_data_fields['tos_checked_online'] == true),
            'placeholder' => '',
        );
    }

    if (isset($start_data_fields['auto_start_chat']) && $start_data_fields['auto_start_chat'] == true) {
        $chat_ui['auto_start'] = true;
    }
}

// Admin interface custom fields
if (isset($start_data_fields['custom_fields']) && $start_data_fields['custom_fields'] != ''){
    $customAdminfields = json_decode($start_data_fields['custom_fields'],true);
    if (is_array($customAdminfields)) {
        $adminCustomFieldsMode = $Params['user_parameters_unordered']['online'] == '0' ? 'off' : 'on';
        foreach ($customAdminfields as $key => $adminField) {
            if ($adminField['visibility'] == 'all' || $adminCustomFieldsMode == $adminField['visibility']) {

                $fieldData = array(
                    'type' => $adminField['fieldtype'],
                    'width' => $adminField['size'],
                    'label' => $adminField['fieldname'],
                    'class' => 'form-control form-control-sm',
                    'required' => $adminField['isrequired'] == 'true',
                    'name' => 'value_items_admin_'. $key,
                    'identifier' => 'value_items_admin_' . $key,
                    'identifier_prefill' => $adminField['fieldidentifier'],
                    'hide_prefilled' => ((isset($adminField['hide_prefilled']) && $adminField['hide_prefilled'] == true) ? true : false),
                    'value' => $adminField['defaultvalue'],
                    'priority' => ((isset($adminField['priority']) && is_numeric($adminField['priority'])) ? (int)$adminField['priority'] : 200)
                );

                if ($fieldData['type'] == 'dropdown') {
                    $optionsRaw = explode("\n",$adminField['options']);
                    $fieldData['options'] = array();
                    $defaultValue = null;
                    foreach ($optionsRaw as $optionRaw) {
                        $itemData = explode('=>',$optionRaw);
                        if ($defaultValue === null) {
                            $defaultValue = trim($itemData[0]);
                        }
                        $fieldData['options'][] = array(
                            'name' => trim(isset($itemData[1]) ? $itemData[1] : $itemData[0]),
                            'value' => trim($itemData[0])
                        );
                    }
                    $fieldData['value'] = $defaultValue;
                }

                $fields[] = $fieldData;
            }
        } 
    }
}

if (isset($requestPayload['phash']) && isset($requestPayload['pvhash']) && (string)$requestPayload['phash'] != '' && (string)$requestPayload['pvhash'] != '') {
    $paidSettings = erLhcoreClassChatPaid::paidChatWorkflow(array(
        'uparams' => $requestPayload,
        'mode' => 'chat',
        'output' => 'json'
    ));
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

if (erLhcoreClassModelChatConfig::fetch('product_enabled_module')->current_value == 1) {

    $filter = array('sort' => 'priority ASC, name ASC');

    if (is_array($Params['user_parameters_unordered']['product']) && !empty($Params['user_parameters_unordered']['product'])) {
        erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['product']);
        $filter['filterin']['id'] = $Params['user_parameters_unordered']['product'];
    }

    if (!empty($departament_id_array)) {
        $filter['filterin']['departament_id'] = $departament_id_array;
    }

    if (is_numeric($departament_id) && $departament_id > 0) {
        $filter['filterin']['departament_id'][] = $departament_id;
    }

    $filter['filter']['disabled'] = 0;

    if (erLhcoreClassModelChatConfig::fetch('product_show_departament')->current_value == 0) {

        $products = erLhAbstractModelProduct::getList($filter);

        if (!empty($products)) {
            $departmentsOptions['settings']['hide_department'] = true;
            $departmentsOptions['products'] = array();
            foreach ($products as $product) {
                $departmentsOptions['products'][] = array(
                    'value'=> $product->id,
                    'name'=> $product->name,
                );
            }
            $departmentsOptions['settings']['product_required'] = true;
        }

    } else {
        $departmentsOptions['settings']['product_by_department'] = true;
    }

    $departmentsOptions['settings']['product'] = true;
}

if ($theme !== false) {

    // Theme configuration overrides default settings
    if (isset($theme->bot_configuration_array['hide_bb_code']) && $theme->bot_configuration_array['hide_bb_code'] == true) {
        $chat_ui['bbc_btnh'] = true;
    } elseif (isset($chat_ui['bbc_btnh'])) {
        unset($chat_ui['bbc_btnh']);
    }

    if ($Params['user_parameters_unordered']['mode'] == 'widget' || $Params['user_parameters_unordered']['mode'] == 'embed') {
        if ($Params['user_parameters_unordered']['mode'] == 'widget') {
            if ($theme->popup_image_url != '') {
                $chat_ui['img_icon_popup'] = $theme->popup_image_url;
            }

            if ($theme->close_image_url != '') {
                $chat_ui['img_icon_close'] = $theme->close_image_url;
            }

            if ($theme->minimize_image_url != '') {
                $chat_ui['img_icon_min'] = $theme->minimize_image_url;
            }

            foreach (array('min_text','popup_text','end_chat_text') as $textIcon) {
                if (isset($theme->bot_configuration_array[$textIcon]) && $theme->bot_configuration_array[$textIcon] != '') {
                    $chat_ui[$textIcon] = $theme->bot_configuration_array[$textIcon];
                }
            }
        }

        if (isset($theme->bot_configuration_array['custom_html_widget_bot']) && $theme->bot_configuration_array['custom_html_widget_bot'] != '') {
            $onlyBotOnline = erLhcoreClassChat::isOnlyBotOnline($departament_id > 0 ? $departament_id : $Params['user_parameters_unordered']['department']);

            if ($onlyBotOnline === true) {
                $chat_ui['custom_html_widget'] = $theme->bot_configuration_array['custom_html_widget_bot'];
            }
        }

        if (!isset($chat_ui['custom_html_widget']) && isset($theme->bot_configuration_array['custom_html_widget']) && $theme->bot_configuration_array['custom_html_widget'] != '') {
            $chat_ui['custom_html_widget'] = $theme->bot_configuration_array['custom_html_widget'];
        }
    }

    if (isset($theme->bot_configuration_array['trigger_id']) && !empty($theme->bot_configuration_array['trigger_id']) && $theme->bot_configuration_array['trigger_id'] > 0) {

        $tpl = new erLhcoreClassTemplate('lhchat/part/render_intro.tpl.php');

        // Use bot photo in case it's bot messages
        $bot = erLhcoreClassModelGenericBotBot::fetch($theme->bot_configuration_array['bot_id']);

        if ($bot instanceof erLhcoreClassModelGenericBotBot)
        {
            if ($bot->has_photo) {
                $theme->operator_image_url = $bot->photo_path;
            }

            if (isset($Params['user_parameters_unordered']['vid']) && !empty($Params['user_parameters_unordered']['vid'])){
                $onlineUser = erLhcoreClassModelChatOnlineUser::fetchByVid($Params['user_parameters_unordered']['vid']);
                if ($onlineUser instanceof erLhcoreClassModelChatOnlineUser) {
                    $chat = new erLhcoreClassModelChat();
                    $chat->bot = $bot;
                    $chat->gbot_id = $bot->id;
                    $chat->additional_data_array = $onlineUser->online_attr_array;
                    $chat->chat_variables_array = $onlineUser->chat_variables_array;
                    $tpl->set('chat',$chat);
                }
            }
        }

        $tpl->set('theme',$theme);
        $tpl->set('react',true);
        $tpl->set('no_wrap_intro',true);
        $tpl->set('no_br',true);
        $tpl->set('triggerMessageId',$theme->bot_configuration_array['trigger_id']);

        $chat_ui['cmmsg_widget'] = $tpl->fetch();

    } elseif (isset($theme->bot_configuration_array['auto_bot_intro']) && $theme->bot_configuration_array['auto_bot_intro'] == true) {

        if (isset($requestPayload['bot_id']) && is_numeric($requestPayload['bot_id']) && $requestPayload['bot_id'] > 0) {
            $bot = erLhcoreClassModelGenericBotBot::fetch($requestPayload['bot_id']);
        } elseif ($departament_id > 0) {
            $department = erLhcoreClassModelDepartament::fetch($departament_id);
            if (isset($department->bot_configuration_array['bot_id']) && is_numeric($department->bot_configuration_array['bot_id']) && $department->bot_configuration_array['bot_id'] > 0) {
                $bot = erLhcoreClassModelGenericBotBot::fetch($department->bot_configuration_array['bot_id']);
            }
        }

        if (isset($bot) && $bot instanceof erLhcoreClassModelGenericBotBot) {

            $botIds = $bot->getBotIds();

            if ($bot instanceof erLhcoreClassModelGenericBotBot && $bot->has_photo)
            {
                $theme->operator_image_url = $bot->photo_path;
            }

            $triggerDefault = erLhcoreClassModelGenericBotTrigger::findOne(array('filterin' => array('bot_id' => $botIds), 'filter' => array('default' => 1)));

            if ($triggerDefault instanceof erLhcoreClassModelGenericBotTrigger) {
                $tpl = new erLhcoreClassTemplate('lhchat/part/render_intro.tpl.php');
                $tpl->set('theme',$theme);
                $tpl->set('react',true);
                $tpl->set('no_wrap_intro',true);
                $tpl->set('no_br',true);
                $tpl->set('triggerMessageId',$triggerDefault->id);
                $chat_ui['cmmsg_widget'] = $tpl->fetch();
            }
        }
    }

    if (isset($theme->bot_configuration_array['prev_msg']) && $theme->bot_configuration_array['prev_msg'] == true) {
        if (!isset($onlineUser) || !($onlineUser instanceof erLhcoreClassModelChatOnlineUser)) {
            if (isset($Params['user_parameters_unordered']['vid']) && !empty($Params['user_parameters_unordered']['vid'])){
                $onlineUser = erLhcoreClassModelChatOnlineUser::fetchByVid($Params['user_parameters_unordered']['vid']);
            }
        }

        if (isset($onlineUser) && $onlineUser instanceof erLhcoreClassModelChatOnlineUser) {

            $previousChat = erLhcoreClassModelChat::findOne(array('sort' => 'id DESC', 'limit' => 1, 'filter' => array('online_user_id' => $onlineUser->id)));

            if ($previousChat instanceof erLhcoreClassModelChat) {

                if (!isset($chat_ui['cmmsg_widget'])) {
                    $chat_ui['cmmsg_widget'] = '';
                }

                if ($previousChat->has_unread_op_messages == 1) {
                    $previousChat->unread_op_messages_informed = 0;
                    $previousChat->has_unread_op_messages = 0;
                    $previousChat->unanswered_chat = 0;
                    $previousChat->updateThis(array('update' => array('unread_op_messages_informed','has_unread_op_messages','unanswered_chat')));
                    $chat_ui['uprev'] = true;
                }

                $tpl = erLhcoreClassTemplate::getInstance( 'lhchat/previous_chat.tpl.php');
                $tpl->set('messages', erLhcoreClassChat::getPendingMessages((int)$previousChat->id,  0));
                $tpl->set('chat',$previousChat);
                $tpl->set('sync_mode','');
                $tpl->set('async_call',true);
                $tpl->set('theme',$theme);
                $tpl->set('react',true);
                $chat_ui['cmmsg_widget'] = $tpl->fetch() . $chat_ui['cmmsg_widget'];
            }
        }
    }

    if ($Params['user_parameters_unordered']['mode'] == 'popup') {
        if (isset($theme->bot_configuration_array['custom_html_bot']) && $theme->bot_configuration_array['custom_html_bot'] != '') {
            $onlyBotOnline = erLhcoreClassChat::isOnlyBotOnline($departament_id > 0 ? $departament_id : $Params['user_parameters_unordered']['department']);
            if ($onlyBotOnline === true) {
                $chat_ui['custom_html_widget'] = $theme->bot_configuration_array['custom_html_bot'];
            }
        }
        if (!isset($chat_ui['custom_html_widget']) && isset($theme->bot_configuration_array['custom_html']) && $theme->bot_configuration_array['custom_html'] != '') {
            $chat_ui['custom_html_widget'] = $theme->bot_configuration_array['custom_html'];
        }
    }

    if ($theme->explain_text != '') {
        if (!isset($chat_ui['custom_html_widget'])) {
            $chat_ui['custom_html_widget'] = '';
        }

        $chat_ui['custom_html_widget'] .= erLhcoreClassBBCode::make_clickable(htmlspecialchars($theme->explain_text));
    }

    if (isset($theme->bot_configuration_array['close_in_status']) && $theme->bot_configuration_array['close_in_status'] == true) {
        $chat_ui['clinst'] = true;
    }

    if (isset($theme->bot_configuration_array['msg_expand']) && $theme->bot_configuration_array['msg_expand'] == true) {
        $chat_ui['msg_expand'] = true;
    }

    if ($Params['user_parameters_unordered']['online'] == '0' && isset($theme->bot_configuration_array['custom_start_button_offline']) && $theme->bot_configuration_array['custom_start_button_offline'] != '') {
        $chat_ui['custom_start_button'] = $theme->bot_configuration_array['custom_start_button_offline'];
    }

    if ($Params['user_parameters_unordered']['online'] == '1') {

        if (isset($theme->bot_configuration_array['custom_start_button_bot']) && $theme->bot_configuration_array['custom_start_button_bot'] != '') {
            if (!isset($onlyBotOnline)) {
                $onlyBotOnline = erLhcoreClassChat::isOnlyBotOnline($departament_id > 0 ? $departament_id : $Params['user_parameters_unordered']['department']);
            }

            if ($onlyBotOnline === true) {
                $chat_ui['custom_start_button'] = $theme->bot_configuration_array['custom_start_button_bot'];
            }
        }

        if (!isset($chat_ui['custom_start_button']) && isset($theme->bot_configuration_array['custom_start_button']) && $theme->bot_configuration_array['custom_start_button'] != '') {
            $chat_ui['custom_start_button'] = $theme->bot_configuration_array['custom_start_button'];
        }
    }

    if (isset($theme->bot_configuration_array['custom_html_header']) && $theme->bot_configuration_array['custom_html_header'] != '') {
        $chat_ui['custom_html_header'] = $theme->bot_configuration_array['custom_html_header'];
    }

    if (isset($theme->bot_configuration_array['custom_html_header_body']) && $theme->bot_configuration_array['custom_html_header_body'] != '') {
        $chat_ui['custom_html_header_body'] = $theme->bot_configuration_array['custom_html_header_body'];
    }
}

if ($Params['user_parameters_unordered']['online'] == '1' && isset($startDataFields['show_operator_profile']) && $startDataFields['show_operator_profile'] != '') {
    $tpl = new erLhcoreClassTemplate('lhchat/part/operator_profile_start_chat.tpl.php');
    $tpl->set('theme',$theme);
    $tpl->set('start_data_fields',$startDataFields);
    $tpl->set('react',true);

    if (!isset($chat_ui['operator_profile'])) {
        $chat_ui['operator_profile'] = '';
    }

    $chat_ui['operator_profile'] .= $tpl->fetch();
}

if ($theme !== false && $theme->hide_popup == 1) {
    $chat_ui['hide_popup'] = true;
}

$chat_ui['header_buttons'] = array(
    array(
        'pos' => 'left',
        'btn' => 'min'
    ),
    array(
        'pos' => 'right',
        'btn' => 'close'
    ),
    array(
        'pos' => 'right',
        'btn' => 'popup'
    )
);

if ($theme !== false && isset($theme->bot_configuration_array['icons_order']) && $theme->bot_configuration_array['icons_order'] != '') {
    $icons = explode(',',str_replace(' ','',$theme->bot_configuration_array['icons_order']));
    $chat_ui['header_buttons'] = array();
    foreach ($icons as $icon) {
        $paramsIcon = explode('_',$icon);
        $chat_ui['header_buttons'][] = array(
            'pos' => $paramsIcon[0],
            'btn' => $paramsIcon[1],
            'print' => isset($paramsIcon[2]) && $paramsIcon[2] == 'print',
        );
    }
}

if ($theme !== false && $theme->hide_close == 1) {
    $chat_ui['hide_close'] = true;
}

$visibleCount = ($departament_id > 0 || count($departmentsOptions['departments']) == 0) ? 0 : 1;
$messageFieldVisible = isset($departmentsOptions['settings']['product']);

if (isset($departmentsOptions['settings']['product'])) {
    $visibleCount = 1;
}

foreach ($fields as $field) {
    if ($field['type'] != 'hidden') {
        $visibleCount++;
        if (isset($field['identifier']) && $field['identifier'] == 'question') {
            $messageFieldVisible = true;
        }
    }
}

usort($fields, function($a, $b){
    return isset($a['priority']) && isset($b['priority']) && $a['priority'] > $b['priority'];
});

// We have to increase count to show normal form
if ($messageFieldVisible === false && $visibleCount == 1) {
    $visibleCount = 2;
}

$chat_ui['max_length'] = (int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value - 1;

$outputResponse = array(
    'fields' => $fields,
    'fields_visible' => $visibleCount, // how many fields are visible one
    'js_vars' => $jsVars,
    'chat_ui' => $chat_ui,
    'paid' => $paidSettings
);

$outputResponse['disabled'] = $disabled_department === true || (isset($department_invalid) && $department_invalid === true);

if ($outputResponse['disabled'] === true) {
    $departmentsOptions['departments'] = [];
}

$outputResponse['department'] = $departmentsOptions;
$outputResponse['dep_forms'] = $department_id_form;

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('widgetrestapi.onlinesettings', array('output' => & $outputResponse));

erLhcoreClassRestAPIHandler::outputResponse($outputResponse);
exit();