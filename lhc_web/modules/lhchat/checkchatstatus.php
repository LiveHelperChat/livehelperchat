<?php

header('Content-type: application/json');

$activated = 'false';
$result = 'false';
$ott = '';
$ru = '';

$tpl = erLhcoreClassTemplate::getInstance('lhchat/checkchatstatus.tpl.php');
$tpl->set('theme',false);

if (isset($Params['user_parameters_unordered']['theme']) && ($themeId = erLhcoreClassChat::extractTheme($Params['user_parameters_unordered']['theme'])) !== false) {
    try {
        $theme = erLhAbstractModelWidgetTheme::fetch($themeId);
        $theme->translate();
        $tpl->set('theme',$theme);
    } catch (Exception $e) {

    }
} else {
    $defaultTheme = erLhcoreClassModelChatConfig::fetch('default_theme_id')->current_value;
    if ($defaultTheme > 0) {
        try {
            $theme = erLhAbstractModelWidgetTheme::fetch($defaultTheme);
            $theme->translate();
            $tpl->set('theme',$theme);
        } catch (Exception $e) {
             
        }
    }
}

$responseArray = array();

try {
    
    $db = ezcDbInstance::get();
    $db->beginTransaction();
    
    $chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);
    
    if ($chat instanceof erLhcoreClassModelChat && $chat->hash === $Params['user_parameters']['hash']) {

    	// Main unasnwered chats callback
    	if ( $chat->na_cb_executed == 0 && $chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT && erLhcoreClassModelChatConfig::fetch('run_unaswered_chat_workflow')->current_value > 0) {    		
    		$delay = time()-(erLhcoreClassModelChatConfig::fetch('run_unaswered_chat_workflow')->current_value*60);    		
    		if ($chat->time < $delay) {    		
    			erLhcoreClassChatWorkflow::unansweredChatWorkflow($chat);
    		}
    	}
    	
    	if ( $chat->nc_cb_executed == 0 && $chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT) {      		  		
    		$department = $chat->department;    		   		
    		if ($department !== false) {    			
    			$options = $department->inform_options_array;   		 				
    			$delay = time()-$department->inform_delay;    			
    			if ($chat->time < $delay) {
    				erLhcoreClassChatWorkflow::newChatInformWorkflow(array('department' => $department,'options' => $options),$chat);
    			}
    		} else {
    			$chat->nc_cb_executed = 1;
    			$chat->updateThis(array('update' => array('nc_cb_executed')));
    		}
    	}
    	
    	$contactRedirected = false;
    	
    	if ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT) {
    		$department = $chat->department;
    		if ($department !== false) {
    			$delay = time()-$department->delay_lm;
    			if ($department->delay_lm > 0 && $chat->pnd_time < $delay) {
    				$baseURL = (isset($Params['user_parameters_unordered']['mode']) && $Params['user_parameters_unordered']['mode'] == 'widget') ? erLhcoreClassDesign::baseurl('chat/chatwidget') : erLhcoreClassDesign::baseurl('chat/startchat');
    				$ru = $baseURL.'/(department)/'.$department->id.'/(offline)/true/(leaveamessage)/true/(chatprefill)/'.$chat->id.'_'.$chat->hash;
    				
    				$msg = new erLhcoreClassModelmsg();
    				$msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','Visitor has been redirected to contact form');
    				$msg->chat_id = $chat->id;
    				$msg->user_id = -1;
    				$msg->time = time();    				
    				erLhcoreClassChat::getSession()->save($msg);
    				
    				// We do not store last msg time for chat here, because in any case none of opeators has opened it
    				$contactRedirected = true;
    				
    				if ($chat->status_sub != erLhcoreClassModelChat::STATUS_SUB_CONTACT_FORM) {
        				$chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_CONTACT_FORM;
        				$chat->updateThis(array('update' => array('status_sub')));
    				}
    				
    			} else {
                    if (erLhcoreClassModelChatConfig::fetchCache('disable_live_autoassign')->current_value == 0) {
    				    erLhcoreClassChatWorkflow::autoAssign($chat,$department, array('user_init' => true));
                    }
    			}
    		}   		
    	}    	
    	
	    if ( erLhcoreClassChat::isOnline($chat->dep_id,false,array('online_timeout' => (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'])) ) {
	         $tpl->set('is_online',true);
	    } else {
	         $tpl->set('is_online',false);
	    }

	    if ( $chat->chat_initiator == erLhcoreClassModelChat::CHAT_INITIATOR_PROACTIVE ) {
	         $tpl->set('is_proactive_based',true);
	    } else {
	         $tpl->set('is_proactive_based',false);
	    }

	    if ($chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) {
	       $activated = 'true';
	       $tpl->set('is_activated',true);
	    } else {
	       $tpl->set('is_activated',false);
	    }

	    if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
	    	$activated = 'true';
	    	$tpl->set('is_closed',true);
	    	$responseArray['closed'] = true;
	    } else {
	    	$tpl->set('is_closed',false);
	    }
	    
	    if ($chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_CONTACT_FORM && $contactRedirected == false) {
	    	$activated = 'false';
	    	$department = $chat->department;
	    	if ($department !== false) {
	    		$baseURL = (isset($Params['user_parameters_unordered']['mode']) && $Params['user_parameters_unordered']['mode'] == 'widget') ? erLhcoreClassDesign::baseurl('chat/chatwidget') : erLhcoreClassDesign::baseurl('chat/startchat');
	    		$ru = $baseURL.'/(department)/'.$department->id.'/(offline)/true/(leaveamessage)/true/(chatprefill)/'.$chat->id.'_'.$chat->hash;

	    		$msg = new erLhcoreClassModelmsg();
	    		$msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','Visitor has been redirected to contact form');
	    		$msg->chat_id = $chat->id;
	    		$msg->user_id = -1;
	    		$msg->time = time();
	    		erLhcoreClassChat::getSession()->save($msg);
	    		// We do not store last msg time for chat here, because in any case none of opeators has opened it
	    	}
	    }
	    
	    $tpl->set('chat', $chat);
    } else {
        $responseArray['error'] = 'false';
        $responseArray['result'] = '';
        $responseArray['activated'] = 'true';
        $responseArray['closed'] = true;
        echo json_encode($responseArray);
        exit;
    }
    
    $db->commit();
} catch (Exception $e) {
    $db->rollback();
    exit;
}

$responseArray['error'] = 'false';
$responseArray['ru'] = $ru;
$responseArray['ott'] = $ott;
$responseArray['result'] = $tpl->fetch();
$responseArray['activated'] = $activated;
$responseArray['uid'] = (int)$chat->user_id;
$responseArray['status'] = (int)$chat->status;

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.checkchatstatus',array('chat' => & $chat, 'response' => & $responseArray));

echo json_encode($responseArray);
exit;
?>