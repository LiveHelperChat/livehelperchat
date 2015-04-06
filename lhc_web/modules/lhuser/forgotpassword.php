<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhuser/forgotpassword.tpl.php');

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

	if (count($Errors) == 0) {

		if (($userID = erLhcoreClassModelUser::fetchUserByEmail($form->Email)) !== false) {

			$host = $_SERVER['HTTP_HOST'];

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
			$body  = erTranslationClassLhTranslation::getInstance()->getTranslation('user/forgotpassword','Click this link and You will be sent a new password').' </br><a href="http://'.$host.erLhcoreClassDesign::baseurl('user/remindpassword').'/'.$hash.'">Restore password</a>';

			// Plain text body
			$text_body  = erTranslationClassLhTranslation::getInstance()->getTranslation('user/forgotpassword','Click this link and You will be sent a new password').' - http://'.$host.erLhcoreClassDesign::baseurl('user/remindpassword').'/'.$hash;

			$mail->Body    = $body;
			$mail->AltBody = $text_body;
			$mail->AddAddress( $UserData->email, $UserData->username);

			erLhcoreClassChatMail::setupSMTP($mail);

			$mail->Send();
			$mail->ClearAddresses();

			$tpl = erLhcoreClassTemplate::getInstance( 'lhuser/forgotpasswordsent.tpl.php');

		} else {
			erLhcoreClassModule::redirect('user/forgotpassword');
			exit;
		}
    }  else {
        $tpl->set('errors',$Errors);
    }
}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'login';

?>