<?php

$tpl = erLhcoreClassTemplate::getInstance('lhnotifications/settings.tpl.php');

$nSettings = erLhcoreClassModelChatConfig::fetch('notifications_settings');
$data = (array)$nSettings->data;

if ( isset($_POST['StoreOptions']) ) {

    $definition = array(
        'enabled' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'require_interaction' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'renotify' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'subject' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'icon' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'http_host' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'badge' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'vibrate' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'public_key' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'private_key' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )
    );

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ( $form->hasValidData( 'enabled' ) && $form->enabled == true ) {
        $data['enabled'] = 1;
    } else {
        $data['enabled'] = 0;
    }

    if ( $form->hasValidData( 'require_interaction' ) && $form->require_interaction == true ) {
        $data['require_interaction'] = 1;
    } else {
        $data['require_interaction'] = 0;
    }

    if ( $form->hasValidData( 'renotify' ) && $form->renotify == true ) {
        $data['renotify'] = 1;
    } else {
        $data['renotify'] = 0;
    }

    if ( $form->hasValidData( 'public_key' ) && $form->public_key != '' ) {
        $data['public_key'] = $form->public_key;
    } else {
        $data['public_key'] = '';
    }

    if ( $form->hasValidData( 'http_host' ) && $form->http_host != '' ) {
        $data['http_host'] = $form->http_host;
    } else {
        $data['http_host'] = '';
    }

    if ( $form->hasValidData( 'icon' ) && $form->icon != '' ) {
        $data['icon'] = $form->icon;
    } else {
        $data['icon'] = '';
    }

    if ( $form->hasValidData( 'badge' ) && $form->badge != '' ) {
        $data['badge'] = $form->badge;
    } else {
        $data['badge'] = '';
    }

    if ( $form->hasValidData( 'vibrate' ) && $form->vibrate != '' ) {
        $data['vibrate'] = $form->vibrate;
    } else {
        $data['vibrate'] = '';
    }

    if ( $form->hasValidData( 'subject' ) && $form->subject != '' ) {
        $data['subject'] = $form->subject;
    } else {
        $data['subject'] = '';
    }

    if ( $form->hasValidData( 'private_key' ) && $form->private_key != '' ) {
        $data['private_key'] = $form->private_key;
    }

    $nSettings->explain = '';
    $nSettings->type = 0;
    $nSettings->hidden = 1;
    $nSettings->identifier = 'notifications_settings';
    $nSettings->value = serialize($data);
    $nSettings->saveThis();
    
    // Cleanup cache to recompile templates etc.
    $CacheManager = erConfigClassLhCacheConfig::getInstance();
    $CacheManager->expireCache();

    $tpl->set('updated','done');
}

$tpl->set('n_settings',$data);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','System configuration')),
    array(
        'url' => erLhcoreClassDesign::baseurl('notifications/index'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/index', 'Notifications')
    ),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/index', 'Settings')
    )
);

?>