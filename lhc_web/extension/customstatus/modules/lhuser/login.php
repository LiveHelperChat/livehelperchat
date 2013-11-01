<?php

$tpl = erLhcoreClassTemplate::getInstance('lhuser/login.tpl.php');

if (isset($_POST['Login']))
{
    $currentUser = erLhcoreClassUser::instance();
    
    if (!$currentUser->authenticate($_POST['Username'],$_POST['Password']))
    {     
            $Error = erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Incorrect username or password');
            $tpl->set('error',$Error);   
    } else {    
        erLhcoreClassModule::redirect();
        return ;
    }    
}

$pagelayout = erConfigClassLhConfig::getInstance()->getOverrideValue('site','login_pagelayout');
if ($pagelayout != null)
$Result['pagelayout'] = $pagelayout;

$Result['content'] = $tpl->fetch();