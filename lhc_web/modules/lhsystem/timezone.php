<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhsystem/timezone.tpl.php');

$cfgSite = erConfigClassLhConfig::getInstance();
$timezone = $cfgSite->getSetting( 'site', 'time_zone' );

$date_format = $cfgSite->getSetting( 'site', 'date_format' );
$date_hour_format = $cfgSite->getSetting( 'site', 'date_hour_format' );
$date_date_hour_format = $cfgSite->getSetting( 'site', 'date_date_hour_format' );

if ( isset($_POST['StoreTimeZoneSettings']) ) {

	$definition = array(
			'TimeZone' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),			
			'DateFormat' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),			
			'DateFullFormat' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),			
			'DateHourFormat' => new ezcInputFormDefinitionElement(
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

	if ( $form->hasValidData( 'DateFormat' ) ) {
		$date_format = $form->DateFormat;
	} else {
		$date_format = '';
	}

	if ( $form->hasValidData( 'DateFullFormat' ) ) {
		$date_date_hour_format = $form->DateFullFormat;
	} else {
		$date_date_hour_format = '';
	}

	if ( $form->hasValidData( 'DateHourFormat' ) ) {
		$date_hour_format = $form->DateHourFormat;
	} else {
		$date_hour_format = '';
	}

	if (isset($_POST['StoreTimeZoneSettings'])){		
		$cfgSite->setSetting('site', 'time_zone', $timezone);
		$cfgSite->setSetting('site', 'date_format', $date_format);
		$cfgSite->setSetting('site', 'date_hour_format', $date_hour_format);
		$cfgSite->setSetting('site', 'date_date_hour_format', $date_date_hour_format);		
		$cfgSite->save();
	}

	$tpl->set('updated','done');
}

$tpl->set('timezone',$timezone);
$tpl->set('date_format',$date_format);
$tpl->set('date_hour_format',$date_hour_format);
$tpl->set('date_date_hour_format',$date_date_hour_format);



$Result['content'] = $tpl->fetch();

$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','System configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/timezone','Time zone')));


?>