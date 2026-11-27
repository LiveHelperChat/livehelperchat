<?php

$tpl = erLhcoreClassTemplate::getInstance('lhjitsi/configuration.tpl.php');

$jitsiConfigObj = erLhcoreClassModelChatConfig::fetch('jitsi_configuration');
$storedData = is_array($jitsiConfigObj->data) ? $jitsiConfigObj->data : [];
$defaults = erLhcoreClassJitsi::getDefaultConfig();
$data = array_merge($defaults, $storedData);
$data['operator_options'] = array_merge(
    $defaults['operator_options'],
    isset($storedData['operator_options']) && is_array($storedData['operator_options']) ? $storedData['operator_options'] : []
);
$data['visitor_options'] = array_merge(
    $defaults['visitor_options'],
    isset($storedData['visitor_options']) && is_array($storedData['visitor_options']) ? $storedData['visitor_options'] : []
);

if (isset($_POST['StoreJitsiConfiguration'])) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('jitsi/configuration');
        exit;
    }

    $definition = [
        'enabled' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean'),
        'domain' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'),
        'room_prefix' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'),
        'default_mode' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'string'),
        'video_enabled' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean'),
        'screenshare_enabled' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean'),
        'disable_deep_linking' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean'),
        'confirm_before_invite' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean'),
        'auto_join_operator' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean'),
        'visitor_call_mode' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'string'),
        'widget_audio_minimal' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean'),
        'widget_audio_height' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int', ['min_range' => 160, 'max_range' => 420]),
        'widget_video_width' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int', ['min_range' => 0, 'max_range' => 1600]),
        'widget_video_height' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int', ['min_range' => 0, 'max_range' => 1200]),
        'jwt_mode' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'string'),
        'jwt_ttl' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int', ['min_range' => 60, 'max_range' => 86400]),
        'jwt_app_id' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'),
        'jwt_app_secret' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'),
        'clear_jwt_app_secret' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean'),
        'jaas_app_id' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'),
        'jaas_kid' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'),
        'jaas_private_key' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'),
        'clear_jaas_private_key' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean'),
    ];

    $form = new ezcInputForm(INPUT_POST, $definition);
    $Errors = [];

    $data['enabled'] = $form->hasValidData('enabled') && $form->enabled == true;
    $data['video_enabled'] = $form->hasValidData('video_enabled') && $form->video_enabled == true;
    $data['screenshare_enabled'] = $form->hasValidData('screenshare_enabled') && $form->screenshare_enabled == true;
    $data['disable_deep_linking'] = $form->hasValidData('disable_deep_linking') && $form->disable_deep_linking == true;
    $data['confirm_before_invite'] = $form->hasValidData('confirm_before_invite') && $form->confirm_before_invite == true;
    $data['auto_join_operator'] = $form->hasValidData('auto_join_operator') && $form->auto_join_operator == true;

    $rawVisitorCallMode = $form->hasValidData('visitor_call_mode') ? $form->visitor_call_mode : erLhcoreClassJitsi::VISITOR_CALL_MODE_POPUP;
    $data['visitor_call_mode'] = in_array($rawVisitorCallMode, [
        erLhcoreClassJitsi::VISITOR_CALL_MODE_POPUP,
        erLhcoreClassJitsi::VISITOR_CALL_MODE_WIDGET_AUDIO,
        erLhcoreClassJitsi::VISITOR_CALL_MODE_WIDGET,
    ], true) ? $rawVisitorCallMode : erLhcoreClassJitsi::VISITOR_CALL_MODE_POPUP;

    $data['widget_audio_minimal'] = $form->hasValidData('widget_audio_minimal') && $form->widget_audio_minimal == true;
    $data['widget_audio_height'] = $form->hasValidData('widget_audio_height') ? (int)$form->widget_audio_height : 220;
    $rawVideoW = $form->hasValidData('widget_video_width') ? (int)$form->widget_video_width : 680;
    $rawVideoH = $form->hasValidData('widget_video_height') ? (int)$form->widget_video_height : 620;
    $data['widget_video_width'] = ($rawVideoW > 0 && $rawVideoW < 350) ? 350 : $rawVideoW;
    $data['widget_video_height'] = ($rawVideoH > 0 && $rawVideoH < 400) ? 400 : $rawVideoH;

    $rawMode = $form->hasValidData('default_mode') ? $form->default_mode : erLhcoreClassJitsi::MODE_VIDEO;
    $data['default_mode'] = in_array($rawMode, [erLhcoreClassJitsi::MODE_AUDIO, erLhcoreClassJitsi::MODE_VIDEO], true)
        ? $rawMode
        : erLhcoreClassJitsi::MODE_VIDEO;

    $rawJwtMode = $form->hasValidData('jwt_mode') ? $form->jwt_mode : erLhcoreClassJitsi::JWT_MODE_NONE;
    $data['jwt_mode'] = in_array($rawJwtMode, [erLhcoreClassJitsi::JWT_MODE_NONE, erLhcoreClassJitsi::JWT_MODE_HS256, erLhcoreClassJitsi::JWT_MODE_JAAS_RS256], true)
        ? $rawJwtMode
        : erLhcoreClassJitsi::JWT_MODE_NONE;

    $data['jwt_ttl'] = $form->hasValidData('jwt_ttl') ? (int)$form->jwt_ttl : 3600;

    $rawDomain = $form->hasValidData('domain') ? trim((string)$form->domain) : '';
    if ($rawDomain === '' && $data['jwt_mode'] === erLhcoreClassJitsi::JWT_MODE_JAAS_RS256) {
        $data['domain'] = '8x8.vc';
    } elseif ($rawDomain !== '') {
        $normalizedDomain = erLhcoreClassJitsi::normalizeDomain($rawDomain);
        if ($normalizedDomain === '') {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Please enter a valid Jitsi server domain (e.g. meet.jit.si).');
        } else {
            $data['domain'] = $normalizedDomain;
        }
    } else {
        $data['domain'] = '';
        if ($data['enabled']) {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Jitsi server domain is required when integration is enabled.');
        }
    }

    $data['room_prefix'] = $form->hasValidData('room_prefix')
        ? erLhcoreClassJitsi::sanitizePrefix((string)$form->room_prefix)
        : 'lhc_';

    $data['jwt_app_id'] = $form->hasValidData('jwt_app_id') ? trim((string)$form->jwt_app_id) : '';
    if ($form->hasValidData('clear_jwt_app_secret') && $form->clear_jwt_app_secret == true) {
        $data['jwt_app_secret'] = '';
    } elseif ($form->hasValidData('jwt_app_secret') && trim((string)$form->jwt_app_secret) !== '') {
        $data['jwt_app_secret'] = trim((string)$form->jwt_app_secret);
    }

    $data['jaas_app_id'] = $form->hasValidData('jaas_app_id') ? trim((string)$form->jaas_app_id) : '';
    $data['jaas_kid'] = $form->hasValidData('jaas_kid') ? trim((string)$form->jaas_kid) : '';
    if ($form->hasValidData('clear_jaas_private_key') && $form->clear_jaas_private_key == true) {
        $data['jaas_private_key'] = '';
    } elseif ($form->hasValidData('jaas_private_key') && trim((string)$form->jaas_private_key) !== '') {
        $candidateKey = trim((string)$form->jaas_private_key);
        if (openssl_pkey_get_private($candidateKey) === false) {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'The provided JaaS RSA private key is not a valid PEM private key.');
        } else {
            $data['jaas_private_key'] = $candidateKey;
        }
    }

    if ($data['enabled'] && $data['jwt_mode'] === erLhcoreClassJitsi::JWT_MODE_HS256) {
        if ($data['jwt_app_id'] === '' || $data['jwt_app_secret'] === '') {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'HS256 JWT mode requires both App ID and App Secret.');
        }
    }

    if ($data['enabled'] && $data['jwt_mode'] === erLhcoreClassJitsi::JWT_MODE_JAAS_RS256) {
        if ($data['jaas_app_id'] === '' || $data['jaas_kid'] === '' || $data['jaas_private_key'] === '') {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'JaaS RS256 mode requires App ID, Key ID (kid), and RSA Private Key.');
        }
    }

    $allowedButtons = array_keys(erLhcoreClassJitsi::getAvailableToolbarButtons());
    $allowedSections = array_keys(erLhcoreClassJitsi::getAvailableSettingsSections());
    $roleBoolFlags = [
        'skip_prejoin',
        'start_with_audio_muted',
        'start_with_video_muted',
        'disable_shortcuts',
        'disable_profile',
        'disable_remote_mute',
        'disable_virtual_background',
        'disable_reactions',
        'disable_participants_pane',
        'hide_conference_subject',
        'hide_conference_timer',
        'hide_jitsi_hangup',
        'hide_jitsi_logo',
        'hide_profile_pic',
    ];

    foreach (['operator_options' => 'Operator', 'visitor_options' => 'Visitor'] as $roleKey => $roleLabel) {
        $rawRolePost = isset($_POST[$roleKey]) && is_array($_POST[$roleKey]) ? $_POST[$roleKey] : [];
        $parsedRole = [];

        $rawBtns = isset($rawRolePost['toolbar_buttons']) && is_array($rawRolePost['toolbar_buttons']) ? $rawRolePost['toolbar_buttons'] : [];
        $parsedRole['toolbar_buttons'] = array_values(array_unique(array_filter($rawBtns, static function ($b) use ($allowedButtons) {
            return is_string($b) && in_array($b, $allowedButtons, true);
        })));

        $rawSecs = isset($rawRolePost['settings_sections']) && is_array($rawRolePost['settings_sections']) ? $rawRolePost['settings_sections'] : [];
        $parsedRole['settings_sections'] = array_values(array_unique(array_filter($rawSecs, static function ($s) use ($allowedSections) {
            return is_string($s) && in_array($s, $allowedSections, true);
        })));

        foreach ($roleBoolFlags as $flag) {
            $parsedRole[$flag] = !empty($rawRolePost[$flag]);
        }

        $customJson = isset($rawRolePost['custom_config_json']) ? trim((string)$rawRolePost['custom_config_json']) : '';
        if ($customJson !== '') {
            $decoded = json_decode($customJson, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded) || array_is_list($decoded)) {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', $roleLabel . ' custom configOverwrite must be a valid JSON object.');
            }
        }
        $parsedRole['custom_config_json'] = $customJson;

        $data[$roleKey] = $parsedRole;
    }

    if (empty($Errors)) {
        $jitsiConfigObj->explain = '';
        $jitsiConfigObj->type = 0;
        $jitsiConfigObj->hidden = 1;
        $jitsiConfigObj->identifier = 'jitsi_configuration';
        $jitsiConfigObj->value = serialize($data);
        $jitsiConfigObj->saveThis();

        $CacheManager = erConfigClassLhCacheConfig::getInstance();
        $CacheManager->expireCache();

        $tpl->set('updated', 'done');
    } else {
        $tpl->set('errors', $Errors);
    }
}

$tpl->set('jitsi_data', $data);
$Result['content'] = $tpl->fetch();
$Result['path'] = [
    ['url' => erLhcoreClassDesign::baseurl('system/configuration'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration', 'System configuration')],
    ['title' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Jitsi Calls')],
];
