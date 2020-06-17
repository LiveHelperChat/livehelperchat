<?php

erLhcoreClassRestAPIHandler::setHeaders();
$requestPayload = json_decode(file_get_contents('php://input'),true);

try {
    $db = ezcDbInstance::get();
    $db->beginTransaction();

    $chat = erLhcoreClassModelChat::fetchAndLock($requestPayload['id']);

    erLhcoreClassChat::setTimeZoneByChat($chat);

    if ($chat->hash == $requestPayload['hash'])
    {
        $outputResponse = array(
            'operator' => 'operator',
            'chat_ui' => array(

            )
        );

        $fileData = (array)erLhcoreClassModelChatConfig::fetch('file_configuration')->data;

        $chatVariables = $chat->chat_variables_array;

        if ((isset($fileData['active_user_upload']) && $fileData['active_user_upload'] == true) || (isset($chatVariables['lhc_fu']) && $chatVariables['lhc_fu'] == 1)) {
            $outputResponse['chat_ui']['file'] = true;
            $outputResponse['chat_ui']['file_options'] = array(
                'fs' => $fileData['fs_max']*1024,
                'ft_us' => $fileData['ft_us'],
            );
        }

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('widgetrestapi.uisettings', array('output' => & $outputResponse, 'chat' => $chat));

        echo erLhcoreClassRestAPIHandler::outputResponse($outputResponse);
    }

} catch(Exception $e) {
    $db->rollback();
}

exit;

?>