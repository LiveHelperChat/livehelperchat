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
                    (($msg->user_id > 0 || $msg->user_id == -2) && erLhcoreClassUser::instance()->hasAccessTo('lhchat','editpreviousop'))
                )
            ) {

                if ($msg->user_id == $currentUser->getUserID()) {
                    if (!erLhcoreClassUser::instance()->hasAccessTo('lhchat','editpreviousall')) {
                        $lastMessageDirectly = erLhcoreClassChat::getGetLastChatMessageEdit($Chat->id, $currentUser->getUserID());
                        if (!isset($lastMessageDirectly['id']) || $lastMessageDirectly['id'] != $msg->id ) {
                            echo json_encode(array('error' => 't', 'result' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','You can edit only your last message!')));
                            exit;
                        }
                    }
                }

                $originalMessage = $msg->msg;
		        $msg->msg = trim($form->msg);

                $contentChanged = $msg->msg !== $originalMessage;

		        if ($Chat->chat_locale != '' && $Chat->chat_locale_to != '' && isset($Chat->chat_variables_array['lhc_live_trans']) && $Chat->chat_variables_array['lhc_live_trans'] === true) {
		            erLhcoreClassTranslate::translateChatMsgOperator($Chat, $msg);
		        }

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_msg_admin_update',array('msg' => & $msg,'chat' => & $Chat));

		        erLhcoreClassChat::getSession()->update($msg);

                if ($contentChanged == true && ($msg->user_id != $currentUser->getUserID() || !erLhcoreClassUser::instance()->hasAccessTo('lhchat','no_edit_history'))) {
                    $metaData = $msg->meta_msg_array;
                    $historyContent = '[' . $currentUser->getUserID() . '] ' . $currentUser->getUserData()->name_support . ' ' . htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','has modified a message.')) . ' '.htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Original message')).' - [b]'.$originalMessage.'[/b]';
                    if (!isset($metaData['content']['notice']['content'])) {
                        $metaData['content']['notice']['content'] =  $historyContent;
                    } else {
                        if (!isset($metaData['content']['notice']['content_history'])) {
                            $metaData['content']['notice']['content_history'] = []; 
                        }
                        $metaData['content']['notice']['content_history'][] = $historyContent;
                    }

                    $msg->meta_msg_array = $metaData;
                    $msg->meta_msg = json_encode($metaData);
                    $msg->updateThis(['update' => ['meta_msg']]);
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
            // General messages was updated event
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.message_updated', array('msg' => & $msg, 'chat' => & $Chat));
            
            // Event indicates that admin has an updated message manually
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.message_updated_admin', array('msg' => & $msg, 'chat' => & $Chat));
        }

	} catch (Exception $e) {
   		$db->rollback();
    }

} else {
    echo erLhcoreClassChat::safe_json_encode(array('error' => 't', 'r' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter a message, max characters').' - '.(int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value));
}


exit;

?>