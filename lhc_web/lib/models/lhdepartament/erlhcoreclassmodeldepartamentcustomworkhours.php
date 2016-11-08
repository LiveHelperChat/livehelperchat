<?php

class erLhcoreClassModelDepartamentCustomWorkHours {

   public function getState()
   {
       return array(
               'id'             => $this->id,
               'dep_id'         => $this->dep_id,
               'date_from'      => $this->date_from,
               'date_to'        => $this->date_to,
               'start_hour'     => $this->start_hour,
               'end_hour'       => $this->end_hour
       );
   }

    public function __get($var) {
        switch ($var) {
            case 'start_hour_front':
                return str_pad(floor($this->start_hour/100), 2, '0', STR_PAD_LEFT);
                break;

            case 'start_minutes_front':
                return str_pad($this->start_hour - ($this->start_hour_front * 100), 2, '0', STR_PAD_LEFT);
                break;

            case 'end_hour_front':
                return str_pad(floor($this->end_hour/100), 2, '0', STR_PAD_LEFT);
                break;

            case 'end_minutes_front':
                return str_pad($this->end_hour - ($this->end_hour_front * 100), 2, '0', STR_PAD_LEFT);
                break;

            default:
                ;
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

   public static function getList($paramsSearch = array())
   {
	   	$paramsDefault = array('limit' => 32, 'offset' => 0);

	   	$params = array_merge($paramsDefault,$paramsSearch);

	   	$session = erLhcoreClassDepartament::getSession();
	   	$q = $session->createFindQuery( 'erLhcoreClassModelDepartamentCustomWorkHours' );

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

	   	if (isset($params['customfilter']) && count($params['customfilter']) > 0)
	   	{
	   		foreach ($params['customfilter'] as $fieldValue)
	   		{
	   			$conditions[] = $fieldValue;
	   		}
	   	}

	   	if (count($conditions) > 0)
	   	{
	   		$q->where(
	   				$conditions
	   		);
	   	}

	   	if (isset($params['groupby']) )
	   	{
	   		$q->groupBy($params['groupby']);
	   	}

	   	$q->limit($params['limit'],$params['offset']);

	   	$q->orderBy(isset($params['sort']) ? $params['sort'] : 'id DESC' );

	   	$objects = $session->find( $q );

	   	return $objects;
   }

   public $id = null;
   public $dep_id = 0;
   public $date_from = 0;
   public $date_to = 0;
   public $start_hour = 0;
   public $end_hour = 0;
}

?>