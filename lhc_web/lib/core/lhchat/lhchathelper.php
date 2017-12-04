<?php

class erLhcoreClassChatHelper
{

    /**
     * Message for timeout
     */
    public static function redirectToContactForm($params)
    {
        $msg = new erLhcoreClassModelmsg();
        $msg->msg = (string) $params['user'] . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closechatadmin', 'has redirected visitor to contact form!');
        $msg->chat_id = $params['chat']->id;
        $msg->user_id = - 1;
        
        $params['chat']->last_user_msg_time = $msg->time = time();
        erLhcoreClassChat::getSession()->save($msg);
        
        // Set last message ID
        if ($params['chat']->last_msg_id < $msg->id) {
            if ($params['chat']->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT) {
                $params['chat']->status = erLhcoreClassModelChat::STATUS_ACTIVE_CHAT;
            }
            $params['chat']->last_msg_id = $msg->id;
        }
        
        if ($params['chat']->user_id == 0) {
            $params['chat']->user_id = $params['user']->id;
        }
        
        $params['chat']->support_informed = 1;
        $params['chat']->has_unread_messages = 0;
        
        $params['chat']->status_sub = erLhcoreClassModelChat::STATUS_SUB_CONTACT_FORM;
        $params['chat']->updateThis();        
    }

    /**
     * Redirect user to survey form
     * */
    public static function redirectToSurvey($params)
    {
        $msg = new erLhcoreClassModelmsg();
        $msg->msg = (string) $params['user'] . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closechatadmin', 'has redirected visitor to survey form!');
        $msg->chat_id = $params['chat']->id;
        $msg->user_id = - 1;
        
        $params['chat']->last_user_msg_time = $msg->time = time();
        erLhcoreClassChat::getSession()->save($msg);
        
        $surveyItem = erLhAbstractModelSurveyItem::findOne(array('filter' => array('chat_id' => $params['chat']->id)));
        
        // Make form temporary so user can fill a survey again
        if ($surveyItem instanceof erLhAbstractModelSurveyItem) {
            $surveyItem->status = erLhAbstractModelSurveyItem::STATUS_TEMP;
            $surveyItem->saveThis();
        }
        
        // Set last message ID
        if ($params['chat']->last_msg_id < $msg->id) {
            if ($params['chat']->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT) {
                $params['chat']->status = erLhcoreClassModelChat::STATUS_ACTIVE_CHAT;
            }
            $params['chat']->last_msg_id = $msg->id;
        }
        
        if ($params['chat']->user_id == 0) {
            $params['chat']->user_id = $params['user']->id;
        }
        
        $params['chat']->support_informed = 1;
        $params['chat']->has_unread_messages = 0;
        
        // Store survey id
        if ( isset($params['survey_id']) ) {
            $subArg = $params['chat']->status_sub_arg;
            $argStore = array();

            if ($subArg != '') {
                $argStore = json_decode($subArg,true);
            }
            
            $argStore['survey_id'] = $params['survey_id'];
            
            $params['chat']->status_sub_arg = json_encode($argStore);            
        }
        
        $params['chat']->status_sub = erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW;
        $params['chat']->saveThis();

        if ($params['chat']->user_id > 0){
            erLhcoreClassChat::updateActiveChats($params['chat']->user_id);
        }
    }
    
    public static function getSubStatusArguments( $chat )
    {
        if ($chat->status_sub_arg != '') {
            $args = json_decode($chat->status_sub_arg, true);            
            reset($args);
            $string = array();
            
	        while (list ($key, $value) = each($args)) {
	            $string [] = $key . ':' . $value ;
	        }
	      	        
	        return implode(':', $string);
        }
        
        return '';
    }
    
    public static function closeChat($params)
    {
        if ($params['chat']->status != erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
            
            $db = ezcDbInstance::get();
            $db->beginTransaction();
            
                $params['chat']->status = erLhcoreClassModelChat::STATUS_CLOSED_CHAT;
                $params['chat']->chat_duration = erLhcoreClassChat::getChatDurationToUpdateChatID($params['chat']->id);
                $params['chat']->has_unread_messages = 0;
                
                $msg = new erLhcoreClassModelmsg();
                $msg->msg = (string) $params['user'] . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closechatadmin', 'has closed the chat!');
                $msg->chat_id = $params['chat']->id;
                $msg->user_id = - 1;
                
                $params['chat']->last_user_msg_time = $msg->time = time();
                
                erLhcoreClassChat::getSession()->save($msg);
                
                if ($params['chat']->wait_time == 0) {
                    $params['chat']->wait_time = time() - $chat->time;
                }
                
                $params['chat']->updateThis();
            
            $db->commit();
            
            erLhcoreClassChat::updateActiveChats($params['chat']->user_id);
            
            if ($params['chat']->department !== false) {
                erLhcoreClassChat::updateDepartmentStats($params['chat']->department);
            }
            
            // Execute callback for close chat
            erLhcoreClassChat::closeChatCallback($params['chat'], $params['user']);            
        }
    }
    
