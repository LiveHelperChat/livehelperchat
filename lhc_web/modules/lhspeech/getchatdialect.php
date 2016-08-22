<?php 

if (is_numeric($Params['user_parameters']['chat_id']))
{
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

    $errors = array();
    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('speech.before_getchatdialect',array('chat' => & $chat, 'errors' => & $errors));

    if(empty($errors)) {
        if ( erLhcoreClassChat::hasAccessToRead($chat) )
        {
            $chatSpeech = erLhcoreClassSpeech::getSpeechInstance($chat);

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('speech.getchatdialect',array('chat' => & $chat));

            echo json_encode(array('error' => false, 'dialect' => $chatSpeech->dialect));
        } else {
            echo json_encode(array('error' => true, 'result' => 'No permission'));
        }
    } else {
        echo json_encode(array('error' => true, 'result' => implode(PHP_EOL, $errors)));
    }

}

exit;

?>