<?php
/**
 * 
 * @author Remigijus Kiminas
 * 
 * @desc Stores surveys themself
 *
 */

class erLhAbstractModelSurveyItem {

	public function getState()
	{
		$stateArray = array (
			'id'         	=> $this->id,
			'survey_id'  	=> $this->survey_id,
			'chat_id'		=> $this->chat_id,
			'stars'		    => $this->stars,
			'user_id'		=> $this->user_id,
			'dep_id'		=> $this->dep_id,		
			'ftime'		    => $this->ftime, // Then user was completed by visitor
		);

		return $stateArray;
	}

	public function setState( array $properties )
	{
		foreach ( $properties as $key => $val )
		{
			$this->$key = $val;
		}
	}

	public function __toString()
	{
		return $this->survey;
	}
  
	public static function getCount($params = array())
	{	
		$session = erLhcoreClassAbstract::getSession();
		$q = $session->database->createSelectQuery();
		$q->select( 'COUNT(id)' )->from( 'lh_abstract_survey_item' );
		$conditions = array();
		
		if (isset($params['filter']) && count($params['filter']) > 0)
		{
		    foreach ($params['filter'] as $field => $fieldValue)
		    {
		        $conditions[] = $q->expr->eq( $field, $q->bindValue($fieldValue) );
		    }
		}
		
		if (isset($params['filterin']) && count($params['filterin']) > 0)
		{
		    foreach ($params['filterin'] as $field => $fieldValue)
		    {
		        $conditions[] = $q->expr->in( $field, $fieldValue );
		    }
		}
		
		if (isset($params['filterlt']) && count($params['filterlt']) > 0)
		{
		    foreach ($params['filterlt'] as $field => $fieldValue)
		    {
		        $conditions[] = $q->expr->lt( $field, $q->bindValue($fieldValue) );
		    }
		}
		
		if (isset($params['filtergt']) && count($params['filtergt']) > 0)
		{
		    foreach ($params['filtergt'] as $field => $fieldValue)
		    {
		        $conditions[] = $q->expr->gt( $field,$q->bindValue( $fieldValue ));
		    }
		}
		
		if (isset($params['filterlte']) && count($params['filterlte']) > 0)
		{
		    foreach ($params['filterlte'] as $field => $fieldValue)
		    {
		        $conditions[] = $q->expr->lte( $field, $q->bindValue($fieldValue) );
		    }
		}
		
		if (isset($params['filtergte']) && count($params['filtergte']) > 0)
		{
		    foreach ($params['filtergte'] as $field => $fieldValue)
		    {
		        $conditions[] = $q->expr->gte( $field,$q->bindValue( $fieldValue ));
		    }
		}
		
		if (isset($params['filterlike']) && count($params['filterlike']) > 0)
		{
		    foreach ($params['filterlike'] as $field => $fieldValue)
		    {
		        $conditions[] = $q->expr->like( $field, $q->bindValue('%'.$fieldValue.'%') );
		    }
		}
		
		if (isset($params['customfilter']) && count($params['customfilter']) > 0)
		{
		    foreach ($params['customfilter'] as $fieldValue)
		    {
		        $conditions[] = $fieldValue;
		    }
		}
		
		if (isset($params['leftjoin']) && count($params['leftjoin']) > 0) {
		    foreach ($params['leftjoin'] as $table => $joinOn) {
		        $q->leftJoin($table, $q->expr->eq($joinOn[0], $joinOn[1]));
		    }
		}
		 
		if (isset($params['innerjoin']) && count($params['innerjoin']) > 0) {
		    foreach ($params['innerjoin'] as $table => $joinOn) {
		        $q->innerJoin($table, $q->expr->eq($joinOn[0], $joinOn[1]));
		    }
		}
		 
		if ( count($conditions) > 0 )
		{
		    $q->where( $conditions );
		}
		
		 
		if (isset($params['use_index'])) {
		    $q->useIndex( $params['use_index'] );
		}
		
		$stmt = $q->prepare();
		$stmt->execute();
		$result = $stmt->fetchColumn();
		
		return $result;		
	}

	public function __get($var)
	{
	   switch ($var) {
	       
	   	case 'survey':
	   	       $this->survey = '';
	   		   return $this->survey;
	   		break;
	       
	   	case 'ftime_front':
	   	       $this->ftime_front = date('Ymd') == date('Ymd',$this->ftime) ? date(erLhcoreClassModule::$dateHourFormat,$this->ftime) : date(erLhcoreClassModule::$dateDateHourFormat,$this->ftime);
	   		   return $this->ftime_front;
	   		break;
	   		
	   	case 'user':
	   	       try {
	   	           $this->user = erLhcoreClassModelUser::fetch($this->user_id,true);
	   	       } catch (Exception $e) {
	   	           $this->user = false;
	   	       }
	   		   return $this->user;
	   		break;

	   	case 'is_filled':
	   	       return !is_null($this->id);
	   	    break;	
	   	    
   	    case 'department':
   	        $this->department = false;
   	        if ($this->dep_id > 0) {
   	            try {
   	           					$this->department = erLhcoreClassModelDepartament::fetch($this->dep_id,true);
   	            } catch (Exception $e) {
   	    
   	            }
   	        }
   	    
   	        return $this->department;
   	        break;
   	    
   	    case 'department_name':
   	        return $this->department_name = (string)$this->department;
   	        break;
	   	        
	   	default:
	   		break;
	   }
	}

