<?php

$currentUser = erLhcoreClassUser::instance();
if (!$currentUser->isLogged() && !$currentUser->authenticate($_POST['username'],$_POST['password']))
{
    exit;
}

$definition = array(
        'msg' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
        )
);

$form = new ezcInputForm( INPUT_POST, $definition );

if (trim($form->msg) != '')
{

    $Chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

    if ( erLhcoreClassChat::hasAccessToRead($Chat) )
    {
        $currentUser = erLhcoreClassUser::instance();
        $userData = $currentUser->getUserData();
        
        $msgText = trim($form->msg);
        $messageUserId = $userData->id;
        
        if (strpos($msgText, '!') === 0) {
            $statusCommand = erLhcoreClassChatCommand::processCommand(array('no_ui_update' => true, 'user' => $userData, 'msg' => $msgText, 'chat' => & $Chat));
            if ($statusCommand['processed'] === true) {
                $messageUserId = -1; // Message was processed set as internal message
                $msgText =  trim('[b]'.$userData->name_support.'[/b]: '.$msgText .' '. $statusCommand['process_status']);
            };
        }
        
        $msg = new erLhcoreClassModelmsg();    
        $msg->msg = $msgText;
        $msg->chat_id = $Params['user_parameters']['chat_id'];
        $msg->user_id = $messageUserId;
        $msg->time = time();
        $msg->name_support = $userData->name_support;
        erLhcoreClassChat::getSession()->save($msg);

        // Set last message ID
        if ($Chat->last_msg_id < $msg->id) {
        	$Chat->last_msg_id = $msg->id;
        	$Chat->updateThis();
        }
        
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.desktop_client_admin_msg',array('msg' => & $msg,'chat' => & $Chat));

    }

} else {

}


exit;

?>