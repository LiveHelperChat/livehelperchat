<?php

erLhcoreClassRestAPIHandler::setHeaders('Content-Type: text/html; charset=UTF-8');

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/bbcodeinsert.tpl.php');

if (is_numeric($Params['user_parameters']['chat_id']) && $Params['user_parameters']['chat_id'] > 0 && !isset($_GET['react'])) {
    $tpl->set('chat_id', (int)$Params['user_parameters']['chat_id']);
} else {
    $tpl->set('chat_id', null);
}

$tpl->set('mode', null);

$tpl->set('react', isset($_GET['react']));

if (isset($_GET['react'])) {

    if (is_numeric($Params['user_parameters']['chat_id'])) {
        $chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);
    }

    $fileData = (array)erLhcoreClassModelChatConfig::fetch('file_configuration')->data;

    if (!(isset($fileData['active_user_upload']) && $fileData['active_user_upload'] == true || (isset($chat->chat_variables_array['lhc_fu']) && $chat->chat_variables_array['lhc_fu'] == 1))) {
        $tpl->set('file_upload_disabled', true);
    }
}

$bbcodeDisabledOptions = erLhcoreClassModelChatConfig::fetch('bbcode_options')->data;

if (isset($Params['user_parameters_unordered']['mode']) == 'editor') {
    $tpl->set('bb_code_disabled', (isset($bbcodeDisabledOptions['dio']) ? $bbcodeDisabledOptions['dio'] : []));
} else {
    $tpl->set('bb_code_disabled', (isset($bbcodeDisabledOptions['div']) ? $bbcodeDisabledOptions['div'] : []));
}

if (isset($Params['user_parameters_unordered']['mode']) && !empty($Params['user_parameters_unordered']['mode'])){
    $tpl->set('mode', $Params['user_parameters_unordered']['mode']);
}

echo $tpl->fetch();
exit;