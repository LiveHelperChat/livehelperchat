<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhsystem/recaptcha.tpl.php');

$rcData = erLhcoreClassModelChatConfig::fetch('recaptcha_data');
$data = erLhcoreClassUserValidator::getCaptchaSettings();

if ( isset($_POST['StoreRecaptchaSettings']) ) {
    $definition = array(
        'provider' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'site_key' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'secret_key' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'turnstile_site_key' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'turnstile_secret_key' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'enabled' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )
    );

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('system/recaptcha');
        exit;
    }

    $Errors = array();

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ( $form->hasValidData( 'provider' ) && in_array($form->provider, array('google', 'turnstile'))) {
        $data['provider'] = $form->provider;
    } else {
        $data['provider'] = 'google';
    }

    if ( $form->hasValidData( 'site_key' )) {
        $data['site_key'] = trim($form->site_key);
    } else {
        $data['site_key'] = '';
    }

    if ($form->hasValidData( 'secret_key' ) && $form->secret_key != '') {
        $data['secret_key'] = trim($form->secret_key);
    }

    if ( $form->hasValidData( 'turnstile_site_key' )) {
        $data['turnstile_site_key'] = trim($form->turnstile_site_key);
    } else {
        $data['turnstile_site_key'] = '';
    }

    if ($form->hasValidData( 'turnstile_secret_key' ) && $form->turnstile_secret_key != '') {
        $data['turnstile_secret_key'] = trim($form->turnstile_secret_key);
    }

    if ( $form->hasValidData( 'enabled' ) && $form->enabled == 1) {
        $data['enabled'] = 1;
    } else {
        $data['enabled'] = 0;
    }

    $rcData->value = serialize($data);
    $rcData->saveThis();

    $CacheManager = erConfigClassLhCacheConfig::getInstance();
    $CacheManager->expireCache(true);

    $tpl->set('updated','done');
}

$tpl->set('rc_data',$data);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','System configuration')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Captcha settings')));

?>
