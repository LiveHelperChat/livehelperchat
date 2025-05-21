<?php

header('X-Frame-Options: DENY');

$tpl = erLhcoreClassTemplate::getInstance( 'lhuser/forgotpassword.tpl.php');

$possibleLoginSiteAccess = array();

$configInstance = erConfigClassLhConfig::getInstance();

$adminSiteAccess = $configInstance->getSetting('site', 'default_admin_site_access', false);

if (is_array($adminSiteAccess)) {
    $possibleLoginSiteAccess = $adminSiteAccess;
} else {
    $possibleLoginSiteAccess[] = 'site_admin';
}

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.login_site_access', array('loginSiteAccess' => & $possibleLoginSiteAccess));

$instance = erLhcoreClassSystem::instance();

if (!in_array($instance->SiteAccess, $possibleLoginSiteAccess)) {
    $tpl = erLhcoreClassTemplate::getInstance( 'lhkernel/validation_error.tpl.php');
    $tpl->set('errors', [erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Invalid back office URL')]);
    $tpl->set('hideErrorButton',true);
    $Result['pagelayout'] = 'login';
    $Result['content'] = $tpl->fetch();
    return;
}

$currentUser = erLhcoreClassUser::instance();

if (isset($_POST['Forgotpassword'])) {

	$definition = array(
        'Email' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'validate_email'
        )
    );

    $form = new ezcInputForm( INPUT_POST, $definition );

    $Errors = array();
    
    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('user/forgotpassword');
        exit;
    }
    
    if ( !$form->hasValidData( 'Email' ) )
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/forgotpassword','Invalid e-mail address!');
    }

    $recaptchaData = erLhcoreClassModelChatConfig::fetch('recaptcha_data')->data_value;

    if (is_array($recaptchaData) && isset($recaptchaData['enabled']) && $recaptchaData['enabled'] == 1) {
        $params = [
            'secret'    => $recaptchaData['secret_key'],
            'response'  => $_POST['g-recaptcha']
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$params);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Some hostings produces warning...
        $res = curl_exec($ch);

        $res = json_decode($res,true);

        if (!(isset($res['success']) && $res['success'] == 1 && isset($res['score']) && $res['score'] >= 0.1 && $res['action'] == 'login_action')) {
            $Errors[] = 'Invalid recaptcha!';
        }
    }

	if (count($Errors) == 0) {

		if (($userID = erLhcoreClassModelUser::fetchUserByEmail($form->Email)) !== false) {

            $host = erConfigClassLhConfig::getInstance()->getSetting( 'site', 'site_address', false);

            if (!empty($host)) {
                $host = erLhcoreClassSystem::getHost();
            }

			$adminEmail = erConfigClassLhConfig::getInstance()->getSetting( 'site', 'site_admin_email' );

			$UserData = erLhcoreClassUser::getSession()->load( 'erLhcoreClassModelUser', $userID );

			$hash = erLhcoreClassModelForgotPassword::randomPassword(40);

			erLhcoreClassModelForgotPassword::setRemindHash($UserData->id,$hash);

			$mail = new PHPMailer();
			$mail->CharSet = "UTF-8";
			$mail->From = $adminEmail;

			$mail->FromName = erConfigClassLhConfig::getInstance()->getSetting( 'site', 'title' );
			$mail->Subject = erTranslationClassLhTranslation::getInstance()->getTranslation('user/forgotpassword','Password remind');

			// HTML body
			$body  = erTranslationClassLhTranslation::getInstance()->getTranslation('user/forgotpassword','Click this link and You will be able to change a password').' </br><a href="' . $host . erLhcoreClassDesign::baseurl('user/remindpassword').'/'.$hash.'">Restore password</a>';

			// Plain text body
			$text_body  = erTranslationClassLhTranslation::getInstance()->getTranslation('user/forgotpassword','Click this link and You will be able to change a password').' - ' . $host . erLhcoreClassDesign::baseurl('user/remindpassword').'/'.$hash;

			$mail->Body    = $body;
			$mail->AltBody = $text_body;
			$mail->AddAddress( $UserData->email, $UserData->username);

			erLhcoreClassChatMail::setupSMTP($mail);

			$mail->Send();
			$mail->ClearAddresses();

			$tpl = erLhcoreClassTemplate::getInstance( 'lhuser/forgotpasswordsent.tpl.php');

		} else {
            $tpl = erLhcoreClassTemplate::getInstance( 'lhuser/forgotpasswordsent.tpl.php');
		}

    }  else {
        $tpl->set('errors',$Errors);
    }
}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'login';

?>