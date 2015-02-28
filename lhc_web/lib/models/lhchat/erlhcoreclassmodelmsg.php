<?php
/**
 *
 * user_id - administartor user_id,
 * If 0 web user
 *
 * */

class erLhcoreClassModelmsg {

   public function getState()
   {
       return array(
               'id'         	=> $this->id,
               'msg'        	=> $this->msg,
               'time'       	=> $this->time,
               'chat_id'    	=> $this->chat_id,
               'user_id'    	=> $this->user_id,
               'name_support'   => $this->name_support
              );
   }

   public function saveThis() {
   		erLhcoreClassChat::getSession()->saveOrUpdate($this);
   }
   
   public static function fetch($msg_id) {
   		$msg = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelmsg', (int)$msg_id );
   		return $msg;
   }
   
   public static function getList($paramsSearch = array()) {
       if (!isset($paramsSearch['sort'])){
           $paramsSearch['sort'] = 'id ASC';
       };
       
       return erLhcoreClassChat::getList($paramsSearch,'erLhcoreClassModelmsg','lh_msg');
   }

   public function __get($var) {

	   	switch ($var) {
	   		case 'time_front':
		   			if (date('Ymd') == date('Ymd',$this->time)) {
		   			     $this->time_front = date(erLhcoreClassModule::$dateHourFormat,$this->time);
		   			} else {
		   			     $this->time_front = date(erLhcoreClassModule::$dateDateHourFormat,$this->time);
		   			}
		   			return $this->time_front;
	   			break;

	   		default:
	   			break;
	   	}
   }

   public function setState( array $properties )
   {
       foreach ( $properties as $key => $val )
       {
           $this->$key = $val;
       }
   }

    public $id = null;
    public $time = '';
    public $chat_id = null;
    public $user_id = null;
    public $name_support = '';
}

?>