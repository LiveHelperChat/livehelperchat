<?php

class erLhcoreClassChatEventDispatcher {
      
   private $listeners = array();
   
   private $finishListeners = array();

   public $globalListenersSet = false;

   public $webhooksSet = false;

   public $disableMobile = false;

   const STOP_WORKFLOW = 1;
   
   public function listen($event, $callback)
   {
   		$this->listeners[$event][] = $callback;
   }

   public function setGlobalListeners($event = null, $param = array())
   {
       if ($this->globalListenersSet == false) {
           $this->globalListenersSet = true;

           // Do not set listeners if mobile is disabled
           if ($this->disableMobile == false) {
               $this->listen('chat.chat_started', 'erLhcoreClassLHCMobile::chatStarted');
               $this->listen('chat.data_changed_auto_assign', 'erLhcoreClassLHCMobile::chatStarted');
               $this->listen('chat.addmsguser', 'erLhcoreClassLHCMobile::newMessage');
               $this->listen('chat.messages_added_passive', 'erLhcoreClassLHCMobile::newMessage');
               $this->listen('chat.genericbot_chat_command_transfer', 'erLhcoreClassLHCMobile::botTransfer');
               $this->listen('chat.chat_transfered', 'erLhcoreClassLHCMobile::chatTransferred');
               $this->listen('group_chat.web_add_msg_admin', 'erLhcoreClassLHCMobile::newGroupMessage');
               $this->listen('chat.subject_add', 'erLhcoreClassLHCMobile::newSubject');
           }
       }

       if ($this->webhooksSet == false && $event !== null) {

           $cfg = erConfigClassLhConfig::getInstance();

           $webhooksEnabled = $cfg->getSetting( 'webhooks', 'enabled', false);

           // Web hooks disabled
           if ($webhooksEnabled === false) {
               $this->webhooksSet = true;
               return;
           }

           $worker = $cfg->getSetting( 'webhooks', 'worker' );
           $singleEvent = $cfg->getSetting( 'webhooks', 'single_event', false);

           $className = 'erLhcoreClassChatWebhook' .ucfirst($worker);
           if (class_exists($className)) {
               $worker = new $className;
               $worker->processEvent($event, $param, $singleEvent);
           }
       }
   }

   public function dispatch($event, $param = array())
   {
       $this->setGlobalListeners($event, $param);

	   	if (isset($this->listeners[$event])) {
            $param['lhc_caller'] = debug_backtrace(2,2)[1];

		   	foreach ($this->listeners[$event] as $listener)
		   	{
		   		$responseData = call_user_func_array($listener, array($param));
		   				   		
		   		// We finish executing callback like one of callbacks finished workflow and does not allow more particular callback executions
		   		if (isset($responseData['status']) && $responseData['status'] === self::STOP_WORKFLOW){
		   			return $responseData;
		   		}
		   	}
	   	}
	   	
	   	return false;
   }
   
   // Add finish request callbacks
   public function addFinishRequestEvent($callback, $arguments = array()) {       
       $this->finishListeners[] = array(
           $callback,
           $arguments
       );
   }
   
   // Executes finish request
   // These are low priority and executed then content is already pushed to user
   public function executeFinishRequest() {
       foreach ($this->finishListeners as $listener) {
           call_user_func($listener[0],$listener[1]);
       }
   }
   
   static private $evenDispather = NULL;
   
   static function getInstance() {
   	
	   	if (self::$evenDispather == NULL) {
	   		self::$evenDispather = new self();
	   	}
	   	
	   	return self::$evenDispather;
   }
   
}

?>