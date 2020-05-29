<?php

erLhcoreClassRestAPIHandler::setHeaders('Content-type: text/css');

$theme = erLhAbstractModelWidgetTheme::fetch($Params['user_parameters']['theme']);

if ($theme->modified > 0) {
    Header("Expires:".gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));
    header("Last-Modified: ".gmdate("D, d M Y H:i:s", $theme->modified)." GMT");

    if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $theme->modified) {
        header("HTTP/1.1 304 Not Modified");
        exit;
    }
}
echo "
#start-chat-btn,#close-need-help-btn{
    cursor:pointer;
}
";

if ($theme->need_help_bcolor != '') {
    echo ".nh-background{background-color:#" . $theme->need_help_bcolor .'!important}';
}

if ($theme->need_help_hover_bg != '') {
    echo ".nh-background:hover{background-color:#" . $theme->need_help_hover_bg .'!important}';
}

if ($theme->need_help_tcolor != '') {
    echo ".nh-background{color:#" . $theme->need_help_tcolor .'!important}';
}

if ($theme->need_help_border != '') {
    echo ".nh-background{border:1px solid #" . $theme->need_help_border .'!important}';
}

if ($theme->need_help_close_bg != '') {
    echo "#close-need-help-btn{color#" . $theme->need_help_close_bg .'!important}';
}

if ($theme->need_help_close_hover_bg != '') {
    echo "#close-need-help-btn:hover{color:#" . $theme->need_help_close_hover_bg .'!important}';
}

exit;
?>