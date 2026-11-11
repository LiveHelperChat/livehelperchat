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

        case 'register_options':
            $body = $requestBody();
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
