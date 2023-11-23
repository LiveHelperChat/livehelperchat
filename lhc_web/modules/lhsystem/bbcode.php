<?php

$tpl = erLhcoreClassTemplate::getInstance('lhsystem/bbcode.tpl.php');

$bbcodeOptions = erLhcoreClassModelChatConfig::fetch('bbcode_options');
$data = (array)$bbcodeOptions->data;

if (!isset($data['div'])) {
    $data['div'] = [];
}

if (!isset($data['dio'])) {
    $data['dio'] = [];
}

if ( isset($_POST['StoreOptions']) ) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('system/bbcode');
        exit;
    }

    $definition = array(
        'enabled_visitor' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'string', null,FILTER_REQUIRE_ARRAY),
        'enabled_operator' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'string', null,FILTER_REQUIRE_ARRAY)
    );

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ($form->hasValidData('enabled_visitor')) {
        $data['div'] = $form->enabled_visitor;
    } else {
        $data['div'] = [];
    }

    if ($form->hasValidData('enabled_operator')) {
        $data['dio'] = $form->enabled_operator;
    } else {
        $data['dio'] = [];
    }

    $bbcodeOptions->explain = '';
    $bbcodeOptions->type = 0;
    $bbcodeOptions->hidden = 1;
    $bbcodeOptions->identifier = 'bbcode_options';
    $bbcodeOptions->value = serialize($data);
    $bbcodeOptions->saveThis();

    $CacheManager = erConfigClassLhCacheConfig::getInstance();
    $CacheManager->expireCache(true);

    $tpl->set('updated','done');
}

$tpl->set('bbcode_options',$data);
$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array(
        'url' => erLhcoreClassDesign::baseurl('system/configuration'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/settings', 'System configuration')
    ),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'BBCode configuration')
    )
);

?>