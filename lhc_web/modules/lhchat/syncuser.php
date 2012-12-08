<?php

try {
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
} catch (Exception $e) {
    $chat = false;
}

$content = 'false';
$status = 'true';
$blocked = 'false';

if (is_object($chat) && $chat->hash == $Params['user_parameters']['hash'])
{
    $Messages = erLhcoreClassChat::getPendingUserMessages($Params['user_parameters']['chat_id']);
        
    if (count($Messages) > 0)
    {
        $tpl = new erLhcoreClassTemplate( 'lhchat/syncuser.tpl.php');
        $tpl->set('messages',$Messages);
        $content = $tpl->fetch();
    }
    
    // Closed
    if ($chat->status == 2) $status = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Support closed chat window, but You can leave messages, administrator will read them later.');
        
} else {
    $content = 'false';
    $status = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','You do not have permission to view this chat, or chat was deleted');
    $blocked = 'true';
}

echo json_encode(array('error' => 'false', 'result' => trim($content) == '' ? 'false' : trim($content),'status' => $status, 'blocked' => $blocked ));
exit;

?>