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

if ($form->hasValidData( 'msg' ) && trim($form->msg) != '' && mb_strlen($form->msg) < (int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value)
{
	$db = ezcDbInstance::get();
	$db->beginTransaction();	
	try {
	    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);	
	    
	    if ($chat->hash == $Params['user_parameters']['hash'] && ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT)) // Allow add messages only if chat is active
	    {    	
	    	$msg = erLhcoreClassModelmsg::fetch($form->msgid);
	    	
	    	if ($msg->chat_id == $chat->id && $msg->user_id == 0) {	    	
		    	$msg->msg = trim($form->msg);
		    	
    	    	if ($chat->chat_locale != '' && $chat->chat_locale_to != '') {
    	            erLhcoreClassTranslate::translateChatMsgVisitor($chat, $msg);
    	        }

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_msg_user_update',array('msg' => & $msg,'chat' => & $chat));

		    	erLhcoreClassChat::getSession()->update($msg);
		    	
		    	$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/syncuser.tpl.php');
		    	$tpl->set('messages',array((array)$msg));
		    	$tpl->set('chat',$chat);
		    	$tpl->set('sync_mode',isset($Params['user_parameters_unordered']['mode']) ? $Params['user_parameters_unordered']['mode'] : '');
		    		    	
		    	$chat->operation_admin .= "lhinst.updateMessageRowAdmin({$chat->id},{$msg->id});\n";
		    	$chat->user_typing = time();
		    	$chat->user_typing_txt = htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/updatemsguser','User has edited his last message'),ENT_QUOTES);
		    			    			    	
		    	$chat->updateThis();
		    	
		    	echo erLhcoreClassChat::safe_json_encode(array('error' => 'f', 'msg' => trim($tpl->fetch())));	 
	    	}	    	
	    }	    
	    $db->commit();
	} catch (Exception $e) {
   		$db->rollback();
    }    
} else {
	echo erLhcoreClassChat::safe_json_encode(array('error' => 't', 'msg' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter a message, max characters').' - '.(int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value));
}

exit;

?>