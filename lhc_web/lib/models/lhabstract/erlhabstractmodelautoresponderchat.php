<?php

class erLhAbstractModelAutoResponderChat {

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
            'wait_timeout_send' => $this->wait_timeout_send
		);

		return $stateArray;
	}

	public function __toString()
	{
		return $this->chat_id;
	}
	
	public function process()
	{	  	    
	    if ($this->auto_responder !== false) {
	        
	        if ($this->chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT && 
	            $this->wait_timeout_send <= 0 && 
	            $this->auto_responder->wait_timeout > 0 && 
	            !empty($this->auto_responder->timeout_message) &&
	            (time() - $this->chat->time) > ($this->auto_responder->wait_timeout*($this->auto_responder->wait_timeout_repeat-(abs($this->wait_timeout_send)))))
            {
                $errors = array();
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_auto_responder_triggered',array('chat' => & $this->chat, 'errors' => & $errors));
                 
                if (empty($errors)) {
                    erLhcoreClassChatWorkflow::timeoutWorkflow($this->chat);
                } else {
                    $msg = new erLhcoreClassModelmsg();
                    $msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Auto responder got error').': '.implode('; ', $errors);
                    $msg->chat_id = $this->chat->id;
                    $msg->user_id = -1;
                    $msg->time = time();
                     
                    if ($this->chat->last_msg_id < $msg->id) {
                        $this->chat->last_msg_id = $msg->id;
                    }

                    erLhcoreClassChat::getSession()->save($msg);
                }
            }
	    }
	    
	    /*
	    if ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT && $chat->wait_timeout_send <= 0 && $chat->wait_timeout > 0 && !empty($chat->timeout_message) && (time() - $chat->time) > ($chat->wait_timeout*($chat->wait_timeout_repeat-(abs($chat->wait_timeout_send))))) {
	        $errors = array();
	        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_auto_responder_triggered',array('chat' => & $chat, 'errors' => & $errors));
	    
	        if (empty($errors)) {
	            erLhcoreClassChatWorkflow::timeoutWorkflow($chat);
	        } else {
	            $msg = new erLhcoreClassModelmsg();
	            $msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Auto responder got error').': '.implode('; ', $errors);
	            $msg->chat_id = $chat->id;
	            $msg->user_id = -1;
	            $msg->time = time();
	    
	            if ($chat->last_msg_id < $msg->id) {
	                $chat->last_msg_id = $msg->id;
	            }
	    
	            erLhcoreClassChat::getSession()->save($msg);
	        }
	    }*/
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
}