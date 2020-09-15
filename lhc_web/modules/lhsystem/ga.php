<?php

$tpl = erLhcoreClassTemplate::getInstance('lhsystem/ga.tpl.php');

$gaOptions = erLhcoreClassModelChatConfig::fetch('ga_options');
$data = (array)$gaOptions->data;

if ( isset($_POST['StoreOptions']) ) {

    $definition = array(
        'ga' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'ga_js' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )
    );

    $optionsEvents = array(
        'showWidget',
        'closeWidget',
        'openPopup',
        'endChat',
        'chatStarted',
        'offlineMessage',
        'showInvitation',
        'hideInvitation',
        'nhClicked',
        'nhClosed',
        'nhShow',
        'nhHide',
        'cancelInvitation',
        'fullInvitation',
        'readInvitation',
        'botTrigger',
    );

    foreach ($optionsEvents as $event){
        $definition[$event . '_category'] = new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        );
        $definition[$event . '_action'] = new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        );
        $definition[$event . '_label'] = new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        );
    }

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ( $form->hasValidData( 'ga' ) && $form->ga == true ) {
        $data['ga_enabled'] = 1;
    } else {
        $data['ga_enabled'] = 0;
    }
    
    if ( $form->hasValidData( 'ga_js' )) {
        $data['ga_js'] = $form->ga_js;
    } else {
        $data['ga_js'] = '';
    }

    foreach ($optionsEvents as $event) {

        if ($form->hasValidData( $event . '_category' )) {
            $data[$event . '_category'] = $form->{$event . '_category'};
        }

        if ($form->hasValidData( $event . '_action' )) {
            $data[$event . '_action'] = $form->{$event . '_action'};
        }

        if ($form->hasValidData( $event . '_label' )) {
            $data[$event . '_label'] = $form->{$event . '_label'};
        }
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
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('mobile/settings', 'Analytics')
    )
);

?>