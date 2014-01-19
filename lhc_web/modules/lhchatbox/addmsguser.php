<?php

$definition = array(
        'msg' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
        ),
        'nick' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
        )
);

$form = new ezcInputForm( INPUT_POST, $definition );

if ($form->hasValidData( 'msg' ) && trim($form->msg) != '' && strlen($form->msg) < 500)
{
    $Chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

    if ($Chat->hash == $Params['user_parameters']['hash'])
    {
        $msg = new erLhcoreClassModelmsg();
        $msg->msg = trim($form->msg);
        $msg->chat_id = $Params['user_parameters']['chat_id'];
        $msg->user_id = 0;
        $_SESSION['lhc_chatbox_nick'] = $msg->name_support = $form->nick;
        $msg->time = time();

        erLhcoreClassChat::getSession()->save($msg);

        // Set last message ID
        if ($Chat->last_msg_id < $msg->id) {
        	$Chat->last_msg_id = $msg->id;
        }

        // Delete legacy messages, propability 1 of 100
        if (1 == mt_rand(1, 100)) {
        	erLhcoreClassChatbox::cleanupChatbox($Chat);
        }

        $Chat->last_user_msg_time = $msg->time = time();
        $Chat->has_unread_messages = 1;
        $Chat->updateThis();
    } else {

    }
} else {

}

echo json_encode(array('error' => 'false','id' => $msg->id));
exit;

?>