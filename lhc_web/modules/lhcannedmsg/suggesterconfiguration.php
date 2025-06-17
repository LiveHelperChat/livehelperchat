<?php

$tpl = erLhcoreClassTemplate::getInstance('lhcannedmsg/suggesterconfiguration.tpl.php');

$scOptions = erLhcoreClassModelChatConfig::fetch('canned_suggester_settings');
$data = (array)$scOptions->data;

if (isset($_POST['StoreOptions']) ) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('cannedmsg/suggesterconfiguration');
        exit;
    }

    $definition = array(
        'first_n_letters' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1, 'max_range' => 5)
        ),
        'min_percentage' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0,'max_range' => 90)
        ),
        'top_n_match' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1,'max_range' => 5)
        ),
        'max_result' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 50, 'max_range' => 5000)
        )
    );

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ($form->hasValidData( 'first_n_letters' )) {
        $data['first_n_letters'] = $form->first_n_letters;
    } else {
        $data['first_n_letters'] = 1;
    }

    if ($form->hasValidData( 'min_percentage' )) {
        $data['min_percentage'] = $form->min_percentage;
    } else {
        $data['min_percentage'] = 0;
    }

    if ($form->hasValidData( 'max_result' )) {
        $data['max_result'] = $form->max_result;
    } else {
        $data['max_result'] = 50;
    }

    if ($form->hasValidData( 'top_n_match' )) {
        $data['top_n_match'] = $form->top_n_match;
    } else {
        $data['top_n_match'] = 1;
    }

    $scOptions->explain = '';
    $scOptions->type = 0;
    $scOptions->hidden = 1;
    $scOptions->identifier = 'canned_suggester_settings';
    $scOptions->value = serialize($data);
    $scOptions->saveThis();
    $tpl->set('updated','done');
}

$tpl->set('sc_options',$data);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','System configuration')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Canned messages suggester configuration')));

?>