<?php 

header('content-type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

if ($Params['user_parameters_unordered']['hash'] != '' || $Params['user_parameters_unordered']['vid'] != '') {

    $checkHash = true;
    $vid = false;

    if ($Params['user_parameters_unordered']['hash'] != '') {
        list($chatID,$hash) = explode('_',$Params['user_parameters_unordered']['hash']);
    } else if ($Params['user_parameters_unordered']['hash_resume'] != '') {
        list($chatID,$hash) = explode('_',$Params['user_parameters_unordered']['hash_resume']);
    } elseif ($Params['user_parameters_unordered']['vid'] != '') {
        $vid = erLhcoreClassModelChatOnlineUser::fetchByVid($Params['user_parameters_unordered']['vid']);

        if ($vid !== false) {
            $chatID = $vid->chat_id;
            $checkHash = false;
        } else {
            echo json_encode(array('stored' => 'false'));
            exit;
        }
    };

    try {

        if ($chatID > 0) {
            $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $chatID);
        } else {
            $chat = false;
        }

        if ( (($checkHash == true && $chat !== false && $chat->hash == $hash) || $checkHash == false) && ( is_object($vid) || ($chat !== false && $chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT))) {
            
            if ($chat instanceof erLhcoreClassModelChat)
            {
                erLhcoreClassChatValidator::validateCustomFieldsRefresh($chat);      
                
                $chat->user_typing = time();
                $chat->user_typing_txt = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/refreshcustomfields','Data refreshed');
                $chat->operation_admin .= "lhinst.updateVoteStatus(".$chat->id.");";
                $chat->saveThis();
                
                // Force operators to check for new messages
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.data_changed_chat', array(
                    'chat' => & $chat
                ));
            }       
            echo json_encode(array('stored' => 'true'));
            exit;
        }
    } catch (Exception $e) {
        // Do nothing
    }
}

echo json_encode(array('stored' => 'false'));
exit;

?>