<?php

$definition = array(
        'msg' => new ezcInputFormDefinitionElement(
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
        $Chat->last_user_msg_time = $msg->time = time();

        erLhcoreClassChat::getSession()->save($msg);

        $Chat->has_unread_messages = 1;
        $Chat->updateThis();
    } else {

    }
} else {

}

echo json_encode(array('error' => 'false'));
exit;

?>