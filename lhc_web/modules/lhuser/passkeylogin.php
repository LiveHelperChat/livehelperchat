<?php

$currentUser = erLhcoreClassUser::instance();
$action = (string)$Params['user_parameters']['action'];

if ($action === 'complete') {
    $pending = isset($_SESSION['lhc_passkey_login_pending']) ? $_SESSION['lhc_passkey_login_pending'] : null;
    unset($_SESSION['lhc_passkey_login_pending']);

    if (!erLhcoreClassUserUtils::validateLoginSiteAccess() || !erLhcoreClassUserPasskey::isEnabled() || !is_array($pending) || $pending['time'] < time() - erLhcoreClassUserPasskey::OPTIONS_TTL) {
        erLhcoreClassModule::redirect('user/login');
        exit;
    }

    $user = erLhcoreClassModelUser::fetch($pending['user_id'], false);
    if (!($user instanceof erLhcoreClassModelUser) || erLhcoreClassUserUtils::isApiKeyLoginOnly($user) || !erLhcoreClassUserUtils::isLoginIpAllowed($user) || ($currentUser->isLogged() && (int)$currentUser->getUserID() !== (int)$user->id)) {
        erLhcoreClassModule::redirect('user/login');
        exit;
    }

    $tpl = erLhcoreClassTemplate::getInstance('lhuser/login.tpl.php');
    $errors = array();
    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.login_before_authenticate', array('errors' => & $errors, 'tpl' => & $tpl));
    if (!empty($errors)) {
        erLhcoreClassModule::redirect('user/login');
        exit;
    }

    if (!$currentUser->isLogged() && $currentUser->setLoggedUser($user->id, false, true) !== true) {
        erLhcoreClassModule::redirect('user/login');
        exit;
    }

    try {
        $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.login_after_success_authenticate', array('current_user' => & $currentUser, 'tpl' => & $tpl));
        if ($response !== false) {
            $currentUser->logout();
            erLhcoreClassModule::redirect('user/login');
            exit;
        }

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.2fa_intercept', array('remember' => false, 'is_external' => false, 'current_user' => $currentUser));
    } catch (Throwable $e) {
        $currentUser->logout();
        throw $e;
    }

    erLhcoreClassModelUserLogin::logUserAction(array(
        'type' => erLhcoreClassModelUserLogin::TYPE_LOGGED,
        'user_id' => $user->id,
        'msg' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey', 'Logged in successfully. WEB. Passkey')
    ));
    $user->llogin = time();
    $user->force_logout = 0;
    $user->updateThis(array('update' => array('llogin', 'force_logout')));
    erLhcoreClassModule::redirect($pending['redirect']);
    exit;
}

header('Content-type: application/json');

// Passwordless login logs an operator in, so it's allowed only on a back office site access
if (!erLhcoreClassUserUtils::validateLoginSiteAccess()) {
    echo json_encode(array('error' => true, 'message' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/login', 'Invalid back office URL')));
    exit;
}

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
    echo json_encode(array('error' => true, 'message' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey', 'Invalid CSRF Token')));
    exit;
}

try {

    if ($action === 'options') {

        echo json_encode(array(
            'error' => false,
            'options' => erLhcoreClassUserPasskey::createAuthenticationOptions()
        ));
        exit;
    }

    if ($action === 'verify') {

        $body = json_decode(file_get_contents('php://input'), true);

        if (!is_array($body) || !isset($body['credential']) || !is_array($body['credential'])) {
            throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey', 'Invalid request'));
        }

        $user = erLhcoreClassUserPasskey::verifyAuthentication($body['credential']);

        if (erLhcoreClassUserUtils::isApiKeyLoginOnly($user)) {
            throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey', 'Passkey login is not allowed for this account'));
        }

        $redirectPath = '';

        if (!empty($body['redirect'])) {
            $requestedPath = base64_decode(rawurldecode($body['redirect']));
            if (erLhcoreClassUserUtils::isValidRedirectPath($requestedPath)) {
                $redirectPath = $requestedPath;
            }
        }

        $_SESSION['lhc_passkey_login_pending'] = array('user_id' => (int)$user->id, 'time' => time(), 'redirect' => $redirectPath);
        echo json_encode(array('error' => false, 'redirect' => erLhcoreClassDesign::baseurl('user/passkeylogin/complete')));
        exit;
    }

    throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey', 'Unknown action'));

} catch (Throwable $e) {
    erLhcoreClassLog::write('Passkey login action "' . $action . '" failed - ' . $e->getMessage());
    echo json_encode(array('error' => true, 'message' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey', 'Passkey login failed')));
}

exit;
?>
