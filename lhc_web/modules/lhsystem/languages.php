<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhsystem/languages.tpl.php');

if ((string)$Params['user_parameters_unordered']['updated'] == 'true'){
	$tpl->set('updated',true);
}

$input = new stdClass();
$input->siteaccess = erLhcoreClassSystem::instance()->SiteAccess;
$tab = '';

if ((string)$Params['user_parameters_unordered']['sa'] != ''){
	$input->siteaccess = (string)$Params['user_parameters_unordered']['sa'];
	$tab = 'generalsettings';
}

if (isset($_POST['changeSiteAccess'])) {
	$input->siteaccess = $_POST['siteaccess'];
}

if ( isset($_POST['StoreUserSettingsAction']) ) {
	$definition = array(
			'language' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'string'
			)
	);

	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('system/languages');
		exit;
	}

	$Errors = array();

	$form = new ezcInputForm( INPUT_POST, $definition );
	$Errors = array();

	if ( $form->hasValidData( 'language' ) && !empty($form->language)) {
		erLhcoreClassModelUserSetting::setSetting('user_language',$form->language);

		// Redirect for change to take effect
		erLhcoreClassModule::redirect('system/languages','/(updated)/true');
		exit;

	} else {
		$tpl->set('errors', array(erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Please choose correct language')));
	}
}

if ($currentUser->hasAccessTo('lhsystem','configurelanguages')){
	if (isset($_POST['StoreLanguageSettings'])) {
		$tab = 'generalsettings';
		$definition = array(
				'siteaccess' => new ezcInputFormDefinitionElement(
						ezcInputFormDefinitionElement::OPTIONAL, 'string'
				),
				'language' => new ezcInputFormDefinitionElement(
						ezcInputFormDefinitionElement::OPTIONAL, 'string'
				),
				'theme' => new ezcInputFormDefinitionElement(
						ezcInputFormDefinitionElement::OPTIONAL, 'string'
				),
				'module' => new ezcInputFormDefinitionElement(
						ezcInputFormDefinitionElement::OPTIONAL, 'string'
				),
				'view' => new ezcInputFormDefinitionElement(
						ezcInputFormDefinitionElement::OPTIONAL, 'string'
				)
		);

		if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
			erLhcoreClassModule::redirect('system/languages');
			exit;
		}

		$Errors = array();

		$form = new ezcInputForm( INPUT_POST, $definition );
		$Errors = array();

		if ( $form->hasValidData( 'siteaccess' )) {
			$input->siteaccess = $form->siteaccess;
		}

		if ( $form->hasValidData( 'language' )) {
			$input->language = $form->language;
		}

		if ( $form->hasValidData( 'theme' ) && $form->theme != '') {
			$input->theme = $form->theme;
		} else {
			$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Please enter theme');
		}

		if ( $form->hasValidData( 'module' ) && $form->module != '') {
			$input->module = $form->module;
		} else {
			$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Please enter module name');
		}

		if ( $form->hasValidData( 'view' ) && $form->view != '') {
			$input->view = $form->view;
		} else {
			$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Please enter view name');
		}

		if ( empty($Errors) ) {

			erLhcoreClassSiteaccessGenerator::updateSiteAccess($input);

			// Clean cache
			$CacheManager = erConfigClassLhCacheConfig::getInstance();
			$CacheManager->expireCache();

			// Redirect for change to take effect
			erLhcoreClassModule::redirect('system/languages','/(updated)/true/(sa)/'.$input->siteaccess);
	        exit;

		} else {
			$tpl->set('errors',$Errors);
		}
	}
}

$cfgSite = erConfigClassLhConfig::getInstance();
$tpl->set('locales',$cfgSite->getSetting( 'site', 'available_site_access' ));
$tpl->set('current_site_access',erLhcoreClassSystem::instance()->SiteAccess);
$tpl->set('input',$input);
$tpl->set('currentUser',$currentUser);
$tpl->set('tab',$tab);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Languages configuration')))

?>