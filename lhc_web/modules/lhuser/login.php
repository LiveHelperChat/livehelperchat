<?php

$instance = erLhcoreClassSystem::instance();

if ($instance->SiteAccess == erConfigClassLhConfig::getInstance()->getSetting( 'site', 'default_site_access' )) {
    header('Location: ' .erLhcoreClassDesign::baseurldirect('site_admin/user/login') );
    exit;
}

$tpl = erLhcoreClassTemplate::getInstance( 'lhuser/login.tpl.php');

if (isset($_POST['Login']))
{
    $currentUser = erLhcoreClassUser::instance();

    if (!$currentUser->authenticate($_POST['Username'], $_POST['Password'], isset($_POST['rememberMe']) && $_POST['rememberMe'] == 1 ? true : false))
    {
            $Error = erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Incorrect username or password');
            $tpl->set('errors',array($Error));
    } else {
        erLhcoreClassModule::redirect();
        exit;
    }
}

$pagelayout = erConfigClassLhConfig::getInstance()->getOverrideValue('site','login_pagelayout');
if ($pagelayout != null)
$Result['pagelayout'] = 'login';

$Result['content'] = $tpl->fetch();