<?php

// Set new chat owner
$currentUser = erLhcoreClassUser::instance();
$currentUser->getUserID();

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

// Chat can be closed only by owner
if ($chat->user_id == $currentUser->getUserID())
{
    if ($Params['user_parameters']['substatus'] == erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW)
    {
        erLhcoreClassChatHelper::redirectToSurvey(array('chat' => $chat, 'user' => $currentUser->getUserData()));
        
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.set_sub_status',array('chat' => & $chat));
    }
}

exit;

?>