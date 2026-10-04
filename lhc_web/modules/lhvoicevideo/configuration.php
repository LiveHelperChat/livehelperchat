<?php

$tpl = erLhcoreClassTemplate::getInstance('lhvoicevideo/configuration.tpl.php');

$voiceData = erLhcoreClassModelChatConfig::fetch('vvsh_configuration');
$data = (array)$voiceData->data;

if (isset($_POST['StoreVoiceConfiguration'])) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('voicevideo/configuration');
        exit;
    }

    $definition = array(
        'livekit_url' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
        ),
        'livekit_api_key' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
        ),
        'livekit_api_secret' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
        ),
        'token_ttl' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0, 'max_range' => 86400)
        ),
        'livekit_api_url' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
        ),
        'recording_mode' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
        ),
        'recording_audio_only' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'recording_egress_path' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
        ),
        'recording_storage_dir' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
        ),
        'recording_retention_days' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0, 'max_range' => 36500)
        ),
        'recording_notice' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'ring_timeout' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0, 'max_range' => 3600)
        ),
        'voice' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'video' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'screenshare' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'log_calls' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
    );

    $Errors = array();

    $form = new ezcInputForm(INPUT_POST, $definition);

    // Only self hosted LiveKit is supported. Remove settings of the former cloud provider.
    $data['provider'] = erLhcoreClassVoiceVideo::PROVIDER_LIVEKIT;
    unset($data['agora_app_id'], $data['agora_app_token']);

    $data['livekit_api_key'] = ($form->hasValidData('livekit_api_key') && $form->livekit_api_key != '') ? trim($form->livekit_api_key) : '';

    // Secret is never printed back to the browser. Empty value keeps the stored one.
    if (isset($_POST['livekit_api_secret_clear'])) {
        $data['livekit_api_secret'] = '';
    } elseif ($form->hasValidData('livekit_api_secret') && trim($form->livekit_api_secret) != '') {
        $data['livekit_api_secret'] = trim($form->livekit_api_secret);
    } elseif (!isset($data['livekit_api_secret'])) {
        $data['livekit_api_secret'] = '';
    }

    $data['livekit_url'] = '';
    if ($form->hasValidData('livekit_url') && trim($form->livekit_url) != '') {
        if (preg_match('/^(wss?|https?):\/\/[^\s]+$/i', trim($form->livekit_url))) {
            $data['livekit_url'] = rtrim(trim($form->livekit_url), '/');
        } else {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','LiveKit server URL has to start with wss:// or https://');
        }
    }

    $data['livekit_api_url'] = '';
    if ($form->hasValidData('livekit_api_url') && trim($form->livekit_api_url) != '') {
        if (preg_match('/^https?:\/\/[^\s]+$/i', trim($form->livekit_api_url))) {
            $data['livekit_api_url'] = rtrim(trim($form->livekit_api_url), '/');
        } else {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','LiveKit API URL has to start with http:// or https://');
        }
    }

    // Recording
    $data['recording_mode'] = ($form->hasValidData('recording_mode') && in_array($form->recording_mode, array(erLhcoreClassVoiceVideoRecording::MODE_OFF, erLhcoreClassVoiceVideoRecording::MODE_MANUAL, erLhcoreClassVoiceVideoRecording::MODE_AUTO))) ? $form->recording_mode : erLhcoreClassVoiceVideoRecording::MODE_OFF;
    $data['recording_audio_only'] = $form->hasValidData('recording_audio_only') && $form->recording_audio_only == true;
    $data['recording_retention_days'] = $form->hasValidData('recording_retention_days') ? (int)$form->recording_retention_days : 0;
    $data['recording_notice'] = $form->hasValidData('recording_notice') ? mb_substr(trim(strip_tags($form->recording_notice)), 0, 500) : '';

    foreach (array('recording_egress_path', 'recording_storage_dir') as $attr) {
        $value = $form->hasValidData($attr) ? rtrim(trim($form->{$attr}), '/') : '';
        if ($value != '' && (strpos($value, '..') !== false || !preg_match('/^\/[a-zA-Z0-9_\-\.\/]*$/', $value))) {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Recording directories have to be absolute paths');
            $value = '';
        }
        $data[$attr] = $value;
    }

    if ($data['recording_egress_path'] == '') {
        $data['recording_egress_path'] = '/out';
    }

    if ($data['recording_mode'] != erLhcoreClassVoiceVideoRecording::MODE_OFF && $data['recording_storage_dir'] == '') {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Recordings directory on this server is required when recording is enabled');
    } elseif ($data['recording_storage_dir'] != '' && !is_dir($data['recording_storage_dir'])) {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Recordings directory does not exist on this server');
    }

    $data['token_ttl'] = $form->hasValidData('token_ttl') ? (int)$form->token_ttl : 0;
    $data['ring_timeout'] = $form->hasValidData('ring_timeout') ? (int)$form->ring_timeout : 0;

    foreach (array('voice', 'video', 'screenshare', 'log_calls') as $attr) {
        $data[$attr] = $form->hasValidData($attr) && $form->{$attr} == true;
    }

    if ($data['voice'] == true && ($data['livekit_url'] == '' || $data['livekit_api_key'] == '' || $data['livekit_api_secret'] == '')) {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','LiveKit server URL, API key and API secret are required');
    }


    if (empty($Errors)) {

        $voiceData->explain = '';
        $voiceData->type = 0;
        $voiceData->hidden = 1;
        $voiceData->identifier = 'vvsh_configuration';
        $voiceData->value = serialize($data);
        $voiceData->saveThis();

        erLhcoreClassVoiceVideo::resetSettings();

        // Cleanup cache to recompile templates etc.
        $CacheManager = erConfigClassLhCacheConfig::getInstance();
        $CacheManager->expireCache();

        $tpl->set('updated', 'done');
    } else {
        $tpl->set('errors', $Errors);
    }

}

$tpl->set('voice_data', $data);
$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration', 'System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('voicevideo/configuration'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration', 'Voice & Video & ScreenShare')));

?>
