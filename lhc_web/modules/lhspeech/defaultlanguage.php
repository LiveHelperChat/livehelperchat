<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhspeech/defaultlanguage.tpl.php' );

$speechData = erLhcoreClassModelChatConfig::fetch('speech_data');
$data = (array)$speechData->data;

if (ezcInputForm::hasPostData()) {
		
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('speech/defaultlanguage');
		exit;
	}
	
	$definition = array(
			'select_language' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1)
			),
			'select_dialect' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'string'
			),
	);
	
	$form = new ezcInputForm( INPUT_POST, $definition );
	
	if ( $form->hasValidData( 'select_language' ) )	{
		$data['language'] = $form->select_language;
	} else {
		$data['language'] = '';
	}
	
	if ( $form->hasValidData( 'select_dialect' ) )	{
		$data['dialect'] = $form->select_dialect;
	} else {
		$data['dialect'] = 'en-US';
	}
		
	$speechData->value = serialize($data);
	$speechData->saveThis();
			
	$tpl->set('updated',true);
	
	// Cleanup cache to recompile templates etc.
	$CacheManager = erConfigClassLhCacheConfig::getInstance();
	$CacheManager->expireCache();
}

$tpl->set('dataSpeech',$data);
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','System configuration')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('speech/language','Default speech recognition language')));
    
$Result['content'] = $tpl->fetch();

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.speech_defaultlanguage_path',array('result' => & $Result));
