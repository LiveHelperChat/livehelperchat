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
    $tab = 'generalsettings';
}

$cfgSite = erConfigClassLhConfig::getInstance();
$siteAccessAvailable = $cfgSite->getSetting( 'site', 'available_site_access' );

if (!in_array($input->siteaccess, $siteAccessAvailable)) {
    erLhcoreClassModule::redirect('system/languages');
    exit;
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

    $languagesValid = [];
    foreach (erLhcoreClassSiteaccessGenerator::getLanguages() as $language) {
        $languagesValid[] = $language['locale'];
    }

	if ( $form->hasValidData( 'language' ) && !empty($form->language) && in_array($form->language,$languagesValid)) {
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

		if ($form->hasValidData( 'siteaccess' ) && in_array($input->siteaccess, $siteAccessAvailable)) {
			$input->siteaccess = $form->siteaccess;
		} else {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Please choose a valid siteaccess');
        }

        $languagesValid = [];
        foreach (erLhcoreClassSiteaccessGenerator::getLanguages() as $language) {
            $languagesValid[] = $language['locale'];
        }

		if ( $form->hasValidData( 'language' ) && in_array($form->language,$languagesValid)) {
			$input->language = $form->language;
		} else {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Please choose a language');
        }

		if ( $form->hasValidData( 'theme' ) && $form->theme != '') {
			$input->theme = $form->theme;
		} else {
			$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Please enter theme');
		}

		if ( $form->hasValidData( 'module' ) && $form->module != '' && in_array('lh'.$form->module,array_keys(erLhcoreClassModules::getModuleList()))) {
			$input->module = $form->module;
		} else {
			$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Please enter module name');
		}

		if (empty($Errors) && $form->hasValidData( 'view' ) && $form->view != '' && in_array($form->view,erLhcoreClassModules::getViewsByModule('lh'.$form->module))) {
			$input->view = $form->view;
		} else {
			$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Please enter view name');
		}

		if ( empty($Errors) ) {

			erLhcoreClassSiteaccessGenerator::updateSiteAccess($input);

			// Clean cache
			$CacheManager = erConfigClassLhCacheConfig::getInstance();
			$CacheManager->expireCache();

			// Invalidate cache if opcache is used
			if (function_exists('opcache_invalidate')) {
                opcache_invalidate('settings/settings.ini.php');
            }

			// Redirect for change to take effect
			erLhcoreClassModule::redirect('system/languages','/(updated)/true/(sa)/'.$input->siteaccess);
	        exit;

		} else {
			$tpl->set('errors',$Errors);
		}
	}
}


$tpl->set('locales',$siteAccessAvailable);
$tpl->set('current_site_access',erLhcoreClassSystem::instance()->SiteAccess);
$tpl->set('input',$input);
$tpl->set('currentUser',$currentUser);
$tpl->set('tab',$tab);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Languages configuration')))

?>