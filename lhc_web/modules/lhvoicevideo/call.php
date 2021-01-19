<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhvoicevideo/call.tpl.php');

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['id']);

$validStatuses = array(
    erLhcoreClassModelChat::STATUS_PENDING_CHAT,
    erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,
    erLhcoreClassModelChat::STATUS_BOT_CHAT,
);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.validstatus_chat',array('chat' => & $chat, 'valid_statuses' => & $validStatuses));

if ($chat->hash == $Params['user_parameters']['hash'] && (in_array($chat->status,$validStatuses)) && !in_array($chat->status_sub, array(erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW,erLhcoreClassModelChat::STATUS_SUB_CONTACT_FORM))) // Allow add messages only if chat is active
{
    $voiceData = (array)erLhcoreClassModelChatConfig::fetch('vvsh_configuration')->data;
    $tpl->set('voice_data',$voiceData);
    $tpl->set('chat',$chat);

    $Result['content'] = $tpl->fetch();
    $Result['pagelayout'] = 'userchat2';
    $Result['voice_call'] = true;

} else {
    $tpl->setFile( 'lhchat/errors/chatnotexists.tpl.php');
}

?>