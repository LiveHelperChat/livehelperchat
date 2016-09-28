<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/editnick.tpl.php');

$nickChanged = false;

try {

    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

    if ($chat->hash == $Params['user_parameters']['hash'] && ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT))
    {
        if (ezcInputForm::hasPostData()) {
            $Errors = erLhcoreClassChatValidator::validateNickChange($chat);
            if (!empty($Errors)) {
                $tpl->set('errors', $Errors);
            } else {
                $chat->saveThis();
                $tpl->set('updated',true);
                
                $chat->user_typing = time();
                $chat->user_typing_txt = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voteaction','User has updated his profile information');
                $chat->operation_admin .= "lhinst.updateVoteStatus(".$chat->id.");";
                
                $nickChanged = true;                
            }
        }
        
        $tpl->set('chat',$chat);
    } else {
        exit;
    }

} catch(Exception $e) {
   exit;
}

echo $tpl->fetch();

flush();
 
session_write_close();
 
if ( function_exists('fastcgi_finish_request') ) {
    fastcgi_finish_request();
};

if ($nickChanged === true) {
    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.nick_changed',array('chat' => & $chat));
}

exit;

?>