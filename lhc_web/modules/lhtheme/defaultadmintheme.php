<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhtheme/defaultadmintheme.tpl.php' );

$themeData = erLhcoreClassModelChatConfig::fetch('default_admin_theme_id');

if (ezcInputForm::hasPostData()) {
		
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('theme/default');
		exit;
	}
	
	$definition = array(
			'ThemeID' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1)
			),
	);
	
	$form = new ezcInputForm( INPUT_POST, $definition );
	
	if ( $form->hasValidData( 'ThemeID' ) )	{
		$themeData->value = $form->ThemeID;
	} else {
		$themeData->value = 0;
	}

	$themeData->saveThis();
	
	// Cleanup cache to recompile templates etc.
	$CacheManager = erConfigClassLhCacheConfig::getInstance();
	$CacheManager->expireCache();
	
	$tpl->set('updated',true);	
}

$tpl->set('default_theme_id',$themeData->value);
$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('theme/index'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','Themes')),array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','Default admin theme')));
$Result['content'] = $tpl->fetch();