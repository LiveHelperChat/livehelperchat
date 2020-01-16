<?php

erLhcoreClassRestAPIHandler::setHeaders();

$msg = '';

if (isset($_POST['msg'])) {
    $msg = $_POST['msg'];
} else {
    $payload = json_decode(file_get_contents('php://input'),true);
    if (isset($payload['msg'])) {
        $msg = $payload['msg'];
    }
}

if ($msg != '') {
    $msg = strip_tags($msg);
}

try {
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
} catch (Exception $e) {
    $chat = false;
}

if (is_object($chat) && $chat->hash == $Params['user_parameters']['hash'])
{
    $validStatuses = array(
        erLhcoreClassModelChat::STATUS_PENDING_CHAT,
        erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,
        erLhcoreClassModelChat::STATUS_BOT_CHAT,
    );

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.validstatus_chat', array('chat' => & $chat, 'valid_statuses' => & $validStatuses));
        
    // Store message only if chat is pending or active
    if (in_array($chat->status,$validStatuses) && !in_array($chat->status_sub,array(erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW,erLhcoreClassModelChat::STATUS_SUB_CONTACT_FORM))) {

        // Rewritten in a more efficient way
        $db = ezcDbInstance::get();
        $stmt = $db->prepare('UPDATE lh_chat SET user_typing = :user_typing, user_typing_txt = :user_typing_txt WHERE id = :id');
        $stmt->bindValue(':id',$chat->id,PDO::PARAM_INT);

        if ( $Params['user_parameters']['status'] == 'true' ) {
            if ($msg != '' && strlen($msg) > 200){
                if ( function_exists('mb_substr') ) {
                    $msg = mb_substr($msg, -200);
                } else {
                    $msg = substr($msg, -200);
                }
            }
            $stmt->bindValue(':user_typing',time(),PDO::PARAM_INT);
            $stmt->bindValue(':user_typing_txt',$msg);
        } else {
            $stmt->bindValue(':user_typing',0,PDO::PARAM_INT);
            $stmt->bindValue(':user_typing_txt',$chat->user_typing_txt);
        }

        $stmt->execute();
    }
}

echo json_encode(array());
exit;

?>