<?php

header('Content-type: application/json');

$currentUser = erLhcoreClassUser::instance();

// Passwordless login logs an operator in, so it's allowed only on a back office site access
if (!erLhcoreClassUserUtils::validateLoginSiteAccess()) {
    echo json_encode(array('error' => true, 'message' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/login', 'Invalid back office URL')));
    exit;
}

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
    echo json_encode(array('error' => true, 'message' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey', 'Invalid CSRF Token')));
    exit;
}

$action = (string)$Params['user_parameters']['action'];

try {

    if ($action === 'options') {

        $body = json_decode(file_get_contents('php://input'), true);

        $optionsUser = null;

        // When username is provided only passkeys of that user are allowed. Otherwise discoverable credentials are used
        if (is_array($body) && isset($body['username']) && trim((string)$body['username']) != '') {
            $optionsUser = erLhcoreClassModelUser::findOne(array(
                'filterlor' => array(
                    'username' => array(trim((string)$body['username'])),
                    'email' => array(trim((string)$body['username']))
                )
            ));

            if (!($optionsUser instanceof erLhcoreClassModelUser)) {
                $optionsUser = null;
            }
        }

        echo json_encode(array(
            'error' => false,
            'options' => erLhcoreClassUserPasskey::createAuthenticationOptions($optionsUser)
        ));
        exit;
    }

    if ($action === 'verify') {

        $body = json_decode(file_get_contents('php://input'), true);

        if (!is_array($body) || !isset($body['credential']) || !is_array($body['credential'])) {
            throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey', 'Invalid request'));
        }

        $user = erLhcoreClassUserPasskey::verifyAuthentication($body['credential']);

        // Login page is available also for already logged users
        $isPasskeyOwnerLoggedIn = $currentUser->isLogged() && (int)$currentUser->getUserID() === (int)$user->id;

        if ($isPasskeyOwnerLoggedIn == false) {

            if ($currentUser->isLogged()) {
                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey', 'Please log out first to login with a passkey'));
            }

            // Login without credentials is not allowed for this account
            if ($currentUser->setLoggedUser($user->id) !== true) {
                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey', 'Passkey login is not allowed for this account'));
            }
        }

        erLhcoreClassModelUserLogin::logUserAction(array(
            'type' => erLhcoreClassModelUserLogin::TYPE_LOGGED,
            'user_id' => $user->id,
            'msg' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey', 'Logged in successfully. WEB. Passkey')
        ));

        $user->llogin = time();
        $user->force_logout = 0;
        $user->updateThis(array('update' => array('llogin', 'force_logout')));

        $redirectUrl = erLhcoreClassDesign::baseurl('/');

        if (!empty($body['redirect'])) {
            $redirectPath = base64_decode(rawurldecode($body['redirect']));

            if (erLhcoreClassUserUtils::isValidRedirectPath($redirectPath)) {
                $redirectUrl = erLhcoreClassDesign::baseurl($redirectPath);
            }
        }

        echo json_encode(array('error' => false, 'redirect' => $redirectUrl));
        exit;
    }

    throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey', 'Unknown action'));

} catch (Throwable $e) {
    erLhcoreClassLog::write('Passkey login action "' . $action . '" failed - ' . $e->getMessage());
    echo json_encode(array('error' => true, 'message' => $e->getMessage()));
}

exit;
?>
