<?php

class erLhcoreClassModelQuestionOption {

   public function getState()
   {
       return array(
	                'id'         	=> $this->id,
	       		    'question_id'   => $this->question_id,
	       			'option_name'	=> $this->option_name,
	       			'priority'	    => $this->priority,
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
	   	 $group_user = erLhcoreClassQuestionary::getSession('slave')->load( 'erLhcoreClassModelQuestionOption', (int)$assignee_id );
	   	 return $group_user;
   }

   public function removeThis()
   {
       erLhcoreClassQuestionary::getSession()->delete($this);

       // Check if there are any other options and if neccesary mark question as plain question form
       $itemsTotal = erLhcoreClassQuestionary::getCount(array('filter' => array('question_id' => $question->id)),'lh_question_option');

       if ($itemsTotal == 0) {
	       	$db = ezcDbInstance::get();
	       	$stmt = $db->prepare('UPDATE lh_question SET is_voting = 0 WHERE id = :id');
	       	$stmt->bindValue(':id',$this->question_id,PDO::PARAM_INT);
	       	$stmt->execute();
       }
   }

   public function saveThis()
   {
   	   $this->ip = ip2long(erLhcoreClassIPDetect::getIP());
       erLhcoreClassQuestionary::getSession()->saveOrUpdate($this);
   }

   public function __get($var){
       switch ($var) {
       		case 'votes':
       			$this->votes = erLhcoreClassQuestionary::getCount(array('filter' => array('option_id' => $this->id)),'lh_question_option_answer');
       			return $this->votes;
       		break;

       	default:
       		break;
       }
   }

   public $id = null;
   public $question_id = null;
   public $option_name = null;
   public $priority = 0;
}

?>