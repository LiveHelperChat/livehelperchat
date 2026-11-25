<?php

header('Content-Type: application/json');

$passwordData = (array)erLhcoreClassModelChatConfig::fetch('password_data')->data;

if (isset($passwordData['allow_login_from_ip']) && $passwordData['allow_login_from_ip'] != '' && !erLhcoreClassIPDetect::isIgnored(erLhcoreClassIPDetect::getIP(), explode(',', $passwordData['allow_login_from_ip']))) {
    http_response_code(403);
    erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => erTranslationClassLhTranslation::getInstance()->getTranslation('lhexport/export', 'Access denied because of IP restrictions')
    ));
    exit;
}

erLhcoreClassRestAPIHandler::outputResponse(array(
    'error' => false,
    'result' => array(
        'ip' => erLhcoreClassIPDetect::getIP(),
        'remote_addr' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null
    )
));

exit();
