<?php

// Debug
//erLhcoreClassLog::write(print_r($_POST,true));

header('Content-type: application/json');

@ini_set('session.cookie_samesite', 'None');
@ini_set('session.cookie_secure', true);

$options = erLhcoreClassModelChatConfig::fetch('mobile_options')->data;

if (!isset($options['notifications']) || !$options['notifications']) {
    exit;
}

$currentUser = erLhcoreClassUser::instance();

// Regular login IP restrictions have to be respected here as well
$passwordData = (array)erLhcoreClassModelChatConfig::fetch('password_data')->data;

if (isset($passwordData['allow_login_from_ip']) && $passwordData['allow_login_from_ip'] != '' && !erLhcoreClassIPDetect::isIgnored(erLhcoreClassIPDetect::getIP(), explode(',', $passwordData['allow_login_from_ip']))) {

    $userToLogin = erLhcoreClassModelUser::findOne(array(
        'filterlor' => array(
            'username' => array((string)$_POST['username']),
            'email' => array((string)$_POST['username'])
        )
    ));

    // Some users are allowed to bypass IP restrictions
    $bypassIpUserIds = (isset($passwordData['bypass_ip_user_id']) && $passwordData['bypass_ip_user_id'] != '') ? explode(',', str_replace(' ', '', $passwordData['bypass_ip_user_id'])) : array();

    if (!($userToLogin instanceof erLhcoreClassModelUser) || !in_array((string)$userToLogin->id, $bypassIpUserIds)) {

        if ($userToLogin instanceof erLhcoreClassModelUser) {
            erLhcoreClassModelUserLogin::logUserAction(array(
                'type' => erLhcoreClassModelUserLogin::TYPE_LOGIN_ATTEMPT,
                'user_id' => $userToLogin->id,
                'msg' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Failed login. XML_CHECK_LOGIN. IP restrictions')
            ));
        }

        echo json_encode(
            array('result' => false)
        );

        exit;
    }
}

if ($currentUser->authenticate($_POST['username'],$_POST['password']))
{     
        echo json_encode(
            array('result' => true)
        );
          
} else {

    if (($userAttempt = erLhcoreClassModelUser::findOne(array('filter' => array('username' => $_POST['Username'])))) instanceof erLhcoreClassModelUser) {
        erLhcoreClassModelUserLogin::logUserAction(array(
            'type' => erLhcoreClassModelUserLogin::TYPE_LOGIN_ATTEMPT,
            'user_id' => $userAttempt->id,
            'msg' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Failed login. XML_CHECK_LOGIN')
        ));

        erLhcoreClassModelUserLogin::disableIfRequired($userAttempt);
    }

    echo json_encode(
            array('result' => false)
        );    
}
  

exit;
?>