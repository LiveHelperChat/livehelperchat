<?php

class erLhcoreClassChatHelper
{

    /**
     * Message for timeout
     */
    public static function redirectToContactForm($params)
    {
        $msg = new erLhcoreClassModelmsg();
        $msg->msg = (string) $params['user'] . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closechatadmin', 'has redirected user to contact form!');
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

    public static function closeChat($params)
    {
        if ($params['chat']->status != erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
            
            $params['chat']->status = erLhcoreClassModelChat::STATUS_CLOSED_CHAT;
            $params['chat']->chat_duration = erLhcoreClassChat::getChatDurationToUpdateChatID($params['chat']->id);
            
            $msg = new erLhcoreClassModelmsg();
            $msg->msg = (string) $params['user'] . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closechatadmin', 'has closed the chat!');
            $msg->chat_id = $params['chat']->id;
            $msg->user_id = - 1;
            
            $params['chat']->last_user_msg_time = $msg->time = time();
            
            erLhcoreClassChat::getSession()->save($msg);
            
            $params['chat']->updateThis();
            
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
        
                CSCacheAPC::getMem()->removeFromArray('lhc_open_chats', $chat->id);
        
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
}

?>