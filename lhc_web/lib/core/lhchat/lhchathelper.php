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
}

?>