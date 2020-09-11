<?php

$tpl = erLhcoreClassTemplate::getInstance('lhsystem/ga.tpl.php');

$gaOptions = erLhcoreClassModelChatConfig::fetch('ga_options');
$data = (array)$gaOptions->data;

if ( isset($_POST['StoreOptions']) ) {

    $definition = array(
        'fcm_key' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'ga' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )
    );

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ( $form->hasValidData( 'ga' ) && $form->ga == true ) {
        $data['ga_enabled'] = 1;
    } else {
        $data['ga_enabled'] = 0;
    }

    if ( $form->hasValidData( 'fcm_key' )) {
        $data['fcm_key'] = $form->fcm_key ;
    } else {
        $data['fcm_key'] = '';
    }

    $gaOptions->explain = '';
    $gaOptions->type = 0;
    $gaOptions->hidden = 1;
    $gaOptions->identifier = 'ga_options';
    $gaOptions->value = serialize($data);
    $gaOptions->saveThis();

    $CacheManager = erConfigClassLhCacheConfig::getInstance();
    $CacheManager->expireCache(true);

    $tpl->set('updated','done');
}

$tpl->set('ga_options',$data);

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