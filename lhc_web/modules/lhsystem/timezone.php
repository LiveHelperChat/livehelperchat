<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhsystem/timezone.tpl.php');

$cfgSite = erConfigClassLhConfig::getInstance();
$timezone = $cfgSite->getSetting( 'site', 'time_zone' );

if ( isset($_POST['StoreTimeZoneSettings']) ) {

	$definition = array(
			'TimeZone' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),			
	);

	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('system/timezone');
		exit;
	}

	$form = new ezcInputForm( INPUT_POST, $definition );

	if ( $form->hasValidData( 'TimeZone' ) ) {
		$timezone = $form->TimeZone;
	} else {
		$timezone = '';
	}

	if (isset($_POST['StoreTimeZoneSettings'])){		
		$cfgSite->setSetting('site', 'time_zone', $timezone);
		$cfgSite->save();
	}

	$tpl->set('updated','done');
}



$tpl->set('timezone',$timezone);
$Result['content'] = $tpl->fetch();

$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','System configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/timezone','Time zone')));


?>