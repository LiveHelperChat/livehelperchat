<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconvoauth/options.tpl.php');

$ms_options = erLhcoreClassModelChatConfig::fetch('mailconv_oauth_options');
$data = (array)$ms_options->data;

if ( isset($_POST['StoreOptions']) ) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('mailconv/optionsgeneral');
        exit;
    }

    $definition = array(
        'ms_tenant_id' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'ms_client_id' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'ms_secret' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )
    );

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ( $form->hasValidData( 'ms_tenant_id' ) ) {
        $data['ms_tenant_id'] = $form->ms_tenant_id;
    } else {
        $data['ms_tenant_id'] = '';
    }

    if ( $form->hasValidData( 'ms_client_id' )) {
        $data['ms_client_id'] = $form->ms_client_id;
    } else {
        $data['ms_client_id'] = '';
    }

    if ( $form->hasValidData( 'ms_secret' )) {
        $data['ms_secret'] = $form->ms_secret;
    } else {
        $data['ms_secret'] = '';
    }

    $ms_options->explain = '';
    $ms_options->type = 0;
    $ms_options->hidden = 1;
    $ms_options->identifier = 'mailconv_oauth_options';
    $ms_options->value = serialize($data);
    $ms_options->saveThis();

    $tpl->set('updated','done');
}

$tpl->set('ms_options',$data);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array(
        'url' => erLhcoreClassDesign::baseurl('system/configuration') . '#!#mailconv',
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('lhelasticsearch/module', 'System configuration')
    ),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('lhelasticsearch/module', 'Options')
    )
);

?>