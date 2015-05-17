<?php

/**
 * Status -
 * 0 - Pending
 * 1 - Active
 * 2 - Closed
 * 3 - Blocked
 * */

class erLhcoreClassChat {

	public static $chatListIgnoreField = array(
			'remarks',			
			'unread_messages_informed',			
			'reinform_timeout',			
			'user_typing_txt',
			'hash',
			'ip',
			'user_status',
			'email',
			'support_informed',
			'phone',
			'user_typing',
			'operator_typing',
			'has_unread_messages',			
			'operation',			
			'operation_admin',			
			'screenshot_id',			
			'last_msg_id',
			'mail_send',
			'lat',
			'lon',
			'city',
			'additional_data',
			'session_referrer',
			'wait_time',
			'chat_duration',
			'priority',
			'online_user_id',
			'transfer_if_na',
			'transfer_timeout_ts',
			'transfer_timeout_ac',
			'wait_timeout',
			'wait_timeout_send',
			'timeout_message',
			'na_cb_executed',
			'nc_cb_executed',
			'fbst',
			'operator_typing_id',
			'chat_initiator',
			'chat_variables',
			'wait_timeout_repeat',
			// Angular remake
			'referrer'
	);
	
    /**
     * Gets pending chats
     */
    public static function getPendingChats($limit = 50, $offset = 0, $filterAdditional = array())
    {
    	$limitation = self::getDepartmentLimitation();

    	// Does not have any assigned department
    	if ($limitation === false) { return array(); }

    	$filter = array();
    	$filter['filter'] = array('status' => 0);

    	if ($limitation !== true) {
    		$filter['customfilter'][] = $limitation;
    		$filter['use_index'] = 'status_dep_id_priority_id';
    	} else {
    		$filter['use_index'] = 'status_priority_id';
    	}

    	$filter['limit'] = $limit;
    	$filter['offset'] = $offset;
    	$filter['smart_select'] = true;
    	$filter['sort'] = 'priority DESC, id DESC';

    	if (!empty($filterAdditional)) {
    		$filter = array_merge_recursive($filter,$filterAdditional);
    	}

    	return self::getList($filter);
    }


    public static function getPendingChatsCount($filterAdditional = array())
    {
    	$limitation = self::getDepartmentLimitation();

    	// Does not have any assigned department
    	if ($limitation === false) { return 0; }

    	$filter = array();
    	$filter['filter'] = array('status' => 0);

    	if ($limitation !== true) {
    		$filter['customfilter'][] = $limitation;
    		$filter['use_index'] = 'status_dep_id_id';
    	}

    	if (!empty($filterAdditional)) {
    		$filter = array_merge_recursive($filter,$filterAdditional);
    	}

    	return self::getCount($filter);
    }

    public static function getPendingChatsCountPublic($department = false)
    {
    	$filter = array();
    	$filter['filter'] = array('status' => 0);

    	if ($department !== false && is_numeric($department)) {
    		$filter['filter']['dep_id'] = $department;
    	} elseif ($department !== false && is_array($department)) {
    		$filter['filterin']['dep_id'] = $department;
    	}

    	return self::getCount($filter);
    }

