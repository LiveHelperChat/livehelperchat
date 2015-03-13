<?php 

// Just extra security
header('X-Robots-Tag: noindex,nofollow');

$currentUser = erLhcoreClassUser::instance();
$instance = erLhcoreClassSystem::instance();

if ($instance->SiteAccess != 'site_admin') {

    if ($currentUser->isLogged() && !empty($Params['user_parameters_unordered']['r'])) {
        header('Location: ' .erLhcoreClassDesign::baseurldirect('site_admin').'/'.base64_decode(rawurldecode($Params['user_parameters_unordered']['r'])));
        exit;
    }

    $redirect = base64_decode(rawurldecode($Params['user_parameters_unordered']['r']));
    $redirectFull = $redirect != '' ? '/(r)/'.rawurlencode(base64_encode($redirect)) : '';

    $redirect = rawurldecode($Params['user_parameters_unordered']['u']);
    $redirectFull .= $redirect != '' ? '/(u)/'.rawurlencode($redirect) : '';

    $redirect = rawurldecode($Params['user_parameters_unordered']['l']);
    $redirectFull .= $redirect != '' ? '/(l)/'.rawurlencode($redirect) : '';

    $redirect = rawurldecode($Params['user_parameters_unordered']['t']);
    $redirectFull .= $redirect != '' ? '/(t)/'.rawurlencode($redirect) : '';

    $redirectHash = rawurlencode(rawurldecode($Params['user_parameters']['hash']));
        
    header('Location: ' .erLhcoreClassDesign::baseurldirect('site_admin/user/autologin').'/'.$redirectHash.$redirectFull );
    exit;
}

$data = erLhcoreClassModelChatConfig::fetch('autologin_data')->data;

if ($data['enabled'] == 1) {
    
    $dataRequest = array(
        'r' => base64_decode(rawurldecode($Params['user_parameters_unordered']['r'])),
        'u' => rawurldecode($Params['user_parameters_unordered']['u']),
        'l' => rawurldecode($Params['user_parameters_unordered']['l']),
        't' => rawurldecode($Params['user_parameters_unordered']['t']),
    );

    $dataRequest = array_filter($dataRequest);
    
    $validateHash = sha1($data['secret_hash'].sha1($data['secret_hash'].implode(',', $dataRequest)));
    
    if ($validateHash == $Params['user_parameters']['hash']) {
        
        if ($dataRequest['t'] > 0 && $dataRequest['t'] < time()) {
            die(erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Autologin hash has expired'));
        }

        try {
            if (is_numeric($dataRequest['u'])){
                $userToLogin = erLhcoreClassModelUser::fetch((int)$dataRequest['u']);
            } else {
                $users = erLhcoreClassModelUser::getUserList(array('limit' => 1,'filter' => array('username' => $dataRequest['l'])));
                if (!empty($users)) {
                    $userToLogin = array_shift($users);
                } else {
                    die(erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Could not find a user'));
                }
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
        
        if ($userToLogin instanceof erLhcoreClassModelUser) {
            erLhcoreClassUser::instance()->setLoggedUser($userToLogin->id);
            header('Location: ' .erLhcoreClassDesign::baseurldirect('site_admin').'/'.ltrim($dataRequest['r'],'/'));
            exit;            
        } else {
            die(erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Could not find a provided user'));
        }        
    } else {
        die(erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Invalid autologin hash'));
        exit;
    }
    
} else {
    die(erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Auto login module is not enabled'));
    exit;
}

exit;
?>