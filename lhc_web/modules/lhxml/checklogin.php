<?php

// Debug
//erLhcoreClassLog::write(print_r($_POST,true));

@ini_set('session.cookie_samesite', 'None');
@ini_set('session.cookie_secure', true);

$currentUser = erLhcoreClassUser::instance();

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