<?php

erLhcoreClassRestAPIHandler::setHeaders('Content-type: text/css');

if (empty($Params['user_parameters']['theme']) || ($themeId = erLhcoreClassChat::extractTheme($Params['user_parameters']['theme'])) === false){
    exit;
}

$theme = erLhAbstractModelWidgetTheme::fetch($themeId);

if ($theme->modified > 0) {
    Header("Expires:".gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));
    header("Last-Modified: ".gmdate("D, d M Y H:i:s", $theme->modified)." GMT");

    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && @strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $theme->modified) {
        header("HTTP/1.1 304 Not Modified");
        exit;
    }
}

echo $theme->bot_configuration_array['custom_page_css'];

exit;
?>