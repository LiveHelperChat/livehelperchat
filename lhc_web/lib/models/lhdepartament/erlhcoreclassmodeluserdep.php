<?php

class erLhcoreClassModelUserDep {

    public function getState()
   {
       return array(
               'id'             => $this->id,
               'user_id'        => $this->user_id,
               'dep_id'         => $this->dep_id,
               'last_activity'  => $this->last_activity,
               'hide_online'    => $this->hide_online,
               'last_accepted'  => $this->last_accepted,
               'active_chats'   => $this->active_chats,
               'hide_online_ts' => $this->hide_online_ts
       );
   }

   public function setState( array $properties )
   {
       foreach ( $properties as $key => $val )
       {
           $this->$key = $val;
       }
   }

   public function __get($var) {
		switch ($var) {
			case 'user':
					$this->user = erLhcoreClassModelUser::fetch($this->user_id);
					return $this->user;
				break;
				
			case 'lastactivity_ago':
					$this->lastactivity_ago = erLhcoreClassChat::getAgoFormat($this->last_activity);
					return $this->lastactivity_ago;
				break;
				
			case 'offline_since':
					$this->offline_since = erLhcoreClassChat::getAgoFormat($this->hide_online_ts);
					return $this->offline_since;
				break;
				
			case 'name_support':
					$this->name_support = $this->user->name_support;
					return $this->name_support;
				break;

			case 'name_official':
					$this->name_official = $this->user->name_official;
					return $this->name_official;
				break;
							
			case 'departments_names':
			         $this->departments_names = array();
			         $ids = $this->user->departments_ids;	
  
			         if ($ids != '') {
    			         $parts = explode(',', $ids);
    			         sort($parts);

    			         foreach ($parts as $depId) {
    			             if ($depId == 0) {
    			                 $this->departments_names[] = '∞';
    			             } elseif ($depId > 0) {
    			                 try {
    			                     $dep = erLhcoreClassModelDepartament::fetch($depId,true);
    			                     $this->departments_names[] = $dep->name;
    			                 } catch (Exception $e) {
    			                     
    			                 }
    			             }
    			         }
			         }			         
			         return $this->departments_names;
			    break;
			    
			default:
				break;
		}
   }

   public static function getList($paramsSearch = array())
   {
	   	$paramsDefault = array('limit' => 32, 'offset' => 0);

	   	$params = array_merge($paramsDefault,$paramsSearch);

	   	$session = erLhcoreClassDepartament::getSession();
	   	$q = $session->createFindQuery( 'erLhcoreClassModelUserDep' );

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

   public static function getOnlineOperators($currentUser, $canListOnlineUsersAll = false, $params = array(), $limit = 10, $onlineTimeout = 120) {

	   	$LimitationDepartament = '';
	   	$userData = $currentUser->getUserData(true);
	   	$filter = array();
	   	
	   	if ($userData->all_departments == 0 && $canListOnlineUsersAll == false)
	   	{
	   		$userDepartaments = erLhcoreClassUserDep::getUserDepartaments($currentUser->getUserID());

	   		if (count($userDepartaments) == 0) return array();

	   		$index = array_search(-1, $userDepartaments);
	   		if ($index !== false){
	   			unset($userDepartaments[$index]);
	   		}
	   		
	   		$filter['customfilter'][] = '(dep_id IN ('.implode(',',$userDepartaments). ') OR user_id = ' . $currentUser->getUserID() . ')';
	   	};
	   	
	   	$filter['filtergt']['last_activity'] = time()-$onlineTimeout;
	   	$filter['limit'] = $limit;
	   	
	   	if (!isset($params['sort'])) {
	   	   $filter['sort'] = 'active_chats DESC, hide_online ASC';
	   	}
	   	
	   	$filter['groupby'] = 'user_id';

	   	$filter = array_merge_recursive($filter,$params);
	   	   	
	   	return self::getList($filter);

   }

   public $id = null;
   public $user_id = 0;
   public $dep_id = 0;
   public $hide_online_ts = 0;
   public $hide_online = 0;
   public $last_activity = 0;
   public $last_accepted = 0;
   public $active_chats = 0;
}

?>