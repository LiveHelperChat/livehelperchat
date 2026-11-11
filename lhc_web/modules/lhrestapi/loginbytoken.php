<?php

erLhcoreClassRestAPIHandler::setHeaders();

$modeAPI = isset($_GET['api']) && $_GET['api'] == 'true';

try {
    
    // Browser login starts a session without a permission check, so it's allowed only on a back office site access
    if ($modeAPI == false && erLhcoreClassUserUtils::validateLoginSiteAccess() == false) {
        throw new Exception('Invalid back office URL');
    }

    if (!isset($_GET['token']) || !is_string($_GET['token']) || trim($_GET['token']) === '') {
        throw new Exception('Token not found!');
    }

    $token = $_GET['token'];

    $uSession = erLhcoreClassModelUserSession::findOne(array(
        'filter' => array('token' => $token),
        // Never authenticate against a revoked/blank token left behind by logout
        'filternot' => array('token' => '')
    ));
        
    if ($uSession instanceof erLhcoreClassModelUserSession)
    {
        // Reject and revoke expired tokens so a leaked token can't be replayed forever
        if ($uSession->isExpired()) {
            $uSession->token = '';
            $uSession->updateThis();
            throw new Exception('Token expired');
        }

        $currentUser = erLhcoreClassUser::instance();
        $instance = erLhcoreClassSystem::instance();
               
        $userToLogin = erLhcoreClassModelUser::fetch((int)$uSession->user_id);           
        
        $r = '';
        if (isset($_GET['r'])) {
            $r = rawurldecode($_GET['r']);
        }

        if ($userToLogin instanceof erLhcoreClassModelUser) {

            if (erLhcoreClassUserUtils::fetchAutologinUser($userToLogin) === false) {
                throw new Exception('Login with token is not allowed for this user');
            }

            // Do not report a started session when the operator cannot actually be logged in
            // (e.g. a disabled account) - setLoggedUser() returns null when the visitor is
            // already logged in as that user, so compare strictly against false.
            if ($currentUser->setLoggedUser($userToLogin->id) === false) {
                throw new Exception('Session could not be started');
            }

            if ($modeAPI == false) {

                $configInstance = erConfigClassLhConfig::getInstance();

                $possibleLoginSiteAccess = array();

                $adminSiteAccess = $configInstance->getSetting('site', 'default_admin_site_access', false);

                if (is_array($adminSiteAccess)) {
                    $possibleLoginSiteAccess = $adminSiteAccess;
                } else {
                    $possibleLoginSiteAccess[] = 'site_admin';
                }

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.login_site_access', array('loginSiteAccess' => & $possibleLoginSiteAccess));

                header('Location: ' .erLhcoreClassDesign::baseurldirect('') . $possibleLoginSiteAccess[0] . '/'.ltrim($r, '/'));
                exit;
            } else {
                echo json_encode(array('error' => false, 'msg' => 'Session started', 'url' => erLhcoreClassSystem::getHost() . erLhcoreClassDesign::baseurldirect('site_admin').'/'.ltrim($r,'/')));
                exit;
            }
        } else {
            throw new Exception('User could not be found!');
        }

    } else {
        throw new Exception('Token not found');        
    }    

} catch (Exception $e) {
    
    if ($modeAPI == false) {
        http_response_code(400);
        die($e->getMessage());
    } else {
        http_response_code(400);
        echo json_encode(
            array('error' => true, 'msg' => $e->getMessage())
        );
    }
}

exit;
?>