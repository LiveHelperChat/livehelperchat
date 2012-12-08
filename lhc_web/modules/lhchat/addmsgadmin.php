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
        $tpl = new erLhcoreClassTemplate('lhchat/addmsgadmin.tpl.php');  
            
        $currentUser = erLhcoreClassUser::instance();
        $userData = $currentUser->getUserData();    
        
        $msg = new erLhcoreClassModelmsg();
        $msg->nick = $userData->name.' '.$userData->surname;
        $msg->msg = $form->msg;
        $msg->status = 0;
        $msg->chat_id = $Params['user_parameters']['chat_id'];
        $msg->user_id = $userData->id;
        $msg->time = time();
        $msg->name_support = $userData->name.' '.$userData->surname;
        
        erLhcoreClassChat::getSession()->save($msg);
        
        $tpl->set('msg',$msg);
        
        echo json_encode(array('error' => 'false','chat_id' => $Params['user_parameters']['chat_id'],'message_id' => $msg->id, 'result' => $tpl->fetch() ));
    }

} else {
    echo json_encode(array('error' => 'true'));
}


exit;

?>