<?php

header('X-Frame-Options: DENY');

$crossDomainCookie = false;

if (
    (isset($_GET['cookie']) && $_GET['cookie'] == 'crossdomain') ||
    (isset($_POST['cookie']) && $_POST['cookie'] == 'crossdomain')
) {
    @ini_set('session.cookie_samesite', 'None');
    @ini_set('session.cookie_secure', true);
    $crossDomainCookie = true;
}

$configInstance = erConfigClassLhConfig::getInstance();

$isExternalRequest = (isset($Params['user_parameters_unordered']['external_request'])) ? true : false;

$currentUser = erLhcoreClassUser::instance();

$possibleLoginSiteAccess = array();

$adminSiteAccess = $configInstance->getSetting('site', 'default_admin_site_access', false);

if (is_array($adminSiteAccess)) {
    $possibleLoginSiteAccess = $adminSiteAccess;
} else {
    $possibleLoginSiteAccess[] = 'site_admin';
}

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.login_site_access', array('loginSiteAccess' => & $possibleLoginSiteAccess));

// We want cookie to be cross domain
if ($currentUser->isLogged() && $crossDomainCookie === true && isset($_GET['ts']) && isset($_GET['token']) && ($_GET['ts'] > time() - 10 * 60) && sha1(erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' ).sha1(erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' ).'_external_login_' . $_GET['ts'])) == $_GET['token']) {
    $currentUser->logout();
    header('Location: ' .erLhcoreClassDesign::baseurldirect('') . $possibleLoginSiteAccess[0] . '/user/login'.'?cookie=crossdomain&logout=1');
    exit;
}

$instance = erLhcoreClassSystem::instance();

