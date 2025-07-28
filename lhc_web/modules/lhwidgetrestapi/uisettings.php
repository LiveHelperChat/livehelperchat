<?php

erLhcoreClassRestAPIHandler::setHeaders();

if (!empty($_GET) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $requestPayload = $_GET;
} else {
    $requestPayload = json_decode(file_get_contents('php://input'),true);
}

try {

    if (!isset($requestPayload['id'])) {
        echo erLhcoreClassRestAPIHandler::outputResponse(['error' => 'Missing chat ID!']);
        exit;
    }

    $db = ezcDbInstance::get();
    $db->beginTransaction();

    $chat = erLhcoreClassModelChat::fetchAndLock($requestPayload['id']);

    if (!is_object($chat)) {
        echo erLhcoreClassRestAPIHandler::outputResponse(['error' => 'Chat not found!']);
        exit;
    }

    erLhcoreClassChat::setTimeZoneByChat($chat);

    if ($chat->hash === $requestPayload['hash'])
    {
        $outputResponse = array(
            'operator' => 'operator',
            'chat_ui' => array(),
            'chat_ui_remove' => array(),
        );

        $fileData = (array)erLhcoreClassModelChatConfig::fetch('file_configuration')->data;

        $chatVariables = $chat->chat_variables_array;

        if ((isset($fileData['active_user_upload']) && $fileData['active_user_upload'] == true) || (isset($chatVariables['lhc_fu']) && $chatVariables['lhc_fu'] == 1)) {
            $outputResponse['chat_ui']['file'] = true;
            $outputResponse['chat_ui']['file_options'] = array(
                'fs' => $fileData['fs_max']*1024,
                'ft_us' => $fileData['ft_us'],
            );

            if (isset($fileData['one_file_upload']) && $fileData['one_file_upload'] == true) {
                $outputResponse['chat_ui']['file_options']['one_file_upload'] = true;
            }

            if (isset($fileData['file_preview']) && $fileData['file_preview'] == true) {
                $outputResponse['chat_ui']['file_options']['file_preview'] = true;
            }

        } else {
            $outputResponse['chat_ui_remove'][] = ['chat_ui','file'];
            $outputResponse['chat_ui_remove'][] = ['chat_ui','file_options'];
        }

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('widgetrestapi.uisettings', array('output' => & $outputResponse, 'chat' => $chat));

        echo erLhcoreClassRestAPIHandler::outputResponse($outputResponse);
    }

} catch(Exception $e) {
    $db->rollback();
}

exit;

?>