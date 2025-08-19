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
        ),
        'download_view_mode' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', ['min_range' => 0, 'max_range' => 2]
        ),
        'file_download_mode' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', ['min_range' => 0, 'max_range' => 1]
        ),
        'allowed_extensions_public' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'allowed_extensions_restricted' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'restricted_message' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )
    );

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ( $form->hasValidData( 'mce_plugins' )) {
        $data['mce_plugins'] = $form->mce_plugins ;
    } else {
        $data['mce_toolbar'] = '';
    }

    if ($form->hasValidData( 'download_view_mode' )) {
        $data['download_view_mode'] = $form->download_view_mode ;
    } else {
        $data['download_view_mode'] = 0;
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

    if ($form->hasValidData( 'file_download_mode' )) {
        $data['file_download_mode'] = $form->file_download_mode ;
    } else {
        $data['file_download_mode'] = 0;
    }

    if ($form->hasValidData( 'allowed_extensions_public' )) {
        $data['allowed_extensions_public'] = $form->allowed_extensions_public ;
    } else {
        $data['allowed_extensions_public'] = '';
    }

    if ($form->hasValidData( 'allowed_extensions_restricted' )) {
        $data['allowed_extensions_restricted'] = $form->allowed_extensions_restricted ;
    } else {
        $data['allowed_extensions_restricted'] = '';
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