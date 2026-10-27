<?php

if ((string)$Params['user_parameters_unordered']['action'] == 'statusdb' || (string)$Params['user_parameters_unordered']['action'] == 'statusdbdoupdate') {

	if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
		echo json_encode(array('error' => 'true', 'result' => 'Invalid CSRF Token' ));		
		exit;
	}

	$tpl = erLhcoreClassTemplate::getInstance( 'lhsystem/update/statusdb.tpl.php');
	
	if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == false && $Params['user_parameters_unordered']['scope'] !== 'local') {
	   $contentData = erLhcoreClassModelChatOnlineUser::executeRequest('https://raw.githubusercontent.com/LiveHelperChat/livehelperchat/master/lhc_web/doc/update_db/structure.json');
	} else {
       $Params['user_parameters_unordered']['scope'] = 'local';
	   $contentData = file_get_contents('doc/update_db/structure.json');
	}
	
    if ((string)$Params['user_parameters_unordered']['action'] == 'statusdbdoupdate') {
        erLhcoreClassUpdate::doTablesUpdate(json_decode($contentData,true));
        // Update extensions
        foreach (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'extensions' ) as $extension) {
            if (file_exists('extension/'.$extension.'/doc/structure.json')) {
                $contentDataExtension = file_get_contents('extension/'.$extension.'/doc/structure.json');
                erLhcoreClassUpdate::doTablesUpdate(json_decode($contentDataExtension,true));
            }
        }
    }

    $tables = erLhcoreClassUpdate::getTablesStatus(json_decode($contentData,true));

    // Handle extensions
    foreach (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'extensions' ) as $extension) {
        if (file_exists('extension/'.$extension.'/doc/structure.json')) {
            $contentDataExtension = file_get_contents('extension/'.$extension.'/doc/structure.json');
            $tablesExtension = erLhcoreClassUpdate::getTablesStatus(json_decode($contentDataExtension,true));
            $tables = array_merge_recursive($tables,$tablesExtension);
        }
    }

	$tpl->set('tables',$tables);
	$tpl->set('scope',$Params['user_parameters_unordered']['scope']);
	echo json_encode(array('result' => $tpl->fetch()));
	exit;
}

$tpl = erLhcoreClassTemplate::getInstance( 'lhsystem/update.tpl.php');

$configInstance = erConfigClassLhConfig::getInstance();

$autologinData = (array)erLhcoreClassModelChatConfig::fetch('autologin_data')->data;
$passwordData = (array)erLhcoreClassModelChatConfig::fetch('password_data')->data;
$mobileData = (array)erLhcoreClassModelChatConfig::fetch('mobile_options')->data;
$siteAddress = (string)$configInstance->getSetting( 'site', 'site_address', false );

try {
    $restApiKeys = erLhAbstractModelRestAPIKey::getCount(array('filter' => array('active' => 1)));
} catch (Exception $e) {
    $restApiKeys = 0;
}

$tpl->set('security_check', array(
    // Signed URL login bypassing password
    'autologin_enabled' => isset($autologinData['enabled']) && $autologinData['enabled'] == 1,
    'autologin_secret_length' => isset($autologinData['secret_hash']) ? strlen($autologinData['secret_hash']) : 0,
    'autologin_options' => isset($autologinData['autologin_options']) ? count($autologinData['autologin_options']) : 0,
    // Prints PHP errors/exceptions to visitors
    'debug_output' => $configInstance->getSetting( 'site', 'debug_output', false ) == true,
    // Back office protected by HTTPS
    'https' => strtolower((string)parse_url($siteAddress, PHP_URL_SCHEME)) === 'https',
    // Embedding forces SameSite=None session cookie
    'iframe_enabled' => $configInstance->getSetting( 'site', 'allow_iframe', false ) === true,
    'iframe_domain' => (string)$configInstance->getSetting( 'site', 'allow_iframe_domain', false ),
    // Password policy and brute force protection
    'password_length' => (int)(isset($passwordData['length']) ? $passwordData['length'] : 0),
    'password_max_attempts' => (int)(isset($passwordData['max_attempts']) ? $passwordData['max_attempts'] : 0),
    'password_expires_in' => (int)(isset($passwordData['expires_in']) ? $passwordData['expires_in'] : 0),
    'login_ip_restriction' => isset($passwordData['allow_login_from_ip']) && trim($passwordData['allow_login_from_ip']) != '',
    'one_login_per_account' => $configInstance->getSetting( 'site', 'one_login_per_account', false ) == true,
    // Default back office URL is well known
    'custom_admin_url' => !in_array('site_admin', (array)$configInstance->getSetting( 'site', 'default_admin_site_access', array('site_admin'))),
    // Rest API accepts username/password unless disabled
    'rest_api_keys' => (int)$restApiKeys,
    'rest_api_by_user_disabled' => $configInstance->getSetting( 'site', 'disable_rest_api_by_user', false ) === true,
    // Mobile application access (XML API used by the mobile app)
    'mobile_notifications' => isset($mobileData['notifications']) && $mobileData['notifications'] == 1,
));

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','System configuration')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Live Helper Chat update information'))
);


?>