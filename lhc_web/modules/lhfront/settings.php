<?php

if ($Params['user_parameters_unordered']['action'] == 'reset') {

    if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
        die('Invalid CSFR Token');
        exit;
    }

    erLhcoreClassModelUserSetting::setSetting('dw_filters', '{}', false, true);
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
    echo json_encode(true);
    exit;
}

erLhcoreClassRestAPIHandler::setHeaders();

$payload = json_decode(file_get_contents('php://input'),true);

if (isset($payload['attr']) && is_string($payload['attr']) && $payload['attr'] != '' && key_exists('val',$payload)){

    // Exclude notifications icons
    $dwFilters = json_decode(erLhcoreClassModelUserSetting::getSetting('dw_filters', '{}', false, false, true),true );

    if ($dwFilters === null) {
        $dwFilters = [];
    }

    if ($payload['val'] === null) {
        if (isset($dwFilters[$payload['attr']])) {
            unset($dwFilters[$payload['attr']]);
        }
    } else {
        $dwFilters[$payload['attr']] = is_array($payload['val']) ? implode('/',$payload['val']) : $payload['val'];
    }

    erLhcoreClassModelUserSetting::setSetting('dw_filters', json_encode($dwFilters,JSON_FORCE_OBJECT), false, true);
}

echo json_encode(true);

exit;
?>