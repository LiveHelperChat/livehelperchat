<?php

$tpl = erLhcoreClassTemplate::getInstance('lhsystem/ga.tpl.php');

$gaOptions = erLhcoreClassModelChatConfig::fetch('ga_options');
$data = (array)$gaOptions->data;

if ( isset($_POST['StoreOptions']) ) {

    $definition = array(
        'ga' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'ga_all' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'ga_js' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'js_static' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'ga_dep' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1),FILTER_REQUIRE_ARRAY)
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
        'clickAction',
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
        $definition[$event . '_on'] = new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        );
    }

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ($form->hasValidData( 'ga' ) && $form->ga == true ) {
        $data['ga_enabled'] = 1;
    } else {
        $data['ga_enabled'] = 0;
    }

    if ($form->hasValidData( 'ga_all' ) && $form->ga_all == true ) {
        $data['ga_all'] = 1;
    } else {
        $data['ga_all'] = 0;
    }
    
    if ($form->hasValidData( 'ga_js' )) {
        $data['ga_js'] = $form->ga_js;
    } else {
        $data['ga_js'] = '';
    }

    if ($form->hasValidData( 'js_static' )) {
        $data['js_static'] = $form->js_static;
    } else {
        $data['js_static'] = '';
    }

    if ($form->hasValidData( 'ga_dep' )) {
        $data['ga_dep'] = $form->ga_dep;
    } else {
        $data['ga_dep'] = [];
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

        if ($form->hasValidData( $event . '_on' ) && $form->hasValidData( $event . '_on' ) == true) {
            $data[$event . '_on'] = 1;
        } else {
            $data[$event . '_on'] = 0;
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
$tpl->set('tab','');

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array(
        'url' => erLhcoreClassDesign::baseurl('system/configuration'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/settings', 'System configuration')
    ),
    array(
        'url' => erLhcoreClassDesign::baseurl('chatsettings/eventindex'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Events tracking')
    ),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Default settings')
    )
);

?>