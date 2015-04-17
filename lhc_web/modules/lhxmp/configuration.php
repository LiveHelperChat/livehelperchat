<?php

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('xmp.configuration', array());

$tpl = erLhcoreClassTemplate::getInstance( 'lhxmp/xmp.tpl.php');

$xmpData = erLhcoreClassModelChatConfig::fetch('xmp_data');
$data = (array)$xmpData->data;

if (isset($_POST['StoreXMPGTalkSendeMessage'])) {	
	try {
        $definition = array(
            'test_recipients_gtalk' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'validate_email')
        );
        
        $form = new ezcInputForm(INPUT_POST, $definition);
        
        if ($form->hasValidData('test_recipients_gtalk')) {
            erLhcoreClassXMP::sendTestXMPGTalk($form->test_recipients_gtalk);
            $tpl->set('message_send','done');
            $tpl->set('test_gmail_email',$form->test_recipients_gtalk);
        } else {
            $tpl->set('errors',array(erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Invalid test e-mail address')));
        }
	    
	} catch (Exception $e) {
		$tpl->set('errors',array($e->getMessage()));
	}
}

if (isset($_POST['StoreXMPGTalkRevokeToken'])){
	try {
		erLhcoreClassXMP::revokeAccessToken();
		$tpl->set('token_revoked','done');
	} catch (Exception $e) {
		$tpl->set('errors',array($e->getMessage()));
	}
}

if ($Params['user_parameters_unordered']['gtalkoauth'] == 'true') {
	
	require_once 'lib/core/lhxmp/google/Google_Client.php';
	require_once 'lib/core/lhxmp/google/contrib/Google_Oauth2Service.php';
	
	$client = new Google_Client();
	$oauth2 = new Google_Oauth2Service($client);
	
	$client->setApplicationName('Live Helper Chat');
	$client->setScopes(array("https://www.googleapis.com/auth/googletalk","https://www.googleapis.com/auth/userinfo.email"));
	
	$client->setClientId($data['gtalk_client_id']);
	$client->setClientSecret($data['gtalk_client_secret']);
	$client->setApprovalPrompt('force');
	$client->setAccessType('offline');
	$client->setRedirectUri(erLhcoreClassXMP::getBaseHost().$_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurl('xmp/configuration').'/(gtalkoauth)/true');
		
	if (isset($_GET['code'])) {
		try {			
			$client->authenticate();		
			$data['gtalk_client_token'] = $client->getAccessToken();			
			$userGoogle = $oauth2->userinfo->get();
			$data['email_gtalk'] = $userGoogle['email'];
						
			$xmpData->value = serialize($data);
			$xmpData->saveThis();
			$tpl->set('token_received','done');
		} catch (Exception $e) {
			$tpl->set('errors',array($e->getMessage()));
		}
	} else {
		$tpl->set('errors',array('Could not receive a token!'));
	}
}




if ( isset($_POST['StoreXMPGTalkSettings']) || isset($_POST['StoreXMPGTalkSettingsTest'])) {
	$definition = array(
			'gtalk_client_id' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'gtalk_client_secret' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'XMPMessage' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'XMPAcceptedMessage' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),			
			'use_standard_xmp' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'int'
			),			
			'use_xmp' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'int'
			)
	);
	
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('xmp/configuration');
		exit;
	}
		
	$form = new ezcInputForm( INPUT_POST, $definition );
	$Errors = array();
	
	if ( $form->hasValidData( 'gtalk_client_id' )) {
		$data['gtalk_client_id'] = $form->gtalk_client_id;
	} else {
		$data['gtalk_client_id'] = '';
	}
	
	if ( $form->hasValidData( 'gtalk_client_secret' )) {
		$data['gtalk_client_secret'] = $form->gtalk_client_secret;
	} else {
		$data['gtalk_client_secret'] = '';
	}
	
	if ( $form->hasValidData( 'use_xmp' ) && $form->use_xmp == true ) {
		$data['use_xmp'] = 1;
	} else {
		$data['use_xmp'] = 0;
	}
	
	if ( $form->hasValidData( 'use_standard_xmp' )) {
		$data['use_standard_xmp'] = $form->use_standard_xmp;
	} else {
		$data['use_standard_xmp'] = 0;
	}
	
	if ( $form->hasValidData( 'XMPMessage' ) ) {
		$data['xmp_message'] = $form->XMPMessage;
	} else {
		$data['xmp_message'] = '';
	}
	
	if ( $form->hasValidData( 'XMPAcceptedMessage' ) ) {
		$data['xmp_accepted_message'] = $form->XMPAcceptedMessage;
	} else {
		$data['xmp_accepted_message'] = '';
	}
	
	$xmpData->value = serialize($data);
	$xmpData->saveThis();
	
	if (isset($_POST['StoreXMPGTalkSettingsTest'])) {
	
		require_once 'lib/core/lhxmp/google/Google_Client.php';
	
		$client = new Google_Client();
		$client->setApplicationName('Live Helper Chat');
		$client->setScopes(array("https://www.googleapis.com/auth/googletalk","https://www.googleapis.com/auth/userinfo.email"));
	
		// Documentation: http://code.google.com/apis/gdata/docs/2.0/basics.html
		// Visit https://code.google.com/apis/console?api=contacts to generate your
		// oauth2_client_id, oauth2_client_secret, and register your oauth2_redirect_uri.
		$client->setClientId($data['gtalk_client_id']);
		$client->setClientSecret($data['gtalk_client_secret']);
		$client->setApprovalPrompt('force');
		$client->setAccessType('offline');
		$client->setRedirectUri(erLhcoreClassXMP::getBaseHost().$_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurl('xmp/configuration').'/(gtalkoauth)/true');
			
		if ( !$client->getAccessToken() ) {
			header('Location: '.$client->createAuthUrl());
			exit;
		}
	}
	
	$tpl->set('updated','done');	
}


