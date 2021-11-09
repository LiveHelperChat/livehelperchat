<?php

erLhcoreClassRestAPIHandler::setHeaders();

$payload = json_decode(file_get_contents('php://input'),true);

if (isset($payload['attr']) && is_string($payload['attr']) && $payload['attr'] != '' && key_exists('val',$payload)){

    // Exclude notifications icons
    $dwFilters = json_decode(erLhcoreClassModelUserSetting::getSetting('dw_filters', ''),true);

    if ($dwFilters === null) {
        $dwFilters = [];
    }

    if ($payload['val'] === null) {
        if (isset($dwFilters[$payload['attr']])) {
            unset($dwFilters[$payload['attr']]);
        }
    } else {
        $dwFilters[$payload['attr']] = $payload['val'];
    }

    erLhcoreClassModelUserSetting::setSetting('dw_filters', json_encode($dwFilters));
}

echo json_encode(true);

exit;
?>