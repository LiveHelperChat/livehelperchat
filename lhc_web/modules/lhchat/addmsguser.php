<?php

$definition = array(
        'msg' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
        )
);

$form = new ezcInputForm( INPUT_POST, $definition );
$r = '';
$error = 'f';

if ($form->hasValidData( 'msg' ) && trim($form->msg) != '' && mb_strlen($form->msg) < (int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value)
{
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

    if ($chat->hash == $Params['user_parameters']['hash'] && ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT)) // Allow add messages only if chat is active
    {
        $msg = new erLhcoreClassModelmsg();
        $msg->msg = trim($form->msg);
        $msg->chat_id = $Params['user_parameters']['chat_id'];
        $msg->user_id = 0;
        $chat->last_user_msg_time = $msg->time = time();

        erLhcoreClassChat::getSession()->save($msg);

        // Set last message ID
        if ($chat->last_msg_id < $msg->id) {
        	$chat->last_msg_id = $msg->id;
        }

        $chat->has_unread_messages = 1;
        $chat->updateThis();
    }
} else {
	$error = 't';
	$r = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter a message, max characters').' - '.(int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value;
}

echo json_encode(array('error' => $error, 'r' => $r));
exit;

?>