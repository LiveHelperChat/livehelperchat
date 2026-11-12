<?php

header('Content-type: application/json');

$currentUser = erLhcoreClassUser::instance();

if (!$currentUser->isLogged()) {
    echo json_encode(array('error' => true, 'message' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey', 'Please login first')));
    exit;
}

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
    echo json_encode(array('error' => true, 'message' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey', 'Invalid CSRF Token')));
    exit;
}

if (!erLhcoreClassUserPasskey::isEnabled() || !$currentUser->hasAccessTo('lhuser', 'use_passkey')) {
    echo json_encode(array('error' => true, 'message' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey', 'You do not have permission to manage passkeys')));
    exit;
}

$action = (string)$Params['user_parameters']['action'];

$userData = $currentUser->getUserData();

/**
 * Raw JSON body sent by the browser
 */
$requestBody = function () {
    $data = json_decode(file_get_contents('php://input'), true);
    return is_array($data) ? $data : array();
};

try {

    switch ($action) {

        case 'reauth_options':
            if (count(erLhcoreClassUserPasskey::getPasskeyList($userData->id)) == 0) {
                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey', 'No passkeys are available'));
            }
            echo json_encode(array('error' => false, 'options' => erLhcoreClassUserPasskey::createAuthenticationOptions($userData)));
            break;

        case 'reauth':
            $body = $requestBody();
            unset($_SESSION['lhc_passkey_registration_reauth']);
            if (!isset($body['credential']) || !is_array($body['credential'])) {
                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey', 'Invalid request'));
            }
            $verifiedUser = erLhcoreClassUserPasskey::verifyAuthentication($body['credential']);
            if ((int)$verifiedUser->id !== (int)$userData->id) {
                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey', 'Authentication failed'));
            }
            $_SESSION['lhc_passkey_registration_reauth'] = array('user_id' => (int)$userData->id, 'time' => time());
            echo json_encode(array('error' => false));
            break;

        case 'register_options':
            $body = $requestBody();
            $reauth = isset($_SESSION['lhc_passkey_registration_reauth']) ? $_SESSION['lhc_passkey_registration_reauth'] : null;
            unset($_SESSION['lhc_passkey_registration_reauth']);
            $verified = is_array($reauth) && $reauth['user_id'] === (int)$userData->id && $reauth['time'] >= time() - erLhcoreClassUserPasskey::OPTIONS_TTL;
            if (!$verified && erLhcoreClassUserUtils::isPasswordLoginAllowed($userData) && isset($body['password']) && is_string($body['password']) && $body['password'] !== '') {
                $verified = strlen($userData->password) == 40
                    ? hash_equals($userData->password, sha1($body['password'] . erConfigClassLhConfig::getInstance()->getSetting('site', 'secrethash') . sha1($body['password'])))
                    : password_verify($body['password'], $userData->password);
                if (!$verified) {
                    erLhcoreClassModelUserLogin::logUserAction(array('type' => erLhcoreClassModelUserLogin::TYPE_LOGIN_ATTEMPT, 'user_id' => $userData->id, 'msg' => 'Failed passkey enrollment authentication'));
                    erLhcoreClassModelUserLogin::disableIfRequired($userData);
                }
            }
            if (!$verified) {
                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey', 'Authentication failed'));
            }
            echo json_encode(array(
                'error' => false,
                'options' => erLhcoreClassUserPasskey::createRegistrationOptions($userData, isset($body['name']) ? $body['name'] : '')
            ));
            break;

        case 'register':
            $body = $requestBody();

            if (!isset($body['credential']) || !is_array($body['credential'])) {
                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey', 'Invalid request'));
            }

            $passkey = erLhcoreClassUserPasskey::verifyRegistration($userData, $body['credential']);

            echo json_encode(array(
                'error' => false,
                'id' => $passkey->id,
                'name' => $passkey->name
            ));
            break;

        case 'delete':
            $passkey = erLhcoreClassModelUserPasskey::findOne(array(
                'filter' => array(
                    'id' => (int)$Params['user_parameters_unordered']['id'],
                    'user_id' => (int)$userData->id
                )
            ));

            if (!($passkey instanceof erLhcoreClassModelUserPasskey)) {
                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey', 'Passkey not found'));
            }

            // Removing the last passkey would lock the user out of his account
            if ($userData->passwordless_login == 1 && count(erLhcoreClassUserPasskey::getPasskeyList($userData->id)) <= 1) {
                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey', 'You can not delete your last passkey while passwordless login is enabled. Disable passwordless login first'));
            }

            $passkey->removeThis();

            echo json_encode(array('error' => false));
            break;

        default:
            throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey', 'Unknown action'));
    }

} catch (Throwable $e) {
    erLhcoreClassLog::write('Passkey action "' . $action . '" failed - ' . $e->getMessage());
    echo json_encode(array('error' => true, 'message' => $e->getMessage()));
}

exit;
?>
