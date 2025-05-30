<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/options.tpl.php');

$mcOptions = erLhcoreClassModelChatConfig::fetch('mailconv_options');
$data = (array)$mcOptions->data;

if ( isset($_POST['StoreOptions']) ) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('mailconv/options');
        exit;
    }

    $definition = array(
        'mce_plugins' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'mce_toolbar' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'reply_to_tmp' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'forward_to_tmp' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'image_skipped_text' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'disable_auto_owner' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'skip_images' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )
    );

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ( $form->hasValidData( 'mce_plugins' )) {
        $data['mce_plugins'] = $form->mce_plugins ;
    } else {
        $data['mce_toolbar'] = '';
    }

    if ( $form->hasValidData( 'reply_to_tmp' )) {
        $data['reply_to_tmp'] = $form->reply_to_tmp ;
    } else {
        $data['reply_to_tmp'] = '';
    }

    if ( $form->hasValidData( 'forward_to_tmp' )) {
        $data['forward_to_tmp'] = $form->forward_to_tmp ;
    } else {
        $data['forward_to_tmp'] = '';
    }

    if ( $form->hasValidData( 'mce_toolbar' )) {
        $data['mce_toolbar'] = $form->mce_toolbar ;
    } else {
        $data['mce_toolbar'] = '';
    }

    if ($form->hasValidData( 'disable_auto_owner' ) && $form->disable_auto_owner == true) {
        $data['disable_auto_owner'] = 1 ;
    } else {
        $data['disable_auto_owner'] = 0;
    }

    if ($form->hasValidData( 'skip_images' ) && $form->skip_images == true) {
        $data['skip_images'] = 1 ;
    } else {
        $data['skip_images'] = 0;
    }
    if ($form->hasValidData( 'image_skipped_text' ) &&  $form->image_skipped_text != '') {
        $data['image_skipped_text'] = $form->image_skipped_text;
    } else {
        $data['image_skipped_text'] = '[img]';
    }

    $mcOptions->explain = '';
    $mcOptions->type = 0;
    $mcOptions->hidden = 1;
    $mcOptions->identifier = 'mailconv_options';
    $mcOptions->value = serialize($data);
    $mcOptions->saveThis();

    $tpl->set('updated','done');
}

$tpl->set('mc_options',$data);

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