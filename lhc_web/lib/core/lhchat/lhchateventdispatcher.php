<?php

class erLhcoreClassChatEventDispatcher {
      
   private $listeners = array();
   
   public function listen($event, $callback)
   {
   		$this->listeners[$event][] = $callback;
   }
   
   public function dispatch($event, $param = array())
   {   	   	
	   	if (isset($this->listeners[$event])){
		   	foreach ($this->listeners[$event] as $listener)
		   	{
		   		call_user_func_array($listener, array($param));
		   	}
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