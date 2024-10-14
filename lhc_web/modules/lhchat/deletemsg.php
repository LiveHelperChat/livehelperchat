<?php

header('Content-type: application/json');

try {
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

    if ( erLhcoreClassChat::hasAccessToRead($chat) )
    {
        if ($Params['user_parameters']['msg_id']) {
            $removeMessage = erLhcoreClassModelmsg::fetch($Params['user_parameters']['msg_id']);
        }

        if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
            echo json_encode(array('error' => 't', 'result' => 'Invalid CSRF Token' ));
            exit;
        }

        if (isset($removeMessage) && $removeMessage->chat_id == $chat->id && (
            ($removeMessage->user_id == 0 && erLhcoreClassUser::instance()->hasAccessTo('lhchat','removemsgvi')) ||
            (($removeMessage->user_id > 0 || $removeMessage->user_id == -2) && erLhcoreClassUser::instance()->hasAccessTo('lhchat','removemsgop'))
            )
        ) {

            $removeMessage->removeThis();

            $chat->operation .= "lhinst.updateMessageRow({$removeMessage->id});\n";
            $chat->updateThis(array('update' => array('operation')));

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.msg_removed', array('msg' => $removeMessage, 'chat' => $chat));

            echo json_encode(array('error' => 'f', 'result' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Message was removed!')));

        } else {
            echo json_encode(array('error' => 't','result' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Message could not be found!')));
        }
    }
} catch (Exception $e) {
    echo json_encode(array('error' => 't', 'result' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Message could not be found!')));
}
exit;


?>