if (!in_array($instance->SiteAccess, $possibleLoginSiteAccess)) {
    if (!in_array('site_admin',$possibleLoginSiteAccess)) {
        $tpl = erLhcoreClassTemplate::getInstance( 'lhkernel/validation_error.tpl.php');
        $tpl->set('errors', [erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Invalid back office URL')]);
        $tpl->set('hideErrorButton',true);
        $Result['pagelayout'] = 'login';
        $Result['content'] = $tpl->fetch();
        return;
    } else {
        if ($currentUser->isLogged() && !empty($Params['user_parameters_unordered']['r'])) {
            header('Location: ' .erLhcoreClassDesign::baseurldirect('site_admin').'/'.base64_decode(rawurldecode($Params['user_parameters_unordered']['r'])));
            exit;
        }

        $redirect = rawurldecode($Params['user_parameters_unordered']['r']);
        $redirectFull = $redirect != '' ? '/(r)/'.rawurlencode($redirect) : '';

        header('Location: ' .erLhcoreClassDesign::baseurldirect('site_admin/user/login').$redirectFull );
        exit;
    }

} elseif ($currentUser->isLogged() && !empty($Params['user_parameters_unordered']['r'])) {
    header('Location: ' .erLhcoreClassDesign::baseurldirect('') . $possibleLoginSiteAccess[0] . '/'.base64_decode(rawurldecode($Params['user_parameters_unordered']['r'])));
    exit;
}

$tpl = erLhcoreClassTemplate::getInstance( 'lhuser/login.tpl.php');
$tpl->set('crossdomain',$crossDomainCookie);

$redirect = '';
if (isset($_POST['redirect'])){
    $redirect = $_POST['redirect'];
    $tpl->set('redirect_url',$redirect);
} else {
    $redirect = rawurldecode((string)$Params['user_parameters_unordered']['r']);
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
            @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Some hostings produces warning...
            $res = curl_exec($ch);

            $res = json_decode($res,true);

            if (!(isset($res['success']) && $res['success'] == 1 && isset($res['score']) && $res['score'] >= 0.1 && $res['action'] == 'login_action')) {
                $valid = false;
            }
        }

        // Check IP restrictions for login
        if ($valid == true) {
            $passwordData = (array)erLhcoreClassModelChatConfig::fetch('password_data')->data;

            // We have to check can particular user login by by-pasing ip restrictions
            if (isset($passwordData['allow_login_from_ip']) && $passwordData['allow_login_from_ip'] != '' && isset($passwordData['bypass_ip_user_id']) && !empty($passwordData['bypass_ip_user_id'])) {
                $userToLogin = erLhcoreClassModelUser::findOne(array(
                    'filterlor' => array(
                        'username' => array($_POST['Username']),
                        'email' => array($_POST['Username'])
                    )
                ));
                if ($userToLogin === false) {
                    $valid = false;
                    $validIP = false;
                } elseif (!in_array((string)$userToLogin->id,explode(',',str_replace(' ','',$passwordData['bypass_ip_user_id']))) && !erLhcoreClassIPDetect::isIgnored(erLhcoreClassIPDetect::getIP(),explode(',',$passwordData['allow_login_from_ip']))) {
                    $valid = false;
                    $validIP = false;
                }
            } elseif (isset($passwordData['allow_login_from_ip']) && $passwordData['allow_login_from_ip'] != '' && !erLhcoreClassIPDetect::isIgnored(erLhcoreClassIPDetect::getIP(),explode(',',$passwordData['allow_login_from_ip']))) {
                $valid = false;
                $validIP = false;
            }
        }

        $Error = '';
        $isThirdPartyLogin = false;

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.third_party_login', array(
            'username' => $_POST['Username'],
            'password' => $_POST['Password'],
            'error' => & $Error,
            'is_third_party_login' => & $isThirdPartyLogin,
            'tpl' => & $tpl));

        if ($isThirdPartyLogin === false && (!empty($Error) || $valid == false || !$currentUser->authenticate($_POST['Username'], $_POST['Password'], isset($_POST['rememberMe']) && $_POST['rememberMe'] == 1 ? true : false)))
        {
            if ($valid == false) {
                if (isset($validIP) && $validIP === false) {
                    $Error = erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','You can not login because of IP restrictions');
                } else {
                    $Error = erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Google re-captcha validation failed');
                }
            } else {

                if (empty($Error) && erLhcoreClassModelUser::getCount(array('filter' => array('disabled' => 1,'username' => $_POST['Username']))) > 0) {
                    $Error = erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Your account is disabled!');
                } else if (empty($Error)) {
                    $Error = erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Incorrect username or password');
                }

                if ($isThirdPartyLogin === false && ($userAttempt = erLhcoreClassModelUser::findOne(array('filter' => array('username' => $_POST['Username'])))) instanceof erLhcoreClassModelUser) {
                    erLhcoreClassModelUserLogin::logUserAction(array(
                        'type' => erLhcoreClassModelUserLogin::TYPE_LOGIN_ATTEMPT,
                        'user_id' => $userAttempt->id,
                        'msg' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Failed login. WEB')
                    ));

                    erLhcoreClassModelUserLogin::disableIfRequired($userAttempt);
                }
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
                $pendResetPassword = erLhcoreClassModelUserLogin::getCount(array('filter' => array(
                    'type' => erLhcoreClassModelUserLogin::TYPE_PASSWORD_RESET_REQUEST,
                    'status' => erLhcoreClassModelUserLogin::STATUS_PENDING,
                    'user_id' => $currentUser->getUserID()))) > 0;

                $userData = $currentUser->getUserData();

                if ((isset($passwordData['expires_in']) && $passwordData['expires_in'] > 0) || $pendResetPassword == true) {
                   if ($pendResetPassword == true || ($userData->pswd_updated < time()-($passwordData['expires_in']*24*3600))) {
                       $currentUser->logout();

                       $secretHash = erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' );
                       $ts = time()+600; // Visitor has 10 minutes to change password until link is expired
                       $hash = sha1($secretHash.sha1($secretHash.implode(',', array($userData->id,$ts))));

                       erLhcoreClassModule::redirect('user/updatepassword','/' . $userData->id . '/' . $ts . '/' . $hash);
                       exit;
                   }
                }

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.2fa_intercept', array('remember' => (isset($_POST['rememberMe']) && $_POST['rememberMe'] == 1),'is_external' => $isExternalRequest, 'current_user' => $currentUser));

                erLhcoreClassModelUserLogin::logUserAction(array(
                    'type' => erLhcoreClassModelUserLogin::TYPE_LOGGED,
                    'user_id' => $currentUser->getUserID(),
                    'msg' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Logged in successfully. WEB')
                ));

                $userData->llogin = time();
                $userData->force_logout = 0;
                $userData->updateThis(['update' => ['llogin','force_logout']]);

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
