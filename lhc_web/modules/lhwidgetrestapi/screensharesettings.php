<?php
erLhcoreClassRestAPIHandler::setHeaders();

$settings = array(
    'nodejssettings' => array(
        'nodejssocket' => erLhcoreClassModelChatConfig::fetch('sharing_nodejs_sllocation')->current_value,
        'nodejshost' => (erLhcoreClassModelChatConfig::fetch('sharing_nodejs_socket_host')->current_value != '' ? erLhcoreClassModelChatConfig::fetch('sharing_nodejs_socket_host')->current_value : $_SERVER['HTTP_HOST']),
        'path' => erLhcoreClassModelChatConfig::fetch('sharing_nodejs_path')->current_value,
        'secure' => ((int)erLhcoreClassModelChatConfig::fetch('sharing_nodejs_secure')->current_value == 1 ? true : false)
    ),
    'auto_share' => (int)erLhcoreClassModelChatConfig::fetch('sharing_auto_allow')->current_value,
    'cobrowser' => erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value . '//' . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::designJS('js/cobrowse/compiled/cobrowse.visitor.min.js'),
    'nodejsenabled' => (int)erLhcoreClassModelChatConfig::fetch('sharing_nodejs_enabled')->current_value,
    'trans' => array(
        'operator_watching' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus', 'Screen shared, click to finish'),
        'start_share' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshare','Start screen share session'),
        'deny' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshare','Deny screen share'),
    ),
    'url' => erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value . '//' . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurlsite() . '/cobrowse/storenodemap/(sharemode)/chat'
);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('widgetrestapi.screensharesettings', array('output' => & $settings));

erLhcoreClassRestAPIHandler::outputResponse($settings);
exit();

?>