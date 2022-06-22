<?php

erLhcoreClassRestAPIHandler::setHeaders();

$payload = json_decode(file_get_contents('php://input'),true);

$validStatuses = array(
    erLhcoreClassModelChat::STATUS_PENDING_CHAT,
    erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,
    erLhcoreClassModelChat::STATUS_BOT_CHAT,
);

$chat = erLhcoreClassModelChat::fetch($payload['id']);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.validstatus_chat',array('chat' => & $chat, 'valid_statuses' => & $validStatuses));

if ($chat->hash === $payload['hash'] && (in_array($chat->status,$validStatuses)) && isset($payload['lng']) && in_array($payload['lng'],erConfigClassLhConfig::getInstance()->getSetting( 'site', 'available_site_access' )))
{
    $settings = erConfigClassLhConfig::getInstance()->getSetting( 'site_access_options', $payload['lng']);
    $chat->chat_locale = $settings['content_language']; // So internal translations will work out of the box
    $chat->updateThis(array('update' => array('chat_locale')));
}

echo erLhcoreClassChat::safe_json_encode(array('error' =>  false));
exit;

?>
