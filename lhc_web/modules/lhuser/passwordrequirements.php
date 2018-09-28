<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhuser/passwordrequirements.tpl.php');

$pswData = erLhcoreClassModelChatConfig::fetch('password_data');
$data = (array)$pswData->data;

if ( isset($_POST['StorePasswordSettings']) ) {
    $definition = array(
        'length' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        ),
        'uppercase_required' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'number_required' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'special_required' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'expires_in' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )
    );

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('user/autologinconfig');
        exit;
    }

    $Errors = array();

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ( $form->hasValidData( 'length' ) ) {
        $data['length'] = $form->length;
    } else {
        $data['length'] = 0;
    }

    if ( $form->hasValidData( 'expires_in' ) ) {
        $data['expires_in'] = $form->expires_in;
    } else {
        $data['expires_in'] = 0;
    }

    if ( $form->hasValidData( 'uppercase_required' ) && $form->uppercase_required == true ) {
        $data['uppercase_required'] = 1;
    } else {
        $data['uppercase_required'] = 0;
    }

    if ( $form->hasValidData( 'number_required' ) && $form->number_required == true ) {
        $data['number_required'] = 1;
    } else {
        $data['number_required'] = 0;
    }

    if ( $form->hasValidData( 'special_required' ) && $form->special_required == true ) {
        $data['special_required'] = 1;
    } else {
        $data['special_required'] = 0;
    }

    $pswData->value = serialize($data);
    $pswData->saveThis();

    $CacheManager = erConfigClassLhCacheConfig::getInstance();
    $CacheManager->expireCache();

    $tpl->set('updated','done');
}

$tpl->set('password_data',$data);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','System configuration')), array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Password requirements')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.autologinconfig_path', array('result' => & $Result));

?>