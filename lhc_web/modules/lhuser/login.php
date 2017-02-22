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

        if (!$currentUser->authenticate($_POST['Username'], $_POST['Password'], isset($_POST['rememberMe']) && $_POST['rememberMe'] == 1 ? true : false))
        {
            $Error = erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Incorrect username or password');
            $tpl->set('errors',array($Error));
            if($isExternalRequest) {
                echo json_encode(array('success' => false, 'result' => $tpl->fetch()));
                exit;
            }
        } else {
            
            $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.login_after_success_authenticate', array('current_user' => & $currentUser, 'tpl' => & $tpl));
            
            if ($response === false)
            {
                if($isExternalRequest) {
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

$pagelayout = erConfigClassLhConfig::getInstance()->getOverrideValue('site','login_pagelayout');
if ($pagelayout != null)
    $Result['pagelayout'] = 'login';

$Result['content'] = $tpl->fetch();