    public static function changeStatus($params)
    {
        $changeStatus = $params['status'];
        $chat = $params['chat'];
        $userData = $params['user'];
        $allowCloseRemote = $params['allow_close_remote'];
                
        if ($changeStatus == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) {
            if ($chat->status != erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) {
                $chat->status = erLhcoreClassModelChat::STATUS_ACTIVE_CHAT;
                $chat->wait_time = time() - $chat->time;
            }
        
            if ($chat->user_id == 0)
            {
                $chat->user_id = $userData->id;
            }
             
            $chat->updateThis();
             
        } elseif ($changeStatus == erLhcoreClassModelChat::STATUS_PENDING_CHAT) {
        
            $chat->status = erLhcoreClassModelChat::STATUS_PENDING_CHAT;
            $chat->support_informed = 0;
            $chat->has_unread_messages = 1;
        
            $chat->updateThis();
        
            
        } elseif ($changeStatus == erLhcoreClassModelChat::STATUS_CLOSED_CHAT && $chat->user_id == $userData->id || $allowCloseRemote == true) {
        
            if ($chat->status != erLhcoreClassModelChat::STATUS_CLOSED_CHAT){
                $chat->status = erLhcoreClassModelChat::STATUS_CLOSED_CHAT;
                $chat->chat_duration = erLhcoreClassChat::getChatDurationToUpdateChatID($chat->id);
                	
                $msg = new erLhcoreClassModelmsg();
                $msg->msg = (string)$userData.' '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closechatadmin','has closed the chat!');
                $msg->chat_id = $chat->id;
                $msg->user_id = -1;
        
                $chat->last_user_msg_time = $msg->time = time();
        
                erLhcoreClassChat::getSession()->save($msg);
        
                $chat->updateThis();
        
                // Execute callback for close chat
                erLhcoreClassChat::closeChatCallback($chat,$userData);
            }
            	
        } elseif ($changeStatus == erLhcoreClassModelChat::STATUS_CHATBOX_CHAT) {
            $chat->status = erLhcoreClassModelChat::STATUS_CHATBOX_CHAT;
            erLhcoreClassChat::getSession()->update($chat);
        } elseif ($changeStatus == erLhcoreClassModelChat::STATUS_OPERATORS_CHAT) {
            $chat->status = erLhcoreClassModelChat::STATUS_OPERATORS_CHAT;
            erLhcoreClassChat::getSession()->update($chat);
        }
        
        erLhcoreClassChat::updateActiveChats($chat->user_id);
         
        if ($chat->department !== false) {
            erLhcoreClassChat::updateDepartmentStats($chat->department);
        }
    }
    
    /**
     * 
     * Converts old online visitor data to new online visitor data
     * 
     * @param array $data
     * 
     * @throws Exception
     */
    public static function mergeVid($data)
    {
    	if (!isset($data['vid'])) {
    		throw new Exception('Old vid not provided');
    	}

    	if (!isset($data['new'])) {
    		throw new Exception('New vid not provided');
    	}

    	$old = erLhcoreClassModelChatOnlineUser::fetchByVid($data['vid']);
    	$new = erLhcoreClassModelChatOnlineUser::fetchByVid($data['new']);

    	if ($old === false) {
    	    throw new Exception('Invalid VID value');
    	}
    	
    	if ($new === false && $old !== false) {
    		// If new record not found just update old vid to new vid hash
    		$old->vid = $data['new'];
    		$old->saveThis();
    	} else if ($new !== false && $old !== false) {
    		$db = ezcDbInstance::get();

    		$stmt = $db->prepare('UPDATE lh_chat_online_user_footprint SET online_user_id = :new_online_user_id WHERE online_user_id = :old_online_user_id');
    		$stmt->bindValue(':new_online_user_id',$new->id,PDO::PARAM_INT);
    		$stmt->bindValue(':old_online_user_id',$old->id,PDO::PARAM_INT);
    		$stmt->execute();

    		$stmt = $db->prepare('UPDATE lh_chat SET online_user_id = :new_online_user_id WHERE online_user_id = :old_online_user_id');
    		$stmt->bindValue(':new_online_user_id',$new->id,PDO::PARAM_INT);
    		$stmt->bindValue(':old_online_user_id',$old->id,PDO::PARAM_INT);
    		$stmt->execute();

    		$stmt = $db->prepare('UPDATE lh_cobrowse SET online_user_id = :new_online_user_id WHERE online_user_id = :old_online_user_id');
    		$stmt->bindValue(':new_online_user_id',$new->id,PDO::PARAM_INT);
    		$stmt->bindValue(':old_online_user_id',$old->id,PDO::PARAM_INT);
    		$stmt->execute();

    		$stmt = $db->prepare('UPDATE lh_chat_file SET online_user_id = :new_online_user_id WHERE online_user_id = :old_online_user_id');
    		$stmt->bindValue(':new_online_user_id',$new->id,PDO::PARAM_INT);
    		$stmt->bindValue(':old_online_user_id',$old->id,PDO::PARAM_INT);
    		$stmt->execute();
    		
    		// count pages count to new
    		$new->pages_count += $old->pages_count;
    		$new->tt_pages_count += $old->tt_pages_count;
    		$new->first_visit = $old->first_visit;
    		$new->notes += trim("\n" . $old->notes);
    		$new->total_visits += $old->total_visits;
    		$new->invitation_count += $old->invitation_count;
    		$new->time_on_site += $old->time_on_site;
    		$new->tt_time_on_site += $old->tt_time_on_site;
    		$new->referrer = $old->referrer;
    		
    		$new->saveThis();
    		    		
    		$old->removeThis();
    	}
    }
}

?>