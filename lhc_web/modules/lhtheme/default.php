<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhtheme/default.tpl.php' );

$themeData = erLhcoreClassModelChatConfig::fetch('default_theme_id');

if (ezcInputForm::hasPostData()) {
		
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('theme/default');
		exit;
	}
	
	$definition = array(
			'ThemeID' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1), FILTER_REQUIRE_ARRAY
			),
            'department_default' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
	);
	
	$form = new ezcInputForm( INPUT_POST, $definition );
	
	if ( $form->hasValidData( 'ThemeID' ) ) {
		$themeData->value = implode(',',$form->ThemeID);
	} else {
		$themeData->value = 0;
	}

    $themeData->saveThis();

    if ( $form->hasValidData( 'department_default' ) ) {
        foreach (erLhcoreClassModelDepartament::getList(['limit' => false]) as $item) {
            $configurationArray = $item->bot_configuration_array;
            $configurationArray['theme_default'] = $themeData->value;
            $item->bot_configuration_array = $configurationArray;
            $item->bot_configuration = json_encode($configurationArray);
            $item->updateThis(['update' => ['bot_configuration']]);
        }
    }

	// Cleanup cache to recompile templates etc.
	$CacheManager = erConfigClassLhCacheConfig::getInstance();
	$CacheManager->expireCache();
	
	$tpl->set('updated',true);
}

$tpl->set('default_theme_id',explode(',',$themeData->value));
$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','System configuration')),array('url' => erLhcoreClassDesign::baseurl('theme/index'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','Themes')),array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','Default theme')));
$Result['content'] = $tpl->fetch();