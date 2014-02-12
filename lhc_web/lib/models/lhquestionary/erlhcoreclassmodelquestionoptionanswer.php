<?php

class erLhcoreClassModelQuestionOptionAnswer {

   public function getState()
   {
       return array(
	                'id'         	=> $this->id,
	                'ip'   			=> $this->ip,
	       		    'question_id'   => $this->question_id,
	       			'option_id'	 	=> $this->option_id,
	       			'ctime'	 		=> $this->ctime,
              );
   }

   public function setState( array $properties )
   {
       foreach ( $properties as $key => $val )
       {
           $this->$key = $val;
       }
   }

   public static function fetch($assignee_id)
   {
	   	 $group_user = erLhcoreClassQuestionary::getSession('slave')->load( 'erLhcoreClassModelQuestionOptionAnswer', (int)$assignee_id );
	   	 return $group_user;
   }

   public function removeThis()
   {
       erLhcoreClassQuestionary::getSession()->delete($this);
   }

   public function saveThis()
   {
   	   $this->ip = ip2long(erLhcoreClassIPDetect::getIP());
   	   $this->ctime = time();
       erLhcoreClassQuestionary::getSession()->saveOrUpdate($this);
   }

   public function __get($var){
       switch ($var) {
       	case 'ip_front':
       	        $this->ip_front = long2ip($this->ip);
           		return $this->ip_front;
       		break;


       	default:
       		break;
       }
   }

   public $id = null;
   public $ip = 0;
   public $question_id = null;
   public $option_id = null;
   public $ctime = 0;
}

?>