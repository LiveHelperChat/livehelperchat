<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmobile/settings.tpl.php');

$mbOptions = erLhcoreClassModelChatConfig::fetch('mobile_options');
$data = (array)$mbOptions->data;

if ( isset($_POST['StoreOptions']) ) {

    $definition = array(
        'fcm_key' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'notifications' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
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