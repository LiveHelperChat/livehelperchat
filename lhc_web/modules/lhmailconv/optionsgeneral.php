<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/optionsgeneral.tpl.php');

$mcOptions = erLhcoreClassModelChatConfig::fetch('mailconv_options_general');
$data = (array)$mcOptions->data;

if ( isset($_POST['StoreOptions']) ) {

    $definition = array(
        'active_lang_detect' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'lang_url' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'lang_provider' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
        )
    );

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ( $form->hasValidData( 'lang_provider' )) {
        $data['lang_provider'] = $form->lang_provider ;
    } else {
        $data['lang_provider'] = '';
    }

    if ( $form->hasValidData( 'lang_url' )) {
        $data['lang_url'] = $form->lang_url;
    } else {
        $data['lang_url'] = '';
    }

    if ($form->hasValidData( 'active_lang_detect' ) && $form->active_lang_detect == true) {
        $data['active_lang_detect'] = 1 ;
    } else {
        $data['active_lang_detect'] = 0;
    }

    $mcOptions->explain = '';
    $mcOptions->type = 0;
    $mcOptions->hidden = 1;
    $mcOptions->identifier = 'mailconv_options_general';
    $mcOptions->value = serialize($data);
    $mcOptions->saveThis();

    $tpl->set('updated','done');
}

$tpl->set('general_options',$data);

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