    public static function getList($paramsSearch = array(), $class = 'erLhcoreClassModelChat', $tableName = 'lh_chat')
    {
	       $paramsDefault = array('limit' => 32, 'offset' => 0);

	       $params = array_merge($paramsDefault,$paramsSearch);

	       $session = erLhcoreClassChat::getSession();
	       $q = $session->createFindQuery( $class, isset($params['ignore_fields']) ? $params['ignore_fields'] : array() );

	       $conditions = array();

	       if (!isset($paramsSearch['smart_select'])) {
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

			      if (isset($params['filterlike']) && count($params['filterlike']) > 0)
			      {
			      	   foreach ($params['filterlike'] as $field => $fieldValue)
			      	   {
			      	   		$conditions[] = $q->expr->like( $field, $q->bindValue('%'.$fieldValue.'%') );
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

				 if (isset($params['use_index'])) {
		      		$q->useIndex( $params['use_index'] );
		      	 }

			      $q->limit($params['limit'],$params['offset']);

			      $q->orderBy(isset($params['sort']) ? $params['sort'] : 'id DESC' );
	      } else {

		      	$q2 = $q->subSelect();
		      	$q2->select( 'id' )->from( $tableName );

		      	if (isset($params['filter']) && count($params['filter']) > 0)
		      	{
		      		foreach ($params['filter'] as $field => $fieldValue)
		      		{
		      			$conditions[] = $q2->expr->eq( $field, $q->bindValue($fieldValue) );
		      		}
		      	}

		      	if (isset($params['filterlike']) && count($params['filterlike']) > 0)
		      	{
		      		foreach ($params['filterlike'] as $field => $fieldValue)
		      		{
		      			$conditions[] = $q->expr->like( $field, $q->bindValue('%'.$fieldValue.'%') );
		      		}
		      	}

		      	if (isset($params['filterin']) && count($params['filterin']) > 0)
		      	{
		      		foreach ($params['filterin'] as $field => $fieldValue)
		      		{
		      			$conditions[] = $q2->expr->in( $field, $fieldValue );
		      		}
		      	}

		      	if (isset($params['filterlt']) && count($params['filterlt']) > 0)
		      	{
		      		foreach ($params['filterlt'] as $field => $fieldValue)
		      		{
		      			$conditions[] = $q2->expr->lt( $field, $q->bindValue($fieldValue) );
		      		}
		      	}

		      	if (isset($params['filterlte']) && count($params['filterlte']) > 0)
		      	{
		      		foreach ($params['filterlte'] as $field => $fieldValue)
		      		{
		      			$conditions[] = $q2->expr->lte( $field, $q->bindValue($fieldValue) );
		      		}
		      	}

		      	if (isset($params['filtergt']) && count($params['filtergt']) > 0)
		      	{
		      		foreach ($params['filtergt'] as $field => $fieldValue)
		      		{
		      			$conditions[] = $q2->expr->gt( $field,$q->bindValue( $fieldValue) );
		      		}
		      	}

		      	if (isset($params['filtergte']) && count($params['filtergte']) > 0)
		      	{
		      		foreach ($params['filtergte'] as $field => $fieldValue)
		      		{
		      			$conditions[] = $q2->expr->gte( $field,$q->bindValue( $fieldValue) );
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
		      		$q2->where(
		      				$conditions
		      		);
		      	}

		      	if (isset($params['use_index'])) {
		      		$q2->useIndex( $params['use_index'] );
		      	}

		      	$q2->limit($params['limit'],$params['offset']);
		      	$q2->orderBy(isset($params['sort']) ? $params['sort'] : 'id DESC');

		      	$q->innerJoin( $q->alias( $q2, 'items' ), $tableName . '.id', 'items.id' );
		      	$q->orderBy(isset($params['sort']) ? $params['sort'] : 'id DESC' );
	      }

	      $objects = $session->find( $q );

	      return $objects;
    }

    public static function getCount($params = array(), $table = 'lh_chat', $operation = 'COUNT(id)')
    {
    	$session = erLhcoreClassChat::getSession();
    	$q = $session->database->createSelectQuery();
    	$q->select( $operation )->from( $table );
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

    public static function getDepartmentLimitation($tableName = 'lh_chat') {
    	$currentUser = erLhcoreClassUser::instance();
    	$LimitationDepartament = '';
    	$userData = $currentUser->getUserData(true);
    	if ( $userData->all_departments == 0 )
    	{
    		$userDepartaments = erLhcoreClassUserDep::getUserDepartaments($currentUser->getUserID());

    		if (count($userDepartaments) == 0) return false;

    		$LimitationDepartament = '('.$tableName.'.dep_id IN ('.implode(',',$userDepartaments).'))';

    		return $LimitationDepartament;
    	}

    	return true;
    }

    // Get's unread messages from users
    public static function getUnreadMessagesChats($limit = 10, $offset = 0, $filterAdditional = array()) {

    	$limitation = self::getDepartmentLimitation();

    	// Does not have any assigned department
    	if ($limitation === false) {
    		return array();
    	}

    	$filter = array();

    	$filter['filter'] = array('has_unread_messages' => 1);

    	if ($limitation !== true) {
    		$filter['customfilter'][] = $limitation;
    		$filter['use_index'] = 'has_unread_messages_dep_id_id';
    	}
    	
    	// Give 5 seconds to operator to sync a chat and avoid annoying him
    	$filter['filterlt']['last_user_msg_time'] = time()-5;
    	
    	$filter['limit'] = $limit;
    	$filter['offset'] = $offset;
    	$filter['smart_select'] = true;

    	if (!empty($filterAdditional)) {
    		$filter = array_merge_recursive($filter,$filterAdditional);
    	}

    	return self::getList($filter);
    }

    // Get's unread messages from users | COUNT
    public static function getUnreadMessagesChatsCount($filterAdditional = array()) {

    	$limitation = self::getDepartmentLimitation();

    	// Does not have any assigned department
    	if ($limitation === false) {
    		return 0;
    	}

    	$filter = array();

    	$filter['filter'] = array('has_unread_messages' => 1);

    	if ($limitation !== true) {
    		$filter['customfilter'][] = $limitation;
    		$filter['use_index'] = 'has_unread_messages_dep_id_id';
    	}

    	if (!empty($filterAdditional)) {
    		$filter = array_merge_recursive($filter,$filterAdditional);
    	}

    	return self::getCount($filter);
    }

    public static function getActiveChats($limit = 50, $offset = 0, $filterAdditional = array())
    {
    	$limitation = self::getDepartmentLimitation();

    	// Does not have any assigned department
    	if ($limitation === false) { return array(); }

    	$filter = array();
    	$filter['filter'] = array('status' => 1);

    	if ($limitation !== true) {
    		$filter['customfilter'][] = $limitation;
    		$filter['use_index'] = 'status_dep_id_id';
    	}

    	$filter['limit'] = $limit;
    	$filter['offset'] = $offset;
    	$filter['smart_select'] = true;

    	if (!empty($filterAdditional)) {
    		$filter = array_merge_recursive($filter,$filterAdditional);
    	}

    	return self::getList($filter);
    }

    public static function getActiveChatsCount($filterAdditional = array())
    {
    	$limitation = self::getDepartmentLimitation();

    	// Does not have any assigned department
    	if ($limitation === false) { return 0; }

    	$filter = array();
    	$filter['filter'] = array('status' => 1);

    	if ($limitation !== true) {
    		$filter['customfilter'][] = $limitation;
    		$filter['use_index'] = 'status_dep_id_id';
    	}

    	if (!empty($filterAdditional)) {
    		$filter = array_merge_recursive($filter,$filterAdditional);
    	}

    	return self::getCount($filter);
    }

    public static function getClosedChats($limit = 50, $offset = 0, $filterAdditional = array())
    {
    	$limitation = self::getDepartmentLimitation();

    	// Does not have any assigned department
    	if ($limitation === false) { return array(); }

    	$filter = array();
    	$filter['filter'] = array('status' => 2);

    	if ($limitation !== true) {
    		$filter['customfilter'][] = $limitation;
    		$filter['use_index'] = 'status_dep_id_id';
    	}

    	$filter['limit'] = $limit;
    	$filter['offset'] = $offset;
    	$filter['smart_select'] = true;

    	if (!empty($filterAdditional)) {
    		$filter = array_merge_recursive($filter,$filterAdditional);
    	}

    	return self::getList($filter);
    }

    public static function getClosedChatsCount($filterAdditional = array())
    {
    	$limitation = self::getDepartmentLimitation();

    	// Does not have any assigned department
    	if ($limitation === false) { return 0; }

    	$filter = array();
    	$filter['filter'] = array('status' => 2);

    	if ($limitation !== true) {
    		$filter['customfilter'][] = $limitation;
    		$filter['use_index'] = 'status_dep_id_id';
    	}

    	if (!empty($filterAdditional)) {
    		$filter = array_merge_recursive($filter,$filterAdditional);
    	}

    	return self::getCount($filter);
    }

    public static function getOperatorsChats($limit = 50, $offset = 0, $filterAdditional = array())
    {
    	$limitation = self::getDepartmentLimitation();

    	// Does not have any assigned department
    	if ($limitation === false) { return array(); }

    	$filter = array();
    	$filter['filter'] = array('status' => 4);

    	if ($limitation !== true) {
    		$filter['customfilter'][] = $limitation;
    		$filter['use_index'] = 'status_dep_id_id';
    	}

    	$filter['limit'] = $limit;
    	$filter['offset'] = $offset;
    	$filter['smart_select'] = true;

    	if (!empty($filterAdditional)) {
    		$filter = array_merge_recursive($filter, $filterAdditional);
    	}

    	return self::getList($filter);
    }

    public static function getOperatorsChatsCount($filterAdditional = array())
    {
    	$limitation = self::getDepartmentLimitation();

    	// Does not have any assigned department
    	if ($limitation === false) { return 0; }

    	$filter = array();
    	$filter['filter'] = array('status' => 4);

    	if ($limitation !== true) {
    		$filter['customfilter'][] = $limitation;
    		$filter['use_index'] = 'status_dep_id_id';
    	}

    	if (!empty($filterAdditional)) {
    		$filter = array_merge_recursive($filter, $filterAdditional);
    	}

    	return self::getCount($filter);
    }

    public static function isOnline($dep_id = false, $exclipic = false, $params = array())
    {
       $isOnlineUser = isset($params['online_timeout']) ? $params['online_timeout'] : (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'];
       $ignoreUserStatus = (isset($params['ignore_user_status']) && $params['ignore_user_status'] == 1) ? true : false;
       
       $db = ezcDbInstance::get();
	   $rowsNumber = 0;
       
       if ($dep_id !== false) {
       		$exclipicFilter = ($exclipic == false) ? ' OR dep_id = 0' : '';
       		
       		if ($ignoreUserStatus === false) {       		
				if (is_numeric($dep_id)) {
		           $stmt = $db->prepare("SELECT COUNT(id) AS found FROM lh_userdep WHERE (last_activity > :last_activity AND hide_online = 0) AND (dep_id = :dep_id {$exclipicFilter})");
		           $stmt->bindValue(':dep_id',$dep_id,PDO::PARAM_INT);
		           $stmt->bindValue(':last_activity',(time()-$isOnlineUser),PDO::PARAM_INT);
				} elseif ( is_array($dep_id) ) {
					$stmt = $db->prepare('SELECT COUNT(id) AS found FROM lh_userdep WHERE (last_activity > :last_activity AND hide_online = 0) AND (dep_id IN ('. implode(',', $dep_id) .") {$exclipicFilter})");
					$stmt->bindValue(':last_activity',(time()-$isOnlineUser),PDO::PARAM_INT);
				}
				$stmt->execute();
				$rowsNumber = $stmt->fetchColumn();	
       		}
			
			if ($rowsNumber == 0) { // Perhaps auto active is turned on for some of departments							
				$daysColumns = array('`mod`','`tud`','`wed`','`thd`','`frd`','`sad`','`sud`');
				$columns = date('N')-1;
				
				if (is_numeric($dep_id)) {
					$stmt = $db->prepare("SELECT COUNT(id) AS found FROM lh_departament WHERE online_hours_active = 1 AND start_hour <= :start_hour AND end_hour > :end_hour AND {$daysColumns[$columns]} = 1 AND id = :dep_id");
					$stmt->bindValue(':dep_id',$dep_id);
				} elseif (is_array($dep_id)) {
					$stmt = $db->prepare("SELECT COUNT(id) AS found FROM lh_departament WHERE online_hours_active = 1 AND start_hour <= :start_hour AND end_hour > :end_hour AND {$daysColumns[$columns]} = 1 AND id IN (". implode(',', $dep_id) .")");
				}
				
				$stmt->bindValue(':start_hour',date('G').date('i'),PDO::PARAM_INT);
				$stmt->bindValue(':end_hour',date('G').date('i'),PDO::PARAM_INT);
				$stmt->execute();
				$rowsNumber = $stmt->fetchColumn();
			}					

       } else {
       	
	       	if ($ignoreUserStatus === false) {
	           $stmt = $db->prepare('SELECT COUNT(id) AS found FROM lh_userdep WHERE last_activity > :last_activity AND hide_online = 0');
	           $stmt->bindValue(':last_activity',(time()-$isOnlineUser),PDO::PARAM_INT);
	           $stmt->execute();
	           $rowsNumber = $stmt->fetchColumn();        
	       }
	       	         
           if ($rowsNumber == 0){ // Perhaps auto active is turned on for some of departments
           		$daysColumns = array('`mod`','`tud`','`wed`','`thd`','`frd`','`sad`','`sud`');           		
           		$columns = date('N')-1;           		
	           	$stmt = $db->prepare("SELECT COUNT(id) AS found FROM lh_departament WHERE online_hours_active = 1 AND start_hour <= :start_hour AND end_hour > :end_hour AND {$daysColumns[$columns]} = 1");
	           	$stmt->bindValue(':start_hour',date('G').date('i'),PDO::PARAM_INT);
	           	$stmt->bindValue(':end_hour',date('G').date('i'),PDO::PARAM_INT);
	           	$stmt->execute();
	           	$rowsNumber = $stmt->fetchColumn();	   
           }
       }

       return $rowsNumber >= 1;
    }

    public static function getRandomOnlineUserID($params = array()) {
    	$isOnlineUser = isset($params['online_timeout']) ? $params['online_timeout'] : (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'];
    	
    	$db = ezcDbInstance::get();
		$agoTime = time()-$isOnlineUser;

		$filterOperators = '';
		if ( isset($params['operators']) && !empty($params['operators']) ) {
			$operators = array();
			foreach ($params['operators'] as $operatorID) {
				if ((int)$operatorID > 0){
					$operators[] = (int)$operatorID;
				}
			}
						
			if (!empty($operators)){
				$filterOperators = ' AND lh_users.id IN ('.implode(',',$operators).')';
			}
		}
		
    	$SQL = 'SELECT count(*) FROM (SELECT count(lh_users.id) FROM lh_users INNER JOIN lh_userdep ON lh_userdep.user_id = lh_users.id WHERE lh_userdep.last_activity > :last_activity AND lh_userdep.hide_online = 0 ' . $filterOperators . ' GROUP BY lh_users.id) as online_users';
    	$stmt = $db->prepare($SQL);
    	$stmt->bindValue(':last_activity',$agoTime,PDO::PARAM_INT);
    	$stmt->execute();
    	$count = $stmt->fetchColumn();

    	if ($count > 0){
	    	$offsetRandom = rand(0, $count-1);

	    	$SQL = "SELECT lh_users.id FROM lh_users INNER JOIN lh_userdep ON lh_userdep.user_id = lh_users.id WHERE lh_userdep.last_activity > :last_activity AND lh_userdep.hide_online = 0 {$filterOperators} GROUP BY lh_users.id LIMIT 1 OFFSET {$offsetRandom}";
	    	$stmt = $db->prepare($SQL);
	    	$stmt->bindValue(':last_activity',$agoTime,PDO::PARAM_INT);
	    	$stmt->execute();

	    	return $stmt->fetchColumn();
    	}

    	return 0;
    }

    public static function getOnlineUsers($UserID = array(), $params = array())
    {     
       $isOnlineUser = isset($params['online_timeout']) ? $params['online_timeout'] : (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'];
       
       
       $db = ezcDbInstance::get();
       $NotUser = '';

       if (count($UserID) > 0)
       {
           $NotUser = ' AND lh_users.id NOT IN ('.implode(',',$UserID).')';
       }

       $limitationSQL = '';

       if (!erLhcoreClassUser::instance()->hasAccessTo('lhchat','allowtransfertoanyuser')){
	       // User can see online only his department users
	       $limitation = self::getDepartmentLimitation('lh_userdep');

	       // Does not have any assigned department
	       if ($limitation === false) { return array(); }

	       if ($limitation !== true) {
	       		$limitationSQL = ' AND '.$limitation;
	       }
       }

       $SQL = 'SELECT lh_users.* FROM lh_users INNER JOIN lh_userdep ON lh_userdep.user_id = lh_users.id WHERE lh_userdep.last_activity > :last_activity '.$NotUser.$limitationSQL.' GROUP BY lh_users.id';
       $stmt = $db->prepare($SQL);

       $stmt->bindValue(':last_activity',(time()-$isOnlineUser),PDO::PARAM_INT);

       $stmt->execute();
       $rows = $stmt->fetchAll();
       return $rows;
    }

    public static function isOnlineUser($user_id, $params = array()) {    	
    	$isOnlineUser = isset($params['online_timeout']) ? $params['online_timeout'] : (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'];
    	    	
    	$db = ezcDbInstance::get();

    	$stmt = $db->prepare('SELECT count(lh_users.id) FROM lh_users INNER JOIN lh_userdep ON lh_userdep.user_id = lh_users.id WHERE lh_userdep.last_activity > :last_activity AND lh_users.id = :user_id');
    	$stmt->bindValue(':last_activity',(time()-$isOnlineUser),PDO::PARAM_INT);
    	$stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
    	$stmt->execute();

    	$rows = $stmt->fetchColumn();

    	return $rows > 0;
    }


   /**
    * All messages, which should get administrator/user
    *
    * */
   public static function getPendingMessages($chat_id,$message_id)
   {
       $db = ezcDbInstance::get();
       $stmt = $db->prepare('SELECT lh_msg.* FROM lh_msg INNER JOIN ( SELECT id FROM lh_msg WHERE chat_id = :chat_id AND id > :message_id ORDER BY id ASC) AS items ON lh_msg.id = items.id');
       $stmt->bindValue( ':chat_id',$chat_id,PDO::PARAM_INT);
       $stmt->bindValue( ':message_id',$message_id,PDO::PARAM_INT);
       $stmt->setFetchMode(PDO::FETCH_ASSOC);
       $stmt->execute();
       $rows = $stmt->fetchAll();

       return $rows;
   }


   /**
    * All messages, which should get administrator/user for chatbox
    *
    * */
   public static function getPendingMessagesChatbox($chat_id,$message_id)
   {
       $db = ezcDbInstance::get();
       $stmt = $db->prepare('SELECT lh_msg.* FROM lh_msg INNER JOIN ( SELECT id FROM lh_msg WHERE chat_id = :chat_id AND id >= :message_id ORDER BY id ASC) AS items ON lh_msg.id = items.id');
       $stmt->bindValue( ':chat_id',$chat_id,PDO::PARAM_INT);
       $stmt->bindValue( ':message_id',$message_id,PDO::PARAM_INT);
       $stmt->setFetchMode(PDO::FETCH_ASSOC);
       $stmt->execute();
       $rows = $stmt->fetchAll();

       return $rows;
   }
   
   /**
    * Get last message for chatbox
    *
    * */
   public static function getGetLastChatMessage($chat_id)
   {
       $db = ezcDbInstance::get();
       $stmt = $db->prepare('SELECT lh_msg.* FROM lh_msg INNER JOIN ( SELECT id FROM lh_msg WHERE chat_id = :chat_id ORDER BY id DESC LIMIT 1 OFFSET 0) AS items ON lh_msg.id = items.id');
       $stmt->bindValue( ':chat_id',$chat_id,PDO::PARAM_INT);
       $stmt->setFetchMode(PDO::FETCH_ASSOC);
       $stmt->execute();
       $row = $stmt->fetch();

       return $row;
   }
   
   
   /**
    * Get last message for chat editing admin last message
    *
    * */
   public static function getGetLastChatMessageEdit($chat_id, $user_id)
   {
       $db = ezcDbInstance::get();
       $stmt = $db->prepare('SELECT lh_msg.* FROM lh_msg INNER JOIN ( SELECT id FROM lh_msg WHERE chat_id = :chat_id AND user_id = :user_id ORDER BY id DESC LIMIT 1 OFFSET 0) AS items ON lh_msg.id = items.id');
       $stmt->bindValue( ':chat_id',$chat_id,PDO::PARAM_INT);
       $stmt->bindValue( ':user_id',$user_id,PDO::PARAM_INT);
       $stmt->setFetchMode(PDO::FETCH_ASSOC);
       $stmt->execute();
       $row = $stmt->fetch();

       return $row;
   }
   
   
   
   /**
    * Get last message for browser notification
    *
    * */
   public static function getGetLastChatMessagePending($chat_id)
   {
       $db = ezcDbInstance::get();
       $stmt = $db->prepare('SELECT lh_msg.msg FROM lh_msg INNER JOIN ( SELECT id FROM lh_msg WHERE chat_id = :chat_id ORDER BY id DESC LIMIT 3 OFFSET 0) AS items ON lh_msg.id = items.id');
       $stmt->bindValue( ':chat_id',$chat_id,PDO::PARAM_INT);
       $stmt->setFetchMode(PDO::FETCH_ASSOC);
       $stmt->execute();
       $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
       $text = mb_substr(implode("\n", array_reverse($rows)),-200);
       
       return $text;
   }


   /**
    * Gets chats messages, used to review chat etc.
    * */
   public static function getChatMessages($chat_id)
   {
   	   $db = ezcDbInstance::get();
       $stmt = $db->prepare('SELECT lh_msg.* FROM lh_msg INNER JOIN ( SELECT id FROM lh_msg WHERE chat_id = :chat_id ORDER BY id ASC) AS items ON lh_msg.id = items.id');
       $stmt->bindValue( ':chat_id',$chat_id,PDO::PARAM_INT);
       $stmt->setFetchMode(PDO::FETCH_ASSOC);
       $stmt->execute();
       $rows = $stmt->fetchAll();
       return $rows;
   }

   /**
    * Get first user mesasge for prefilling chat
    * */
   public static function getFirstUserMessage($chat_id)
   {
	   	$db = ezcDbInstance::get();
	   	$stmt = $db->prepare('SELECT lh_msg.msg FROM lh_msg INNER JOIN ( SELECT id FROM lh_msg WHERE chat_id = :chat_id AND user_id = 0 ORDER BY id ASC LIMIT 1) AS items ON lh_msg.id = items.id');
	   	$stmt->bindValue( ':chat_id',$chat_id,PDO::PARAM_INT);
	   	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	   	$stmt->execute();  
	   	return $stmt->fetchColumn();
   }
   
   public static function hasAccessToRead($chat)
   {
       $currentUser = erLhcoreClassUser::instance();

       $userData = $currentUser->getUserData(true);

       if ( $userData->all_departments == 0 ) {

            /*
             * --From now permission is strictly by assigned department, not by chat owner
             *
             * Finally decided to keep this check, it allows more advance permissions configuration
             * */

       		if ($chat->user_id == $currentUser->getUserID()) return true;

            $userDepartaments = erLhcoreClassUserDep::getUserDepartaments($currentUser->getUserID());

            if (count($userDepartaments) == 0) return false;

            if (in_array($chat->dep_id,$userDepartaments)) {

            	if ($currentUser->hasAccessTo('lhchat','allowopenremotechat') == true || $chat->status == erLhcoreClassModelChat::STATUS_OPERATORS_CHAT){
            		return true;
            	} elseif ($chat->user_id == 0 || $chat->user_id == $currentUser->getUserID()) {
            		return true;
            	}

            	return false;
            }

            return false;
       }

       return true;
   }

   public static function formatSeconds($seconds) {

	    $y = floor($seconds / (86400*365.25));
	    $d = floor(($seconds - ($y*(86400*365.25))) / 86400);
	    $h = gmdate('H', $seconds);
	    $m = gmdate('i', $seconds);
	    $s = gmdate('s', $seconds);

	    $parts = array();

	    if($y > 0)
	    {
	    	$parts[] = $y . ' .y';
	    }

	    if($d > 0)
	    {
	    	$parts[] = $d . ' d.';
	    }

	    if($h > 0)
	    {
	    	$parts[] = $h . ' h.';
	    }

	    if($m > 0)
	    {
	    	$parts[] = $m . ' m.';
	    }

	    if($s > 0)
	    {
	    	$parts[] = $s . ' s.';
	    }

	    return implode($parts,' ');
   }

   /**
    * Is chat activated and user can send messages.
    *
    * */
   public static function isChatActive($chat_id,$hash)
   {
       $db = ezcDbInstance::get();
       $stmt = $db->prepare('SELECT COUNT(id) AS found FROM lh_chat WHERE id = :chat_id AND hash = :hash AND status = 1');
       $stmt->bindValue( ':chat_id',$chat_id,PDO::PARAM_INT);
       $stmt->bindValue( ':hash',$hash);

       $stmt->execute();
       $rows = $stmt->fetchAll();
       return $rows[0]['found'] == 1;
   }

   public static function generateHash()
   {
       return sha1(mt_rand().time());
   }
   
   public static function setTimeZoneByChat($chat)
   {
   		if ($chat->user_tz_identifier != '') {
   			erLhcoreClassModule::$defaultTimeZone = $chat->user_tz_identifier;
   			date_default_timezone_set(erLhcoreClassModule::$defaultTimeZone);   			
   		} 
   }
   
   public static function getSession()
   {
        if ( !isset( self::$persistentSession ) )
        {
            self::$persistentSession = new ezcPersistentSession(
                ezcDbInstance::get(),
                new ezcPersistentCodeManager( './pos/lhchat' )
            );
        }
        return self::$persistentSession;
   }

   public static function formatDate($ts) {
	   	if (date('Ymd') == date('Ymd',$ts)) {
	   		return date(erLhcoreClassModule::$dateHourFormat,$ts);
	   	} else {
	   		return date(erLhcoreClassModule::$dateDateHourFormat,$ts);
	   	}	  
   }
   
   public static function closeChatCallback($chat, $operator = false) {
	   	$extensions = erConfigClassLhConfig::getInstance()->getSetting( 'site', 'extensions' );

	   	$instance = erLhcoreClassSystem::instance();

	   	foreach ($extensions as $ext) {
	   		$callbackFile = $instance->SiteDir . '/extension/' . $ext . '/callbacks/close_chat.php';
	   		if (file_exists($callbackFile)) {
	   			include $callbackFile;
	   		}
	   	}
	   	
	   	erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.close',array('chat' => & $chat, 'user_data' => $operator));
	   	
	   	$dep = $chat->department;
	   	
	   	if ( $dep !== false) {
	   	    self::updateDepartmentStats($dep);
	   	}
	   	
	   	if ( $dep !== false && $dep->inform_close == 1) {
	   		erLhcoreClassChatMail::informChatClosed($chat, $operator);
	   	}
   }

   /**
    * Update department main statistic for frontend
    * */
   public static function updateDepartmentStats($dep) {
       $db = ezcDbInstance::get();
       $stmt = $db->prepare('UPDATE lh_departament SET active_chats_counter = :active_chats_counter, pending_chats_counter = :pending_chats_counter, closed_chats_counter = :closed_chats_counter WHERE id = :id');
       $stmt->bindValue(':active_chats_counter',erLhcoreClassChat::getCount(array('filter' => array('dep_id' => $dep->id, 'status' => erLhcoreClassModelChat::STATUS_ACTIVE_CHAT))),PDO::PARAM_INT);
       $stmt->bindValue(':pending_chats_counter',erLhcoreClassChat::getCount(array('filter' => array('dep_id' => $dep->id, 'status' => erLhcoreClassModelChat::STATUS_PENDING_CHAT))),PDO::PARAM_INT);
       $stmt->bindValue(':closed_chats_counter',erLhcoreClassChat::getCount(array('filter' => array('dep_id' => $dep->id, 'status' => erLhcoreClassModelChat::STATUS_CLOSED_CHAT))),PDO::PARAM_INT);
       $stmt->bindValue(':id',$dep->id,PDO::PARAM_INT);
       $stmt->execute();
       
   }
   
   public static function canReopen(erLhcoreClassModelChat $chat, $skipStatusCheck = false) {
   		if ( ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT || $skipStatusCheck == true)) {
			if ($chat->last_user_msg_time > time()-600 || $chat->last_user_msg_time == 0){
				return true;
			} else {
				return false;
			}
   		}
   		return false;
   }

   public static function canReopenDirectly($params = array()) {
	   	if (($chatPart = CSCacheAPC::getMem()->getSession('chat_hash_widget_resume',true)) !== false) {
	   		try {
		   		$parts = explode('_', $chatPart);
		   		$chat = erLhcoreClassModelChat::fetch($parts[0]);
		   		
		   		if ( ($chat->last_user_msg_time > time()-600 || $chat->last_user_msg_time == 0) && (!isset($params['reopen_closed']) || $params['reopen_closed'] == 1 || ($params['reopen_closed'] == 0 && $chat->status != erLhcoreClassModelChat::STATUS_CLOSED_CHAT))) {
		   			return array('id' => $parts[0],'hash' => $parts[1]);
		   		} else {
					return false;
				}

	   		} catch (Exception $e) {
	   			return false;
	   		}
	   	}

	   	return false;
   }

   /**
    * Is there any better way to initialize __get variables?
    * */
   public static function prefillGetAttributes(& $objects, $attrs = array(),$attrRemove = array(), $params = array()) {   		
   		foreach ($objects as & $object) {
   			foreach ($attrs as $attr) {
   				$object->{$attr};
   			};
   			
   			foreach ($attrRemove as $attr) {
   				$object->{$attr} = null;
   			};
   			
   			if (isset($params['remove_all']) && $params['remove_all'] == true) {
   			    foreach ($object as $attr => $value) {
   			        if (!in_array($attr, $attrs)) {
   			            $object->$attr = null;
   			        }
   			    }
   			}
   			
   			if (!isset($params['do_not_clean']))
   			$object = (object)array_filter((array)$object);
   		}
   }

   /**
    * Is there any better way to initialize __get variables?
    * */
   public static function prefillGetAttributesObject(& $object, $attrs = array(),$attrRemove = array(), $params = array()) {   		
   	
   			foreach ($attrs as $attr) {
   				$object->{$attr};
   			};
   			
   			foreach ($attrRemove as $attr) {
   				$object->{$attr} = null;
   			};
   			
   			if (!isset($params['do_not_clean']))
   			$object = (object)array_filter((array)$object);   		
   }
   
   public static function validateFilterIn(& $params) {
   		foreach ($params as & $param) {
   			$param = (int)$param;
   		}
   }
   
   public static function updateActiveChats($user_id)
   {
	   	$db = ezcDbInstance::get();
	   	$stmt = $db->prepare('UPDATE lh_userdep SET active_chats = :active_chats WHERE user_id = :user_id');
	   	$stmt->bindValue(':active_chats',erLhcoreClassChat::getCount(array('filter' => array('user_id' => $user_id, 'status' => erLhcoreClassModelChat::STATUS_ACTIVE_CHAT))),PDO::PARAM_INT);
	   	$stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
	   	$stmt->execute();
   }
   
   public static function getAdjustment($geo_adjustment, $onlineUserVid = '', $widgetMode = false, $onlineUserDefined = false){
   	
   		$responseStatus = array('status' => 'normal');
   		$onlineUser = false;
   		
	   	if (isset($geo_adjustment['use_geo_adjustment']) && $geo_adjustment['use_geo_adjustment'] == true){
	   	
	   		if ($widgetMode === true && $geo_adjustment['apply_widget'] == 0){
	   			return $responseStatus;
	   		}
	   		
	   		if (is_object($onlineUserDefined)){
	   			$onlineUser = $onlineUserDefined;
	   		} elseif (!empty($onlineUserVid)){
	   			$onlineUser = erLhcoreClassModelChatOnlineUser::fetchByVid($onlineUserVid);
	   		}
	   			   		
	   		if ($onlineUser === false) {	   		
		   		$onlineUser = new erLhcoreClassModelChatOnlineUser(); // Just to pass instance
		   		$onlineUser->ip = erLhcoreClassIPDetect::getIP();
		   		erLhcoreClassModelChatOnlineUser::detectLocation($onlineUser);
	   		}
	   			   		
	   		$countriesAvailableFor = array();
	   		if ($geo_adjustment['available_for'] != '') {
	   			$countriesAvailableFor = explode(',', $geo_adjustment['available_for']);
	   		}
	   	
	   		if (!in_array($onlineUser->user_country_code, $countriesAvailableFor)){
	   			if ($geo_adjustment['other_countries'] == 'all') {
	   				if (($geo_adjustment['other_status']) == 'offline'){	   				
	   					$responseStatus = array('status' => 'offline');
	   				} else {
	   					$responseStatus = array('status' => 'hidden');
	   				}
	   			} else {
	   				if ($geo_adjustment['hide_for'] != '') {
	   					$countrieshideFor = explode(',', $geo_adjustment['hide_for']);
	   					if (in_array($onlineUser->user_country_code, $countrieshideFor)){
	   						if (($geo_adjustment['other_status']) == 'offline'){
	   							$responseStatus = array('status' => 'offline');
	   						} else {
	   							$responseStatus = array('status' => 'hidden');
	   						}
	   					} else {
	   						if (($geo_adjustment['rest_status']) == 'offline'){
	   							$responseStatus = array('status' => 'offline');
	   						} elseif ($geo_adjustment['rest_status'] == 'normal') {
	   							$responseStatus = array('status' => 'normal');
	   						} else {
	   							$responseStatus = array('status' => 'hidden');
	   						}
	   					}
	   				} else {
	   					if (($geo_adjustment['rest_status']) == 'offline'){
   							$responseStatus = array('status' => 'offline');
	   					} elseif ($geo_adjustment['rest_status'] == 'normal') {
   							$responseStatus = array('status' => 'normal');
   						} else {
   							$responseStatus = array('status' => 'hidden');
   						}
	   				}
	   			}
	   		} // Normal status
	   	}

	   	return $responseStatus;
   }
   
   public static function getChatDurationToUpdateChatID($chatID){
	   	$sql = 'SELECT ((SELECT MAX(lh_msg.time) FROM lh_msg WHERE lh_msg.chat_id = lh_chat.id AND lh_msg.user_id = 0)-(lh_chat.time+lh_chat.wait_time)) AS chat_duration_counted FROM lh_chat WHERE lh_chat.id = :chat_id';
	   	$db = ezcDbInstance::get();
	   	$stmt = $db->prepare($sql);
	   	$stmt->bindValue(':chat_id',$chatID);
	   	$stmt->execute();
	   	$time = $stmt->fetchColumn();
   	   	return $time > 0 ? $time : 0;   		
   }
   
   private static $persistentSession;
}

?>