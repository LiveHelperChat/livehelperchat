<?php

$definition = array(
        'msg' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
        )
);

$form = new ezcInputForm( INPUT_POST, $definition );

if ($form->hasValidData( 'msg' ) && trim($form->msg) != '' && strlen($form->msg) < 500)
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

    } else {

    }
} else {

}

echo json_encode(array('error' => 'false'));
exit;

?>