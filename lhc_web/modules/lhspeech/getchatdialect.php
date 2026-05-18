<?php 

if (is_numeric($Params['user_parameters']['chat_id']))
{
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

    $errors = [];
    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('speech.before_getchatdialect',['chat' => & $chat, 'errors' => & $errors]);

    if(empty($errors)) {
        if ( erLhcoreClassChat::hasAccessToRead($chat) )
        {
            $chatSpeech = erLhcoreClassSpeech::getSpeechInstance($chat);

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('speech.getchatdialect',['chat' => & $chat]);

            echo json_encode(['error' => false, 'dialect' => $chatSpeech->dialect]);
        } else {
            echo json_encode(['error' => true, 'result' => 'No permission']);
        }
    } else {
        echo json_encode(['error' => true, 'result' => implode(PHP_EOL, $errors)]);
    }

}

exit;
