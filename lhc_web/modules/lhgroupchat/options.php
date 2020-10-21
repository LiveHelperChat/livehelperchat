<?php

$tpl = erLhcoreClassTemplate::getInstance('lhgroupchat/options.tpl.php');

$gcOptions = erLhcoreClassModelChatConfig::fetch('groupchat_options');
$data = (array)$gcOptions->data;


if ( isset($_POST['StoreOptions']) ) {

    $definition = array(
        'supervisor' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int'
        )
    );

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ( $form->hasValidData( 'supervisor' ) ) {
        $data['supervisor'] = $form->supervisor;
    } else {
        $data['supervisor'] = 0;
    }

    $gcOptions->explain = '';
    $gcOptions->type = 0;
    $gcOptions->hidden = 1;
    $gcOptions->identifier = 'groupchat_options';
    $gcOptions->value = serialize($data);
    $gcOptions->saveThis();

    $CacheManager = erConfigClassLhCacheConfig::getInstance();
    $CacheManager->expireCache(true);

    $tpl->set('updated','done');
}

$tpl->set('gc_options',$data);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array(
        'url' => erLhcoreClassDesign::baseurl('system/configuration'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('mobile/settings', 'Settings')
    ),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('groupchat/settings', 'Options')
    )
);

?>