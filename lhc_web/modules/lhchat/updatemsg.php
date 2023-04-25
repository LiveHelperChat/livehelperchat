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
	        	        
	        if ($msg->chat_id == $Chat->id && (
                    $msg->user_id == $currentUser->getUserID() ||
                    ($msg->user_id == 0 && erLhcoreClassUser::instance()->hasAccessTo('lhchat','editpreviouvis')) ||
                    ($msg->user_id > 0 && erLhcoreClassUser::instance()->hasAccessTo('lhchat','editpreviousop'))
                )
            ) {
                $originalMessage = $msg->msg;
		        $msg->msg = trim($form->msg);

                $contentChanged = $msg->msg !== $originalMessage;

		        if ($Chat->chat_locale != '' && $Chat->chat_locale_to != '' && isset($Chat->chat_variables_array['lhc_live_trans']) && $Chat->chat_variables_array['lhc_live_trans'] === true) {
		            erLhcoreClassTranslate::translateChatMsgOperator($Chat, $msg);
		        }

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_msg_admin_update',array('msg' => & $msg,'chat' => & $Chat));

		        erLhcoreClassChat::getSession()->update($msg);

                if ($contentChanged == true && $msg->user_id != $currentUser->getUserID()) {
                    $metaData = $msg->meta_msg_array;
                    if (!isset($metaData['content']['notice']['content'])) {
                        $metaData['content']['notice']['content'] =  '[' . $currentUser->getUserID() . '] ' . $currentUser->getUserData()->name_support . ' ' . htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','has modified a message.')) . ' '.
                        htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Original message')).' - [b]'.$originalMessage.'[/b]';
                        $msg->meta_msg_array = $metaData;
                        $msg->meta_msg = json_encode($metaData);
                        $msg->updateThis(['update' => ['meta_msg']]);
                    }
                }

		        $tpl = erLhcoreClassTemplate::getInstance( 'lhchat/syncadmin.tpl.php');
		        $tpl->set('messages',array((array)$msg));
			    $tpl->set('chat',$Chat);
                $tpl->set('see_sensitive_information', $currentUser->hasAccessTo('lhchat','see_sensitive_information'));

			    $Chat->operation .= "lhinst.updateMessageRow({$msg->id});\n";
			    $Chat->updateThis(array('update' => array('operation')));

		        echo erLhcoreClassChat::safe_json_encode(array('error' => 'f', 'msg' => trim($tpl->fetch())));
	        } else {
                echo erLhcoreClassChat::safe_json_encode(array('error' => 't', 'result' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','You can edit only your own messages!')));
            }
	    }   
	     	    
	    $db->commit();

        if (isset($msg) && isset($Chat) && isset($contentChanged) && $contentChanged == true) {
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.message_updated', array('msg' => & $msg, 'chat' => & $Chat));
        }

	} catch (Exception $e) {
   		$db->rollback();
    }

} else {
    echo erLhcoreClassChat::safe_json_encode(array('error' => 't', 'r' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter a message, max characters').' - '.(int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value));
}


exit;

?>