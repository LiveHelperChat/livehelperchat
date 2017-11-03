<?php

// Just extra security
header('X-Robots-Tag: noindex,nofollow');

$currentUser = erLhcoreClassUser::instance();
$instance = erLhcoreClassSystem::instance();

$data = erLhcoreClassModelChatConfig::fetch('autologin_data')->data;

$hash = $Params['user_parameters']['hash'];
$autologinConfiguration = false;

if (isset($data['autologin_options'])) {
    foreach ($data['autologin_options'] as $loginData) {
        if ($loginData['secret_hash'] != '' && $loginData['secret_hash'] == $hash && $loginData['ip'] != '' && erLhcoreClassIPDetect::isIgnored(erLhcoreClassIPDetect::getIP(),explode(',',$loginData['ip']))) {
            $autologinConfiguration = $loginData;
            break;
        }
    }
}

if (is_array($autologinConfiguration)) {
    try {
         $userToLogin = erLhcoreClassModelUser::fetch($autologinConfiguration['user_id']);
    } catch (Exception $e) {
        die($e->getMessage());
    }

    if ($userToLogin instanceof erLhcoreClassModelUser) {
        erLhcoreClassUser::instance()->setLoggedUser($userToLogin->id);
        header('Location: /' . $autologinConfiguration['site_access'] . '/' . $autologinConfiguration['url']);
        exit;
    } else {
        $tpl = erLhcoreClassTemplate::getInstance( 'lhkernel/validation_error_autologin_user.tpl.php');
        $tpl->set('errors',array(erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Could not find a user')));
        $Result['content'] = $tpl->fetch();
        $Result['pagelayout'] = 'login';
        return $Result;
    }
} else {
    $tpl = erLhcoreClassTemplate::getInstance( 'lhkernel/validation_error_autologin_user.tpl.php');
    $tpl->set('errors',array(erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Invalid autologin hash')));
    $Result['content'] = $tpl->fetch();
    $Result['pagelayout'] = 'login';
    return $Result;
}

?>