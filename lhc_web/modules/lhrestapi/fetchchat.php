<?php

try {
    erLhcoreClassRestAPIHandler::validateRequest();

    $chat = erLhcoreClassModelChat::fetch((int)$_GET['chat_id']);

    if (!($chat instanceof erLhcoreClassModelChat)) {
        throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('lhrestapi/validation', 'Could not find chat by chat_id!'));
    }
    
    if ($chat instanceof erLhcoreClassModelChat && erLhcoreClassRestAPIHandler::hasAccessToRead($chat) == true) {
        
        $chat = erLhcoreClassModelChat::fetch((int)$_GET['chat_id']);
        
        if (isset($_GET['hash']) && $chat->hash != $_GET['hash']) {
            throw new Exception('Invalid hash');
        }

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('api.fetchchat', array('chat' => & $chat));

        erLhcoreClassChat::prefillGetAttributesObject($chat, array('user','plain_user_name'), array(), array('do_not_clean' => true));

        erLhcoreClassRestAPIHandler::outputResponse(array(
            'error' => false,
            'chat' => $chat
        ));
   
    } else {
        throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('lhrestapi/validation', 'You do not have permission to read this chat!'));
    }

} catch (Exception $e) {
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();