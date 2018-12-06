<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhuser/updatepassword.tpl.php');

$userId = (int)$Params['user_parameters']['user_id'];
$ts = (int)$Params['user_parameters']['ts'];
$hash = $Params['user_parameters']['hash'];

if ($ts > time()) {

    $secretHash = erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' );
    $hashToVerify = sha1($secretHash.sha1($secretHash.implode(',', array($userId,$ts))));

    if ($hashToVerify == $hash) {

        $user = erLhcoreClassModelUser::fetch($userId);

        if (isset($_POST['UpdatePassword'])) {

            $validRequest = true;
            if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
                if ($isExternalRequest) {
                    $tpl->set('errors', array(erTranslationClassLhTranslation::getInstance()->getTranslation('user/login', 'CSFR token is invalid, try to resubmit form')));
                    $validRequest = false;
                }
            }

            if (!($user instanceof erLhcoreClassModelUser)) {
                $tpl->set('errors', array(erTranslationClassLhTranslation::getInstance()->getTranslation('user/login', 'User could not be found!')));
                $validRequest = false;
            }

            if ($validRequest == true) {

                $Errors = erLhcoreClassUserValidator::validatePasswordChange($user, $Errors);

                if (count($Errors) == 0) {

                    erLhcoreClassUser::getSession()->update($user);
                                        
                    // Login user instantly as during password change he verified his logins
                    erLhcoreClassUser::instance()->setLoggedUser($user->id);

                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.2fa_intercept', array('current_user' => erLhcoreClassUser::instance()));
                    
                    $tpl->set('account_updated','done');

                }  else {
                    $tpl->set('errors',$Errors);
                }
            }
        }

    } else {
        $tpl->set('errors',array('Invalid hash!'));
    }
} else {
    $tpl->set('errors',array('Password update link has expired!'));
}

$tpl->set('hash',$hash);
$tpl->set('ts',$ts);
$tpl->set('userId',$userId);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','System configuration')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Update password')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.updatepassword', array('result' => & $Result));

$pagelayout = erConfigClassLhConfig::getInstance()->getOverrideValue('site','login_pagelayout');
if ($pagelayout != null)
    $Result['pagelayout'] = 'login';

?>