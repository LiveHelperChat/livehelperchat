<?php

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);


$currentUser = erLhcoreClassUser::instance();    

if ($currentUser->hasAccessTo('lhchat','deleteglobalchat') || ($currentUser->hasAccessTo('lhchat','deletechat') && $chat->user_id == $currentUser->getUserID()))
{
    erLhcoreClassChat::getSession()->delete($chat);
    
    $q = ezcDbInstance::get()->createDeleteQuery();
    
    // Messages
    $q->deleteFrom( 'lh_msg' )->where( $q->expr->eq( 'chat_id', $Params['user_parameters']['chat_id'] ) );
    $stmt = $q->prepare();
    $stmt->execute();
    
    // Transfered chats
    $q->deleteFrom( 'lh_transfer' )->where( $q->expr->eq( 'chat_id', $Params['user_parameters']['chat_id'] ) );
    $stmt = $q->prepare();
    $stmt->execute();
    
    
    echo json_encode(array('error' => 'false', 'result' => 'ok' ));
} else {
   echo json_encode(array('error' => 'true', 'result' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/deletechatadmin',"You do not have rights to delete a chat") )); 
}

exit;

?>