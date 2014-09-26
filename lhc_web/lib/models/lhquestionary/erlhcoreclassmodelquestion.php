<?php

class erLhcoreClassModelQuestion {

   public function getState()
   {
       return array(
                'id'         	 => $this->id,
                'question'   	 => $this->question,
                'question_intro' => $this->question_intro,
       		    'location'   	 => $this->location,
       			'active'	 	 => $this->active,
       			'priority'	 	 => $this->priority,
       			'is_voting'	 	 => $this->is_voting,
       			'revote'         => $this->revote,
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

       	case 'total_votes':
       			$this->total_votes = erLhcoreClassQuestionary::getCount(array('filter' => array('question_id' => $this->id)),'lh_question_option_answer');
           		return $this->total_votes;
       		break;

       	case 'total_votes_for_percentange':
       			$this->total_votes_for_percentange = $this->total_votes == 0 ? 1 : $this->total_votes;
           		return $this->total_votes_for_percentange;
       		break;

       	case 'revote_seconds':
       			$this->revote_seconds = $this->revote*3600;
           		return $this->revote_seconds;
       		break;

       	case 'options':
       			$this->options = erLhcoreClassQuestionary::getList(array('sort' => 'priority DESC', 'filter' => array('question_id' => $this->id)),'erLhcoreClassModelQuestionOption','lh_question_option');;
       			return $this->options;
       		break;

       	default:
       		break;
       }
   }

   public $id = null;
   public $question = '';
   public $location = '';
   public $question_intro = '';
   public $active = 1;
   public $priority = 0;
   public $is_voting = 0;
   public $revote = 0;
}

?>