<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmobile/settings.tpl.php');

$mbOptions = erLhcoreClassModelChatConfig::fetch('mobile_options');
$data = (array)$mbOptions->data;

if ( isset($_POST['StoreOptions']) ) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('mobile/settings');
        exit;
    }

    $definition = array(
        'fcm_key' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'notifications' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'use_local_service_file' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'limit_p' => new ezcInputFormDefinitionElement( // Pending chats
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        ),
        'limit_a' => new ezcInputFormDefinitionElement(  // Active chats
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        ),
        'limit_c' => new ezcInputFormDefinitionElement(  // Closed chats
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        ),
        'limit_b' => new ezcInputFormDefinitionElement(  // Bot chats
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        ),
        'limit_ov' => new ezcInputFormDefinitionElement( // Online visitors
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        ),
        'limit_op' => new ezcInputFormDefinitionElement( // Online operators
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )
    );

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ( $form->hasValidData( 'notifications' ) && $form->notifications == true ) {
        $data['notifications'] = 1;
    } else {
        $data['notifications'] = 0;
    }

    if ( $form->hasValidData( 'fcm_key' )) {
        $data['fcm_key'] = $form->fcm_key ;
    } else {
        $data['fcm_key'] = '';
    }

    if ( $form->hasValidData( 'use_local_service_file' ) && $form->use_local_service_file == true ) {
        if ($data['use_local_service_file'] != 1) { // Reset FCM key so we generate it next time
            $data['fcm_key'] = '';
        }
        $data['use_local_service_file'] = 1;
    } else {
        if ($data['use_local_service_file'] != 0) { // Reset FCM key so we generate it next time
            $data['fcm_key'] = '';
        }
        $data['use_local_service_file'] = 0;
    }

    foreach (['limit_p','limit_a','limit_c','limit_b','limit_ov','limit_op'] as $field) {
        if ( $form->hasValidData( $field )) {
            $data[$field] = $form->$field ;
        } elseif (isset($data[$field])) {
            unset($data[$field]);
        }
    }

    $mbOptions->explain = '';
    $mbOptions->type = 0;
    $mbOptions->hidden = 1;
    $mbOptions->identifier = 'mobile_options';
    $mbOptions->value = serialize($data);
    $mbOptions->saveThis();

    $CacheManager = erConfigClassLhCacheConfig::getInstance();
    $CacheManager->expireCache(true);

    $tpl->set('updated','done');
}

$tpl->set('mb_options',$data);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array(
        'url' => erLhcoreClassDesign::baseurl('system/configuration'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('mobile/settings', 'Settings')
    ),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('mobile/settings', 'Mobile')
    )
);

?>