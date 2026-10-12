<?php

$templateOverride = 'lhchat/demo.tpl.php';

// Use demo pagelayout
$pagelayoutOverride = 'demo';
$demoMode = true;

// Disable online tracking for demo by session cookies
$_GET['cd'] = 1;

// Demo URL is public. Access is granted either to a logged in operator having the
// same required permission as before ("lhchat"/"use") or to an anonymous request
// providing a get parameter matching the E2E_SECRET environment variable.
$e2eSecret = getenv('E2E_SECRET');

$e2eSecretProvided = isset($_GET['e2e_secret'])
    ? $_GET['e2e_secret']
    : (isset($Params['user_parameters_unordered']['e2e_secret']) ? $Params['user_parameters_unordered']['e2e_secret'] : null);

$e2eAccessGranted = ($e2eSecret !== false && $e2eSecret !== '' && $e2eSecretProvided !== null && is_scalar($e2eSecretProvided) && hash_equals((string)$e2eSecret, (string)$e2eSecretProvided));

if ($e2eAccessGranted === false) {

    $currentUser = erLhcoreClassUser::instance();

    if ($currentUser->hasAccessTo('lhchat', array('use'), true) === false) {

        // Not logged in - redirect to the login page just like before
        if ($currentUser->isLogged() === false) {
            erLhcoreClassModule::redirect('user/login', '/(r)/' . rawurlencode(base64_encode(self::$currentModuleName . '/' . self::$currentView)));
            exit;
        }

        // Logged in, but without the required permission
        include_once('modules/lhkernel/nopermission.php');
        $Result['pagelayout'] = 'login';
        return;
    }
}

if ($Params['user_parameters_unordered']['department'] === null) {

    $startData = erLhcoreClassModelChatConfig::fetch('start_chat_data');
    $start_data_fields = $startDataFields = (array)$startData->data;

    if (isset($startDataFields['requires_dep']) && $startDataFields['requires_dep'] == true) {
        $department = erLhcoreClassModelDepartament::findOne(['filter' => ['disabled' => 0, 'archive' => 0]]);
        if (is_object($department)) {
            $Params['user_parameters_unordered']['department'] = [$department->id];
        }
    }
}

include 'modules/lhchat/start.php';

?>