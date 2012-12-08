<?php

$tpl = new erLhcoreClassTemplate( 'lhchat/addmsguser.tpl.php');

$definition = array(
        'msg' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
        )
);

$form = new ezcInputForm( INPUT_POST, $definition );


if ($form->hasValidData( 'msg' ) && trim($form->msg) != '')
{    
    $Chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);  
    
    if ($Chat->hash == $Params['user_parameters']['hash'])
    {         
        $msg = new erLhcoreClassModelmsg();
        $msg->msg = trim($form->msg);
        $msg->status = 0;
        $msg->chat_id = $Params['user_parameters']['chat_id'];
        $msg->user_id = 0;
        $msg->time = time();
         
        erLhcoreClassChat::getSession()->save($msg);
        
        $tpl->set('msg',$msg);
        $tpl->set('chat',$Chat);
    } else {
         $tpl->setFile( 'lhchat/errors/chatnotexists.tpl.php'); 
    }
} else {
    $tpl->setFile('lhchat/errors/entertext.tpl.php');
}


echo json_encode(array('error' => 'false','chat_id' => $Params['user_parameters']['chat_id'], 'result' => $tpl->fetch() ));
exit;

?>