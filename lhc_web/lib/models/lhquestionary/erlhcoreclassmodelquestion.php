<?php

class erLhcoreClassModelQuestion {

   public function getState()
   {
       return array(
                'id'         => $this->id,
                'question'   => $this->question,
       		    'location'   => $this->location,
       			'active'	 => $this->active,
       			'priority'	 => $this->priority,
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
	   	 $group_user = erLhcoreClassQuestionary::getSession('slave')->load( 'erLhcoreClassModelQuestion', (int)$assignee_id );
	   	 return $group_user;
   }

   public function removeThis()
   {
   	   // Delete question answers
	   $q = ezcDbInstance::get()->createDeleteQuery();

	   $q->deleteFrom( 'lh_question_answer' )->where( $q->expr->eq( 'question_id', $this->id ) );
	   $stmt = $q->prepare();
	   $stmt->execute();

       erLhcoreClassQuestionary::getSession()->delete($this);
   }

   public function saveThis()
   {
       erLhcoreClassQuestionary::getSession()->saveOrUpdate($this);
   }

   public function __get($var){
       switch ($var) {
       	case 'user':
       	        try {
           		   $this->user = erLhcoreClassModelUser::fetch($this->user_id);
       	        } catch (Exception $e){
       	            $this->user = 'Not exist';
       	        }
           		return $this->user;
       		break;

       	default:
       		break;
       }
   }

   public $id = null;
   public $question = '';
   public $location = '';
   public $active = 1;
   public $priority = 0;
}

?>