if ( isset($_POST['StoreXMPSettings']) || isset($_POST['StoreXMPSettingsTest']) ) {

	$definition = array(
			'host' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'username' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'password' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'port' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'use_xmp' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
			'resource' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),			
			'server' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'recipients' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'XMPMessage' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'XMPAcceptedMessage' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'test_recipients' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'test_group_recipients' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'use_standard_xmp' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'int'
			)
	);

	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('xmp/configuration');
		exit;
	}

	$Errors = array();

	$form = new ezcInputForm( INPUT_POST, $definition );
	$Errors = array();

	if ( $form->hasValidData( 'host' )) {
		$data['host'] = $form->host;
	} else {
		$data['host'] = '';
	}

	if ( $form->hasValidData( 'server' )) {
		$data['server'] = $form->server;
	} else {
		$data['server'] = '';
	}

	if ( $form->hasValidData( 'resource' )) {
		$data['resource'] = $form->resource;
	} else {
		$data['resource'] = '';
	}

	if ( $form->hasValidData( 'recipients' )) {
		$data['recipients'] = $form->recipients;
	} else {
		$data['recipients'] = '';
	}

	if ( $form->hasValidData( 'port' )) {
		$data['port'] = $form->port;
	} else {
		$data['port'] = '';
	}

	if ( $form->hasValidData( 'use_xmp' ) && $form->use_xmp == true ) {
		$data['use_xmp'] = 1;
	} else {
		$data['use_xmp'] = 0;
	}

	if ( $form->hasValidData( 'username' ) ) {
		$data['username'] = $form->username;
	} else {
		$data['username'] = '';
	}

	if ( $form->hasValidData( 'password' ) && $form->password != '' ) {
		$data['password'] = $form->password;
	}

	if ( $form->hasValidData( 'XMPMessage' ) ) {
		$data['xmp_message'] = $form->XMPMessage;
	} else {
		$data['xmp_message'] = '';
	}
	
	if ( $form->hasValidData( 'XMPAcceptedMessage' ) ) {
		$data['xmp_accepted_message'] = $form->XMPAcceptedMessage;
	} else {
		$data['xmp_accepted_message'] = '';
	}
	
	if ( $form->hasValidData( 'use_standard_xmp' )) {
		$data['use_standard_xmp'] = $form->use_standard_xmp;
	} else {
		$data['use_standard_xmp'] = 0;
	}
	
	if ( $form->hasValidData( 'test_recipients' )) {
		$data['test_recipients'] = $form->test_recipients;
	} else {
		$data['test_recipients'] = '';
	}
	if ( $form->hasValidData( 'test_group_recipients' )) {
		$data['test_group_recipients'] = $form->test_group_recipients;
	} else {
		$data['test_group_recipients'] = '';
	}
	
	$xmpData->value = serialize($data);
	$xmpData->saveThis();

	if (isset($_POST['StoreXMPSettingsTest'])){
		try {
			erLhcoreClassXMP::sendTestXMP($currentUser->getUserData());
			$tpl->set('message_send','done');
		} catch (Exception $e) {
			$tpl->set('errors',array($e->getMessage()));
		}
	}

	$tpl->set('updated','done');
}

$tpl->set('xmp_data',$data);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','System configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','XMPP settings')));

?>