	public static function fetch($id)
	{
		if (isset($GLOBALS['erLhAbstractModelSurveyItem_'.$id])) return $GLOBALS['erLhAbstractModelSurveyItem_'.$id];

		try {
			$GLOBALS['erLhAbstractModelSurveyItem_'.$id] = erLhcoreClassAbstract::getSession()->load( 'erLhAbstractModelSurveyItem', (int)$id );
		} catch (Exception $e) {
			$GLOBALS['erLhAbstractModelSurveyItem_'.$id] = false;
		}

		return $GLOBALS['erLhAbstractModelSurveyItem_'.$id];
	}

	public function removeThis()
	{
		erLhcoreClassAbstract::getSession()->delete($this);
	}

	public static function getInstance(erLhcoreClassModelChat $chat, erLhAbstractModelSurvey $survey) 
	{
	    $items = self::getList(array('filter' => array('chat_id' => $chat->id, 'survey_id' => $survey->id)));
	    
	    if (!empty($items)){
	        return array_shift($items);
	    } else {
	        $surveyItem = new self();
	        $surveyItem->chat_id = $chat->id;
	        $surveyItem->survey_id = $survey->id;
	        $surveyItem->user_id = $chat->user_id;
	        $surveyItem->dep_id = $chat->dep_id;
	        $surveyItem->ftime = time();
	        return $surveyItem;
	    }
	}
	
	public static function getList($paramsSearch = array())
   	{
       	$paramsDefault = array('limit' => 500, 'offset' => 0);

       	$params = array_merge($paramsDefault,$paramsSearch);

       	$session = erLhcoreClassAbstract::getSession();

       	$q = $session->createFindQuery( 'erLhAbstractModelSurveyItem' );

		$conditions = array();

		if (isset($params['filter']) && count($params['filter']) > 0)
		{
			foreach ($params['filter'] as $field => $fieldValue)
			{
				$conditions[] = $q->expr->eq( $field, $fieldValue );
			}
		}

		if (isset($params['filterin']) && count($params['filterin']) > 0)
		{
			foreach ($params['filterin'] as $field => $fieldValue)
			{
				$conditions[] = $q->expr->in( $field, $fieldValue );
			}
		}

		if (isset($params['filterlt']) && count($params['filterlt']) > 0)
		{
			foreach ($params['filterlt'] as $field => $fieldValue)
			{
				$conditions[] = $q->expr->lt( $field, $fieldValue );
			}
		}

		if (isset($params['filtergt']) && count($params['filtergt']) > 0)
		{
			foreach ($params['filtergt'] as $field => $fieldValue)
			{
				$conditions[] = $q->expr->gt( $field, $fieldValue );
			}
		}

		if (isset($params['filterlte']) && count($params['filterlte']) > 0)
		{
		    foreach ($params['filterlte'] as $field => $fieldValue)
		    {
		        $conditions[] = $q->expr->lte( $field, $q->bindValue($fieldValue) );
		    }
		}
		
		if (isset($params['filtergte']) && count($params['filtergte']) > 0)
		{
		    foreach ($params['filtergte'] as $field => $fieldValue)
		    {
		        $conditions[] = $q->expr->gte( $field,$q->bindValue( $fieldValue ));
		    }
		}
		
		if (count($conditions) > 0)
		{
			$q->where( $conditions );
		}

      	$q->limit($params['limit'],$params['offset']);

      	$q->orderBy(isset($params['sort']) ? $params['sort'] : 'id DESC' );

       	$objects = $session->find( $q );

    	return $objects;
	}

	public function updateThis(){
		erLhcoreClassAbstract::getSession()->update($this);
	}
	
	public function saveThis(){
		erLhcoreClassAbstract::getSession()->save($this);
	}
	
	public function saveOrUpdate(){
		erLhcoreClassAbstract::getSession()->saveOrUpdate($this);
	}
	
   	public $id = NULL;
	public $survey_id = NULL;
	public $chat_id = NULL;
	public $stars = 0;
	public $user_id = 0;
	public $ftime = 0;
	
	public $hide_add = false;
	public $hide_delete = false;

}

?>