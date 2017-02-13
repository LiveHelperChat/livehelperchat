<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Content-Type: application/json');

try {
    erLhcoreClassRestAPIHandler::validateRequest();
    
    $chat = erLhcoreClassModelChat::fetch((int)$_GET['chat_id']);
    
    if (erLhcoreClassRestAPIHandler::hasAccessToRead($chat) == true) {
        
        $messages = erLhcoreClassChat::getPendingMessages($chat->id,isset($_GET['last_message_id']) ? (int)$_GET['last_message_id'] : 0);
        
        if (isset($_GET['ignore_system_messages']) &&  $_GET['ignore_system_messages'] == true)
        {
            foreach ($messages as $key => $data) {
                if ($data['user_id'] == -1) {
                    unset($messages[$key]);
                }
            }
        }
        
        erLhcoreClassRestAPIHandler::outputResponse(array(
            'error' => false,
            'messages' => array_values($messages)
        ));
    } else {
        throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('lhrestapi/validation', 'You do not have permission to read this chat!'));
    }
    
} catch (Exception $e) {
    echo json_encode(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();