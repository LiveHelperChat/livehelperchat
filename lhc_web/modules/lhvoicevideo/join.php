<?php

erLhcoreClassRestAPIHandler::setHeaders();
erTranslationClassLhTranslation::$htmlEscape = false;

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['id']);

$validStatuses = array(
    erLhcoreClassModelChat::STATUS_PENDING_CHAT,
    erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,
    erLhcoreClassModelChat::STATUS_BOT_CHAT,
);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.validstatus_chat',array('chat' => & $chat, 'valid_statuses' => & $validStatuses));

if ($chat->hash == $Params['user_parameters']['hash'] && (in_array($chat->status,$validStatuses)) && !in_array($chat->status_sub, array(erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW,erLhcoreClassModelChat::STATUS_SUB_CONTACT_FORM))) // Allow add messages only if chat is active
{
    $vvcall = erLhcoreClassModelChatVoiceVideo::getInstance($chat->id);

    if ($Params['user_parameters_unordered']['action'] == 'cancel') {
        $vvcall->vi_status = erLhcoreClassModelChatVoiceVideo::STATUS_VI_PENDING;
        $vvcall->updateThis(array('update' => array('vi_status')));
    } else if ($Params['user_parameters_unordered']['action'] == 'request') {

        $payload = json_decode(file_get_contents('php://input'),true);

        if (isset($payload['type']) && $payload['type'] == 'audiovideo') {
            $vvcall->voice = 1;
            $vvcall->video = 1;
        } elseif (isset($payload['type']) && $payload['type'] == 'audio') {
            $vvcall->voice = 1;
            $vvcall->video = 0;
        }

        $vvcall->vi_status = erLhcoreClassModelChatVoiceVideo::STATUS_VI_REQUESTED;
        $vvcall->updateThis(array('update' => array('vi_status', 'voice', 'video')));
    }

    echo json_encode($vvcall->getState());
}

exit;
?>