<?php

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.geoadjustment', array());

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/geoadjustment.tpl.php');

$geoData = erLhcoreClassModelChatConfig::fetch('geoadjustment_data');
$data = (array)$geoData->data;

if ( isset($_POST['SaveGeoAdjustment'])) {
	$definition = array(
			'use_geo_adjustment' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
			'AvailableFor' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'string'
			),
			'OtherCountries' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'string'
			),
			'HideFor' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'string'
			),
			'OtherStatus' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'string'
			),		
			'RestStatus' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'string'
			),
			'ApplyWidget' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			)
	);
	
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('xmp/configuration');
		exit;
	}
	
	$Errors = array();
	
	$form = new ezcInputForm( INPUT_POST, $definition );
	$Errors = array();
	
	if ( $form->hasValidData( 'use_geo_adjustment' )) {
		$data['use_geo_adjustment'] = true;
	} else {
		$data['use_geo_adjustment'] = false;
	}
	
	if ($data['use_geo_adjustment']){
		if ( $form->hasValidData( 'AvailableFor' )) {
			$data['available_for'] = $form->AvailableFor;
		} else {
			$data['available_for'] = '';
		}
		
		if ( $form->hasValidData( 'OtherCountries' )) {
			$data['other_countries'] = $form->OtherCountries;
		} else {
			$data['other_countries'] = '';
		}
		
		if ( $form->hasValidData( 'HideFor' )) {
			$data['hide_for'] = $form->HideFor;
		} else {
			$data['hide_for'] = '';
		}
		
		if ( $form->hasValidData( 'OtherStatus' )) {
			$data['other_status'] = $form->OtherStatus;
		} else {
			$data['other_status'] = '';
		}
		
		if ( $form->hasValidData( 'RestStatus' )) {
			$data['rest_status'] = $form->RestStatus;
		} else {
			$data['rest_status'] = '';
		}
		if ( $form->hasValidData( 'ApplyWidget' )) {
			$data['apply_widget'] = 1;
		} else {
			$data['apply_widget'] = 0;
		}
	}
	
	$geoData->value = serialize($data);
	$geoData->saveThis();

	$CacheManager = erConfigClassLhCacheConfig::getInstance();
	$CacheManager->expireCache();
	
	$tpl->set('updated','done');	
}

$tpl->set('geo_data',$data);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','System configuration')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/geoadjustment','GEO adjustment')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.geoadjustment_path',array('result' => & $Result));

?>