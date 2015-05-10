<?php

$currentUser = erLhcoreClassUser::instance();

$instance = erLhcoreClassSystem::instance();



if ($instance->SiteAccess != 'site_admin') {

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
        erLhcoreClassModule::redirect('user/login');
        exit;
    }
    
    if (!$currentUser->authenticate($_POST['Username'], $_POST['Password'], isset($_POST['rememberMe']) && $_POST['rememberMe'] == 1 ? true : false))
    {
        $Error = erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Incorrect username or password');
        $tpl->set('errors',array($Error));
    } else {
    	if ($redirect != '') {
    		erLhcoreClassModule::redirect(base64_decode($redirect));
    	} else {
	        erLhcoreClassModule::redirect();
	        exit;
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