<?php

erLhcoreClassRestAPIHandler::setHeaders();

erTranslationClassLhTranslation::$htmlEscape = false;

header('Cache-Control: max-age=84600');
header("Expires:".gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));
header("Last-Modified: ".gmdate("D, d M Y H:i:s", time())." GMT");
header("Pragma: cache");
header("User-Cache-Control: max-age=84600");

if ($Params['user_parameters']['ns'] == 'group_chat') {
    include 'modules/lhrestapi/trans/group_chat.php';
} else if ($Params['user_parameters']['ns'] == 'chat_tabs') {
    include 'modules/lhrestapi/trans/chat_tabs.php';
} else if ($Params['user_parameters']['ns'] == 'chat_canned') {
    include 'modules/lhrestapi/trans/chat_canned.php';
} else if ($Params['user_parameters']['ns'] == 'lhcbo') {
    include 'modules/lhrestapi/trans/lhcbo.php';
} else if ($Params['user_parameters']['ns'] == 'voice_call') {
    include 'modules/lhrestapi/trans/voice_call.php';
} else if ($Params['user_parameters']['ns'] == 'masking') {
    include 'modules/lhrestapi/trans/masking.php';
} else if ($Params['user_parameters']['ns'] == 'mail_chat') {
    include 'modules/lhmailconv/trans/mail_chat.php';
}

exit;
?>