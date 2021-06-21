<?php

$tpl = erLhcoreClassTemplate::getInstance('lhvoicevideo/configuration.tpl.php');

$voiceData = erLhcoreClassModelChatConfig::fetch('vvsh_configuration');
$data = (array)$voiceData->data;

if (isset($_POST['StoreVoiceConfiguration'])) {
    $definition = array(
        'provider' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
        ),
        'agora_app_id' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
        ),
        'agora_app_token' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
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
    );

    $Errors = array();

    $form = new ezcInputForm(INPUT_POST, $definition);
    $Errors = array();

    if ($form->hasValidData('provider') && $form->provider != '') {
        $data['provider'] = $form->provider;
    } else {
        $data['provider'] = '';
    }

    if ($form->hasValidData('agora_app_id') && $form->agora_app_id != '') {
        $data['agora_app_id'] = $form->agora_app_id;
    } else {
        $data['agora_app_id'] = '';
    }

    if ($form->hasValidData('agora_app_token') && $form->agora_app_token != '') {
        $data['agora_app_token'] = $form->agora_app_token;
    } else {
        $data['agora_app_token'] = '';
    }

    if ($form->hasValidData('voice') && $form->voice == true) {
        $data['voice'] = true;
    } else {
        $data['voice'] = false;
    }

    if ($form->hasValidData('video') && $form->video == true) {
        $data['video'] = true;
    } else {
        $data['video'] = false;
    }

    if ($form->hasValidData('screenshare') && $form->screenshare == true) {
        $data['screenshare'] = true;
    } else {
        $data['screenshare'] = false;
    }

    if (empty($Errors)) {

        $voiceData->explain = '';
        $voiceData->type = 0;
        $voiceData->hidden = 1;
        $voiceData->identifier = 'vvsh_configuration';
        $voiceData->value = serialize($data);
        $voiceData->saveThis();

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
    array('url' => erLhcoreClassDesign::baseurl('file/configuration'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration', 'Voice & Video & ScreenShare')));

?>