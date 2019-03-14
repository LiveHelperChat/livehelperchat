<?php

$isExternalRequest = (isset($Params['user_parameters_unordered']['external_request'])) ? true : false;

$currentUser = erLhcoreClassUser::instance();

$instance = erLhcoreClassSystem::instance();

$possibleLoginSiteAccess = array();

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.login_site_access', array('loginSiteAccess' => & $possibleLoginSiteAccess));

$possibleLoginSiteAccess[] = 'site_admin';

if (!in_array($instance->SiteAccess, $possibleLoginSiteAccess)) {

    if ($currentUser->isLogged() && !empty($Params['user_parameters_unordered']['r'])) {
        header('Location: ' .erLhcoreClassDesign::baseurldirect('site_admin').'/'.base64_decode(rawurldecode($Params['user_parameters_unordered']['r'])));
        exit;
    }

    $redirect = rawurldecode($Params['user_parameters_unordered']['r']);
    $redirectFull = $redirect != '' ? '/(r)/'.rawurlencode($redirect) : '';

    header('Location: ' .erLhcoreClassDesign::baseurldirect('site_admin/user/login').$redirectFull );
    exit;
}

$tpl = erLhcoreClassTemplate::getInstance( 'lhuser/login.tpl.php');

$redirect = '';
if (isset($_POST['redirect'])){
    $redirect = $_POST['redirect'];
    $tpl->set('redirect_url',$redirect);
} else {
    $redirect = rawurldecode($Params['user_parameters_unordered']['r']);
    $tpl->set('redirect_url',$redirect);
}

if (isset($_POST['Login']))
{
    if (isset($_SESSION['logout_reason'])) {
        unset($_SESSION['logout_reason']);
    }

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        if($isExternalRequest) {
            $tpl->set('errors', array(erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','CSFR token is invalid, try to resubmit form')));
            echo json_encode(array('success' => false, 'result' => $tpl->fetch()));
            exit;
        }

        erLhcoreClassModule::redirect('user/login');
        exit;
    }

    $beforeLoginAuthenticateErrors = array();

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.login_before_authenticate', array('errors' => & $beforeLoginAuthenticateErrors, 'tpl' => & $tpl));

    if (!empty($beforeLoginAuthenticateErrors)) {
        $tpl->set('errors', $beforeLoginAuthenticateErrors);
        if($isExternalRequest) {
            echo json_encode(array('success' => false, 'result' => $tpl->fetch()));
            exit;
        }
    } else {

        $recaptchaData = erLhcoreClassModelChatConfig::fetch('recaptcha_data')->data_value;

        $valid = true;

        if (is_array($recaptchaData) && isset($recaptchaData['enabled']) && $recaptchaData['enabled'] == 1) {
           $params = [
                'secret' 	=> $recaptchaData['secret_key'],
                'response' 	=> $_POST['g-recaptcha']
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
            @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Some hostings produces wargning...
            $res = curl_exec($ch);

            $res 		= json_decode($res,true);

            if (!(isset($res['success']) && $res['success'] == 1 && isset($res['score']) && $res['score'] >= 0.1 && $res['action'] == 'login_action')) {
                $valid = false;
            }
        }

        if ($valid == false || !$currentUser->authenticate($_POST['Username'], $_POST['Password'], isset($_POST['rememberMe']) && $_POST['rememberMe'] == 1 ? true : false))
        {
            if ($valid == false) {
                $Error = erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Google re-captcha validation failed');
            } else {
                $Error = erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Incorrect username or password');
            }

            $tpl->set('errors',array($Error));
            if ($isExternalRequest) {
                echo json_encode(array('success' => false, 'result' => $tpl->fetch()));
                exit;
            }

        } else {
            
            $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.login_after_success_authenticate', array('current_user' => & $currentUser, 'tpl' => & $tpl));
            
            if ($response === false)
            {

                $passwordData = (array)erLhcoreClassModelChatConfig::fetch('password_data')->data;

                if (isset($passwordData['expires_in']) && $passwordData['expires_in'] > 0) {
                   $userData = $currentUser->getUserData();
                   if ($userData->pswd_updated < time()-($passwordData['expires_in']*24*3600)) {
                       $currentUser->logout();

                       $secretHash = erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' );
                       $ts = time()+600; // Visitor has 10 minutes to change password until link is expired
                       $hash = sha1($secretHash.sha1($secretHash.implode(',', array($userData->id,$ts))));

                       erLhcoreClassModule::redirect('user/updatepassword','/' . $userData->id . '/' . $ts . '/' . $hash);
                       exit;
                   }
                }

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.2fa_intercept', array('remember' => (isset($_POST['rememberMe']) && $_POST['rememberMe'] == 1),'is_external' => $isExternalRequest, 'current_user' => $currentUser));

                if ($isExternalRequest) {
                    $tpl->set('msg', erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Logged in successfully'));
                    echo json_encode(array('success' => true, 'result' => $tpl->fetch()));
                    exit;
                }

                if ($redirect != '') {
                    erLhcoreClassModule::redirect(base64_decode($redirect));
                } else {
                    erLhcoreClassModule::redirect();
                    exit;
                }
            }
        }

    }
}

if (isset($_SESSION['logout_reason'])) {
    if ($_SESSION['logout_reason'] == 1) {
        $tpl->set('logout_reason',$_SESSION['logout_reason']);
    }
}

if (isset($Params['user_parameters_unordered']['noaccess']) && $Params['user_parameters_unordered']['noaccess'] == true) {
    $tpl->set('session_ended',true);
}

$pagelayout = erConfigClassLhConfig::getInstance()->getOverrideValue('site','login_pagelayout');
if ($pagelayout != null)
    $Result['pagelayout'] = 'login';

$Result['content'] = $tpl->fetch();
