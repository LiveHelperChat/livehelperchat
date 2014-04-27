<?php

class erLhAbstractModelFormCollected {

	public function getState()
	{
		$stateArray = array (
			'id'         	=> $this->id,
			'form_id'  		=> $this->form_id,
			'ctime'  		=> $this->ctime,
			'ip'  			=> $this->ip,
			'content' 		=> $this->content
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
	
	public static function getCount($params = array())
	{
		$session = erLhcoreClassAbstract::getSession();
		$q = $session->database->createSelectQuery();
		$q->select( "COUNT(id)" )->from( "lh_abstract_form_collected" );

		if (isset($params['filter']) && count($params['filter']) > 0)
		{
	   		$conditions = array();

		   	foreach ($params['filter'] as $field => $fieldValue)
		   	{
		    	$conditions[] = $q->expr->eq( $field, $fieldValue );
		   	}

	   		$q->where( $conditions );
		}

		$stmt = $q->prepare();
		$stmt->execute();
		$result = $stmt->fetchColumn();

		return $result;
	}

	public function __get($var)
	{
	   switch ($var) {
	   	case 'left_menu':
	   	       $this->left_menu = '';
	   		   return $this->left_menu;
	   		break;
	   				
	   	default:
	   		break;
	   }
	}
	
	public function updateThis(){
		$this->saveThis();
	}
	
	public function saveThis()
	{	
		erLhcoreClassAbstract::getSession()->saveOrUpdate($this);
	}
	
	public static function fetch($id)
	{
		if (isset($GLOBALS['erLhAbstractModelFormCollected_'.$id])) return $GLOBALS['erLhAbstractModelFormCollected_'.$id];

		try {
			$GLOBALS['erLhAbstractModelFormCollected_'.$id] = erLhcoreClassAbstract::getSession()->load( 'erLhAbstractModelFormCollected', (int)$id );
		} catch (Exception $e) {
			$GLOBALS['erLhAbstractModelFormCollected_'.$id] = '-';
		}

		return $GLOBALS['erLhAbstractModelFormCollected_'.$id];
	}

	public function removeThis()
	{
		erLhcoreClassAbstract::getSession()->delete($this);
	}

	public static function getList($paramsSearch = array())
   	{
       	$paramsDefault = array('limit' => 500, 'offset' => 0);

       	$params = array_merge($paramsDefault,$paramsSearch);

       	$session = erLhcoreClassAbstract::getSession();

       	$q = $session->createFindQuery( 'erLhAbstractModelFormCollected' );

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
				$conditions[] = $q->expr->gt( $field, $q->bindValue($fieldValue) );
			}
		}

		if (count($conditions) > 0)
		{
			$q->where( $conditions );
		}

      	$q->limit($params['limit'],$params['offset']);

      	$q->orderBy(isset($params['sort']) ? $params['sort'] : 'id ASC' );

       	$objects = $session->find( $q );

    	return $objects;
	}

	
   	public $id = null;
	public $form_id = null;
	public $ctime = null;	
	public $ip = '';
	public $content = '';
	

}

?>