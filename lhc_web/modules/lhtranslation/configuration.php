<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhtranslation/configuration.tpl.php');

$translationData = erLhcoreClassModelChatConfig::fetch('translation_data');
$data = (array)$translationData->data;

if ( isset($_POST['DetectLanguage']) ) {
     try {
        $tpl->set('detected_language',erLhcoreClassTranslate::detectLanguage($_POST['DetectLanguageText']));
     } catch (Exception $e) {
        $tpl->set('errors',array($e->getMessage()));
     }
}

if ( isset($_POST['TranslateToLanguage']) ) {
     try {
        $tpl->set('translated_text',erLhcoreClassTranslate::translateTo($_POST['DetectLanguageText'], false, $_POST['LanguageTo']));
     } catch (Exception $e) {
        $tpl->set('errors',array($e->getMessage()));
     }
}

if ($Params['user_parameters_unordered']['action'] == 'clearcache') {

    if (!isset($Params['user_parameters_unordered']['csfr']) || !$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
        erLhcoreClassModule::redirect('translation/configuration');
        exit;
    }

    // Rest API Cache
    $q = ezcDbInstance::get()->createDeleteQuery();
    $q->deleteFrom('lh_generic_bot_rest_api_cache')->where( $q->expr->eq( 'rest_api_id', 0 ) );
    $stmt = $q->prepare();
    $stmt->execute();
    
    erLhcoreClassModule::redirect('translation/configuration');
    exit;
}

if ( isset($_POST['StoreLanguageSettings']) || isset($_POST['StoreLanguageSettingsTest']) ) {

    $definition = array(
        'translation_handler' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
        ),
        'enable_translations' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'use_cache' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'bing_client_secret' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'bing_region' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'google_api_key' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'google_referrer' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'yandex_api_key' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),

        'aws_region' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'aws_access_key' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'aws_secret_key' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
    );

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('translation/configuration');
        exit;
    }

    $Errors = array();

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ( $form->hasValidData( 'translation_handler' )) {
        $data['translation_handler'] = $form->translation_handler;
    } else {
        $data['translation_handler'] = 'bing';
    }

    if ( $form->hasValidData( 'enable_translations' ) && $form->enable_translations == true) {
        $data['enable_translations'] = true;
    } else {
        $data['enable_translations'] = false;
    }

    if ( $form->hasValidData( 'use_cache' ) && $form->use_cache == true) {
        $data['use_cache'] = true;
    } else {
        $data['use_cache'] = false;
    }
    
    if ( $form->hasValidData( 'bing_region' ) && $form->bing_region != '' && $form->bing_region != '0') {
        $data['bing_region'] = $form->bing_region;
    }

    if ( $form->hasValidData( 'bing_client_secret' ) && $form->bing_client_secret != '') {
        $data['bing_client_secret'] = $form->bing_client_secret;
    }

    if ( $form->hasValidData( 'google_api_key' ) && $form->google_api_key != '') {
        $data['google_api_key'] = $form->google_api_key;
    }

    if ( $form->hasValidData( 'google_referrer' ) && $form->google_referrer != '') {
        $data['google_referrer'] = $form->google_referrer;
    } else {
        $data['google_referrer'] = '';
    }

    if ( $form->hasValidData( 'aws_region' ) && $form->aws_region != '') {
        $data['aws_region'] = $form->aws_region;
    } else {
        $data['aws_region'] = '';
    }

    if ( $form->hasValidData( 'aws_access_key' ) && $form->aws_access_key != '') {
        $data['aws_access_key'] = $form->aws_access_key;
    }

    if ( $form->hasValidData( 'aws_secret_key' ) && $form->aws_secret_key != '') {
        $data['aws_secret_key'] = $form->aws_secret_key;
    }

    if ( $form->hasValidData( 'yandex_api_key' ) && $form->yandex_api_key != '') {
        $data['yandex_api_key'] = $form->yandex_api_key;
    }
    
    $translationData->value = serialize($data);
    $translationData->saveThis();

    if (isset($_POST['StoreLanguageSettingsTest'])){
        try {
            $tpl->set('message_send','done');
        } catch (Exception $e) {
            $tpl->set('errors',array($e->getMessage()));
        }
    }
    
    // Cleanup cache to recompile templates etc.
    $CacheManager = erConfigClassLhCacheConfig::getInstance();
    $CacheManager->expireCache();
    
    $tpl->set('updated','done');
}

$tpl->set('translation_data',$data);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Translation configuration')))

?>
