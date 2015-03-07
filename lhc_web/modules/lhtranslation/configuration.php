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

if ( isset($_POST['StoreLanguageSettings']) || isset($_POST['StoreLanguageSettingsTest']) ) {

    $definition = array(
        'translation_handler' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
        ),
        'enable_translations' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'bing_client_id' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'bing_client_secret' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'google_api_key' => new ezcInputFormDefinitionElement(
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
    
    if ( $form->hasValidData( 'bing_client_id' )) {
        $data['bing_client_id'] = $form->bing_client_id;
    } else {
        $data['bing_client_id'] = '';
    }

    if ( $form->hasValidData( 'bing_client_secret' ) && $form->bing_client_secret != '') {
        $data['bing_client_secret'] = $form->bing_client_secret;
    }

    if ( $form->hasValidData( 'google_api_key' ) && $form->google_api_key != '') {
        $data['google_api_key'] = $form->google_api_key;
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