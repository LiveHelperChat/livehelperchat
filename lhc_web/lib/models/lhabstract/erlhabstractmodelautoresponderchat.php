<?php

class erLhAbstractModelAutoResponderChat
{
    
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_abstract_auto_responder_chat';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'chat_id' => $this->chat_id,
            'auto_responder_id' => $this->auto_responder_id,
            'wait_timeout_send' => $this->wait_timeout_send,
            'pending_send_status' => $this->pending_send_status,
            'active_send_status' => $this->active_send_status
        ); // Which of pending status message was send the last one
        
        return $stateArray;
    }

    public function __toString()
    {
        return $this->chat_id;
    }

    public function process()
    {
        if ($this->auto_responder !== false) {
            
            if ($this->chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT) {
                
                if ($this->auto_responder->ignore_pa_chat == 0 || ($this->auto_responder->ignore_pa_chat == 1 && $this->chat->user_id == 0)) { // Do not send messages to assigned pending chats
                    if ($this->wait_timeout_send <= 0 && $this->auto_responder->wait_timeout > 0 && ! empty($this->auto_responder->timeout_message) && (time() - $this->chat->time) > ($this->auto_responder->wait_timeout * ($this->auto_responder->wait_timeout_repeat - (abs($this->wait_timeout_send))))) {
                        $errors = array();
                        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_auto_responder_triggered', array(
                            'chat' => & $this->chat,
                            'errors' => & $errors
                        ));
                        
                        if (empty($errors)) {
                            erLhcoreClassChatWorkflow::timeoutWorkflow($this->chat);
                            
                            $this->wait_timeout_send ++;
                            
                            // It was the last time this message was executed.
                            // Now we can process next one pending messages if there are any
                            if ($this->wait_timeout_send == 1) {
                                $this->pending_send_status = 1;
                            }
                            
                            $this->saveThis();
                        } else {
                            $msg = new erLhcoreClassModelmsg();
                            $msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat', 'Auto responder got error') . ': ' . implode('; ', $errors);
                            $msg->chat_id = $this->chat->id;
                            $msg->user_id = - 1;
                            $msg->time = time();
                            
                            if ($this->chat->last_msg_id < $msg->id) {
                                $this->chat->last_msg_id = $msg->id;
                            }
                            
                            erLhcoreClassChat::getSession()->save($msg);
                        }
                    } elseif ($this->pending_send_status >= 1 && $this->pending_send_status < 5) {
                        for ($i = 5; $i >= 2; $i --) {
                            if ($this->pending_send_status < $i && $this->auto_responder->{'wait_timeout_' . $i} < (time() - $this->chat->time) && ! empty($this->auto_responder->{'timeout_message_' . $i})) {
                                
                                $this->pending_send_status = $i;
                                $this->saveThis();
                                
                                $msg = new erLhcoreClassModelmsg();
                                $msg->msg = trim($this->auto_responder->{'timeout_message_' . $i});
                                $msg->chat_id = $this->chat->id;
                                $msg->name_support = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Live Support');
                                $msg->user_id = - 2;
                                $msg->time = time();
                                erLhcoreClassChat::getSession()->save($msg);
                                
                                $this->chat->last_msg_id = $msg->id;
                                $this->chat->updateThis();
                            }
                        }
                    }
                }
                
            } elseif ($this->chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) {  
                
                if (($this->chat->last_op_msg_time > $this->chat->last_user_msg_time && $this->chat->last_user_msg_time > 0) || 
                    ($this->chat->last_op_msg_time > $this->chat->time && $this->chat->last_user_msg_time == 0)) 
                {
                     for ($i = 5; $i >= 1; $i--) {
                         if ($this->active_send_status < $i && !empty($this->auto_responder->{'timeout_reply_message_' . $i}) && (time() - $this->chat->last_op_msg_time > $this->auto_responder->{'wait_timeout_reply_' . $i}) ) {
                             
                             $this->active_send_status = $i;
                             $this->saveThis();
                                                          
                             $msg = new erLhcoreClassModelmsg();
                             $msg->msg = trim($this->auto_responder->{'timeout_reply_message_' . $i});
                             $msg->chat_id = $this->chat->id;
                             $msg->name_support = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Live Support');
                             $msg->user_id = - 2;
                             $msg->time = time();
                             erLhcoreClassChat::getSession()->save($msg);
                             
                             $this->chat->last_msg_id = $msg->id;
                             $this->chat->updateThis();                             
                         }
                     }
                } elseif ($this->active_send_status > 0) {
                    $this->active_send_status = 0;
                    $this->saveThis();
                }
            }
        }
        
        /*
         * if ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT && $chat->wait_timeout_send <= 0 && $chat->wait_timeout > 0 && !empty($chat->timeout_message) && (time() - $chat->time) > ($chat->wait_timeout*($chat->wait_timeout_repeat-(abs($chat->wait_timeout_send))))) {
         * $errors = array();
         * erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_auto_responder_triggered',array('chat' => & $chat, 'errors' => & $errors));
         *
         * if (empty($errors)) {
         * erLhcoreClassChatWorkflow::timeoutWorkflow($chat);
         * } else {
         * $msg = new erLhcoreClassModelmsg();
         * $msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Auto responder got error').': '.implode('; ', $errors);
         * $msg->chat_id = $chat->id;
         * $msg->user_id = -1;
         * $msg->time = time();
         *
         * if ($chat->last_msg_id < $msg->id) {
         * $chat->last_msg_id = $msg->id;
         * }
         *
         * erLhcoreClassChat::getSession()->save($msg);
         * }
         * }
         */
    }

    public function __get($var)
    {
        switch ($var) {
            case 'auto_responder':
                $this->auto_responder = erLhAbstractModelAutoResponder::fetch($this->auto_responder_id);
                return $this->auto_responder;
                break;
            
            case 'chat':
                $this->chat = erLhcoreClassModelChat::fetch($this->chat_id);
                return $this->chat;
                break;
            
            default:
                ;
                break;
        }
    }

    public $id = null;

    public $chat_id = null;

    public $auto_responder_id = null;

    public $wait_timeout_send = 0;

    public $pending_send_status = 0;
    
    public $active_send_status = 0;
}