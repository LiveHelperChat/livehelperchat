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

if (!isset($Params['user_parameters_unordered']['m']) || $Params['user_parameters_unordered']['m'] != 'survey'){
    echo "
    #start-chat-btn,#close-need-help-btn{
        cursor:pointer;
    }";

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
        echo "#close-need-help-btn{--bs-btn-close-opacity: 1;--bs-btn-close-hover-opacity: 1;--bs-btn-close-bg:url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23".$theme->need_help_close_bg."'%3e%3cpath d='M.293.293a1 1 0 0 1 1.414 0L8 6.586 14.293.293a1 1 0 1 1 1.414 1.414L9.414 8l6.293 6.293a1 1 0 0 1-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 0 1-1.414-1.414L6.586 8 .293 1.707a1 1 0 0 1 0-1.414z'/%3e%3c/svg%3e\")}";
    }

    if ($theme->need_help_close_hover_bg != '') {
        echo "#close-need-help-btn:hover{--bs-btn-close-opacity: 1;--bs-btn-close-hover-opacity: 1;--bs-btn-close-bg:url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23".$theme->need_help_close_hover_bg."'%3e%3cpath d='M.293.293a1 1 0 0 1 1.414 0L8 6.586 14.293.293a1 1 0 1 1 1.414 1.414L9.414 8l6.293 6.293a1 1 0 0 1-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 0 1-1.414-1.414L6.586 8 .293 1.707a1 1 0 0 1 0-1.414z'/%3e%3c/svg%3e\")}";
    }
}

echo $theme->custom_widget_css;

exit;
?>