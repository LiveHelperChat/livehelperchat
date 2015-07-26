<?php

class erLhcoreClassChatEventDispatcher {
      
   private $listeners = array();
   
   private $finishListeners = array();
   
   const STOP_WORKFLOW = 1;
   
   public function listen($event, $callback)
   {
   		$this->listeners[$event][] = $callback;
   }
   
   public function dispatch($event, $param = array())
   {   	   	
	   	if (isset($this->listeners[$event])){
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