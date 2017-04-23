<?php

header('content-type: application/json; charset=utf-8');

$definition = array(
        'msg' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
        ),
        'msgid' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'int', array('min_range' => 1)
        )
);

$form = new ezcInputForm( INPUT_POST, $definition );

if (trim($form->msg) != '' && $form->hasValidData('msgid'))
{
	$db = ezcDbInstance::get();
	$db->beginTransaction();	
	try {
		$Chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
		
	    if ( erLhcoreClassChat::hasAccessToRead($Chat) )
	    {
	        $currentUser = erLhcoreClassUser::instance();
	
	        if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
	        	echo json_encode(array('error' => 'true', 'result' => 'Invalid CSRF Token' ));
	        	$db->rollback();
	        	exit;
	        }
	        
	        $msg = erLhcoreClassModelmsg::fetch($form->msgid);
	        	        
	        if ($msg->chat_id == $Chat->id && $msg->user_id == $currentUser->getUserID()) {
		        $msg->msg = trim($form->msg);
		        
		        if ($Chat->chat_locale != '' && $Chat->chat_locale_to != '') {
		            erLhcoreClassTranslate::translateChatMsgOperator($Chat, $msg);
		        }

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_msg_admin_update',array('msg' => & $msg,'chat' => & $Chat));

		        erLhcoreClassChat::getSession()->update($msg);
		        
		        $tpl = erLhcoreClassTemplate::getInstance( 'lhchat/syncadmin.tpl.php');
		        $tpl->set('messages',array((array)$msg));
			    $tpl->set('chat',$Chat);
		        
			    $Chat->operation .= "lhinst.updateMessageRow({$msg->id});\n";
			    $Chat->updateThis();
			    
		        echo erLhcoreClassChat::safe_json_encode(array('error' => 'f','msg' => trim($tpl->fetch())));
	        }	        
	    }   
	     	    
	    $db->commit();
	    
	} catch (Exception $e) {
   		$db->rollback();
    }

} else {
    echo erLhcoreClassChat::safe_json_encode(array('error' => 't', 'r' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter a message, max characters').' - '.(int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value));
}


exit;

?>