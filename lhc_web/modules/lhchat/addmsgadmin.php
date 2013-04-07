<?php

$definition = array(
        'msg' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
        )
);

$form = new ezcInputForm( INPUT_POST, $definition );

if (trim($form->msg) != '')
{
	$Chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

    // Has access to read, chat
    //FIXME create permission to add message...
    if ( erLhcoreClassChat::hasAccessToRead($Chat) )
    {
        $currentUser = erLhcoreClassUser::instance();
        $userData = $currentUser->getUserData();

        $msg = new erLhcoreClassModelmsg();
        $msg->nick = $userData->name.' '.$userData->surname;
        $msg->msg = trim($form->msg);
        $msg->chat_id = $Params['user_parameters']['chat_id'];
        $msg->user_id = $userData->id;
        $msg->time = time();
        $msg->name_support = $userData->name.' '.$userData->surname;
        erLhcoreClassChat::getSession()->save($msg);

        // Set last message ID
        if ($Chat->last_msg_id < $msg->id) {
        	$Chat->last_msg_id = $msg->id;
        	$Chat->updateThis();
        }


        echo json_encode(array('error' => 'false'));
    }

} else {
    echo json_encode(array('error' => 'true'));
}


exit;

?>