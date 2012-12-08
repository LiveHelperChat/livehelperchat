<?php
$currentUser = erLhcoreClassUser::instance();
if (!$currentUser->isLogged() && !$currentUser->authenticate($_POST['username'],$_POST['password']))
{
    exit;
}

        $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
        
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
        }


exit;
?>