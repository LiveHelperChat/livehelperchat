<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/editnick.tpl.php');

try {

    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

    if ($chat->hash == $Params['user_parameters']['hash'])
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
            }
        }
        
        $tpl->set('chat',$chat);
        erLhcoreClassChat::setTimeZoneByChat($chat);        
    } else {
        $tpl->setFile( 'lhchat/errors/chatnotexists.tpl.php');
    }

} catch(Exception $e) {
   $tpl->setFile('lhchat/errors/chatnotexists.tpl.php');
}

echo $tpl->fetch();
exit;

?>