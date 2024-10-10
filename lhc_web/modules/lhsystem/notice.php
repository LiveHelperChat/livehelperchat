<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhsystem/notice.tpl.php');

$esOptions = erLhcoreClassModelChatConfig::fetch('notice_message');
$data = (array)$esOptions->data;

if ( isset($_POST['StoreUserSettingsAction']) ) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('system/notice');
        exit;
    }

    $definition = array(
        'message' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'level' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
        )
    );

    $Errors = array();

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();


    if ( $form->hasValidData( 'message' ) ) {
        $data['message'] = $form->message;
    } else {
        $data['message'] = '';
    }

    if ($form->hasValidData( 'level') && in_array($form->level,['primary','warning','danger','success'])) {
        $data['level'] = $form->level;
    } else {
        $data['level'] = '';
    }

    $esOptions->explain = '';
    $esOptions->type = 0;
    $esOptions->hidden = 1;
    $esOptions->identifier = 'notice_message';
    $esOptions->value = serialize($data);
    $esOptions->saveThis();

    $tpl->set('updated','done');
}

$tpl->set('data',$data);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Notice message')))

?>