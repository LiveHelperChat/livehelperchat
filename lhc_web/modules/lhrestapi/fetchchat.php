<?php

try {
    erLhcoreClassRestAPIHandler::validateRequest();

    try {
        $chat = erLhcoreClassModelChat::fetch((int)$_GET['chat_id'],true,true);
        $chat->archive = null;
    } catch (ezcPersistentObjectNotFoundException $e) {
        try {
            $chatData = erLhcoreClassChatArcive::fetchChatById((int)$_GET['chat_id']);

            if ($chatData === null) {
                throw new Exception('Could not find chat by chat_id in archive!');
            }

            $chat = $chatData['chat'];
            $chat->archive = $chatData['archive'];
        } catch (ezcPersistentObjectNotFoundException $e) {
            throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('lhrestapi/validation', 'Could not find chat by chat_id!'));
        }
    }

    if (($chat instanceof erLhcoreClassModelChat || $chat instanceof erLhcoreClassModelChatArchive) && erLhcoreClassRestAPIHandler::hasAccessToRead($chat) == true) {

        if (isset($_GET['hash']) && $chat->hash != $_GET['hash']) {
            throw new Exception('Invalid hash');
        }

        if (isset($_GET['department_groups']) && $_GET['department_groups'] == 'true') {
            $chat->department_groups = array();
            foreach (erLhcoreClassModelDepartamentGroupMember::getList(array('filter' => array('dep_id' => $chat->dep_id))) as $depGroup) {
                $chat->department_groups[] = $depGroup->dep_group_id;
            };
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