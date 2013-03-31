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

   public static function getList($paramsSearch = array())
   {
	   	$paramsDefault = array('limit' => 32, 'offset' => 0);

	   	$params = array_merge($paramsDefault,$paramsSearch);

	   	$session = erLhcoreClassChat::getSession();
	   	$q = $session->createFindQuery( 'erLhcoreClassModelmsg' );

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

	   	if (count($conditions) > 0)
	   	{
	   		$q->where(
	   				$conditions
	   		);
	   	}

	   	$q->limit($params['limit'],$params['offset']);

	   	$q->orderBy(isset($params['sort']) ? $params['sort'] : 'id ASC' );

	   	$objects = $session->find( $q );

	   	return $objects;
   }

   public function __get($var) {

	   	switch ($var) {
	   		case 'time_front':
		   			if (date('Ymd') == date('Ymd',$this->time)) {
		   			     $this->time_front = date('H:i:s',$this->time);
		   			} else {
		   			     $this->time_front = date('Y-m-d H:i:s',$this->time);
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
    public $nick = '';
    public $time = '';
    public $chat_id = null;
    public $user_id = null;
    public $name_support = '';
}

?>