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
			//'user_status',
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
			//'online_user_id',
			'transfer_if_na',
			'transfer_timeout_ts',
			'transfer_timeout_ac',

			'na_cb_executed',
			'nc_cb_executed',
			'fbst',
			'operator_typing_id',
			'chat_initiator',
			'chat_variables',
			// Angular remake
			'referrer',
			'last_op_msg_time',
			'has_unread_op_messages',
			'unread_op_messages_informed',
			'tslasign',
			'user_closed_ts',
			'usaccept',
			'auto_responder_id',
			//'product_id'
	);
	
    /**
     * Gets pending chats
     */
    public static function getPendingChats($limit = 50, $offset = 0, $filterAdditional = array(), $filterAdditionalMainAttr = array(), $limitationDepartment = array())
    {
    	$limitation = self::getDepartmentLimitation('lh_chat',$limitationDepartment);

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
    	$filter['sort'] = isset($filterAdditionalMainAttr['sort']) ? $filterAdditionalMainAttr['sort'] : 'priority DESC, id DESC';

    	if (!empty($filterAdditional)) {
    		$filter = array_merge_recursive($filter,$filterAdditional);
    	}
    	    	
    	return self::getList($filter);
    }

    /**
     * @desc returns chats list for my active chats
     * 
     * @param number $limit
     * @param number $offset
     * @param unknown $filterAdditional
     * @param unknown $filterAdditionalMainAttr
     * @param unknown $limitationDepartment
     * @return multitype:|array(object($class))
     */
    public static function getMyChats($limit = 50, $offset = 0, $filterAdditional = array(), $filterAdditionalMainAttr = array(), $limitationDepartment = array())
    {
        $limitation = self::getDepartmentLimitation('lh_chat',$limitationDepartment);
        
        // Does not have any assigned department
        if ($limitation === false) { return array(); }
        
        $filter = array();
        $filter['filterin'] = array('status' => array(0,1));
        
        if ($limitation !== true) {
            $filter['customfilter'][] = $limitation;
        }
        
        $filter['limit'] = $limit;
        $filter['offset'] = $offset;
        $filter['smart_select'] = true;
        $filter['sort'] = 'status ASC, id DESC';
        
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

                  if (isset($params['use_index'])) {
                       $q->useIndex( $params['use_index'] );
                  }

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

                  if (isset($params['innerjoin']) && count($params['innerjoin']) > 0) {
                       foreach ($params['innerjoin'] as $table => $joinOn) {
                          $q->innerJoin($table, $q->expr->eq($joinOn[0], $joinOn[1]));
                       }
                  }

			      if (count($conditions) > 0)
			      {
			          $q->where(
			                     $conditions
			          );
			      }
               $q->limit($params['limit'],$params['offset']);

			      $q->orderBy(isset($params['sort']) ? $params['sort'] : 'id DESC' );
	      } else {

		      	$q2 = $q->subSelect();
		      	$q2->select( $tableName . '.id' )->from( $tableName );

                if (isset($params['use_index'])) {
                   $q2->useIndex( $params['use_index'] );
                }

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

                if (isset($params['innerjoin']) && count($params['innerjoin']) > 0) {
                   foreach ($params['innerjoin'] as $table => $joinOn) {
                       $q2->innerJoin($table, $q->expr->eq($joinOn[0], $joinOn[1]));
                   }
                }

		      	if (count($conditions) > 0)
		      	{
		      		$q2->where(
		      				$conditions
		      		);
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

    public static function getDepartmentLimitation($tableName = 'lh_chat', $params = array()) {
    	
    	$LimitationDepartament = '';
    	
    	if (!isset($params['user'])) {
        	$currentUser = erLhcoreClassUser::instance();
        	$userData = $currentUser->getUserData(true);
        	$userId = $currentUser->getUserID();
    	} else {
    	    $userData = $params['user'];
    	    $userId = $userData->id;
    	}
    	
    	
    	if ( $userData->all_departments == 0 )
    	{
    		$userDepartaments = erLhcoreClassUserDep::getUserDepartaments($userId);

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
		           $stmt = $db->prepare("SELECT COUNT(lh_userdep.id) AS found FROM lh_userdep INNER JOIN lh_departament ON lh_departament.id = :dep_id_dest WHERE (lh_departament.pending_group_max = 0 || lh_departament.pending_group_max > lh_departament.pending_chats_counter) AND (lh_departament.pending_max = 0 || lh_departament.pending_max > lh_departament.pending_chats_counter) AND (last_activity > :last_activity AND hide_online = 0) AND (dep_id = :dep_id {$exclipicFilter})");
		           $stmt->bindValue(':dep_id',$dep_id,PDO::PARAM_INT);
		           $stmt->bindValue(':dep_id_dest',$dep_id,PDO::PARAM_INT);
		           $stmt->bindValue(':last_activity',(time()-$isOnlineUser),PDO::PARAM_INT);
				} elseif ( is_array($dep_id) ) {
					if (empty($dep_id)) {
						$dep_id = array(-1);
					}
					$stmt = $db->prepare('SELECT COUNT(lh_userdep.id) AS found FROM lh_userdep, lh_departament WHERE lh_departament.id IN ('. implode(',', $dep_id) .') AND (lh_departament.pending_group_max = 0 || lh_departament.pending_group_max > lh_departament.pending_chats_counter) AND (lh_departament.pending_max = 0 || lh_departament.pending_max > lh_departament.pending_chats_counter) AND (last_activity > :last_activity AND hide_online = 0) AND (dep_id IN ('. implode(',', $dep_id) .") {$exclipicFilter})");
					$stmt->bindValue(':last_activity',(time()-$isOnlineUser),PDO::PARAM_INT);
				}
				$stmt->execute();
				$rowsNumber = $stmt->fetchColumn();	
       		}

			if ($rowsNumber == 0) { // Perhaps auto active is turned on for some of departments
                if (is_numeric($dep_id)) {
                    $stmt = $db->prepare("SELECT lh_departament_custom_work_hours.start_hour, lh_departament_custom_work_hours.end_hour FROM lh_departament_custom_work_hours INNER JOIN lh_departament ON lh_departament.id = lh_departament_custom_work_hours.dep_id WHERE (lh_departament.pending_group_max = 0 || lh_departament.pending_group_max > lh_departament.pending_chats_counter) AND (lh_departament.pending_max = 0 || lh_departament.pending_max > lh_departament.pending_chats_counter) AND date_from <= :date_from AND date_to >= :date_to AND dep_id = :dep_id");
                    $stmt->bindValue(':dep_id',$dep_id);
                } elseif (is_array($dep_id)) {
                    $stmt = $db->prepare("SELECT lh_departament_custom_work_hours.start_hour, lh_departament_custom_work_hours.end_hour FROM lh_departament_custom_work_hours INNER JOIN lh_departament ON lh_departament.id = lh_departament_custom_work_hours.dep_id WHERE (lh_departament.pending_group_max = 0 || lh_departament.pending_group_max > lh_departament.pending_chats_counter) AND (lh_departament.pending_max = 0 || lh_departament.pending_max > lh_departament.pending_chats_counter) AND date_from <= :date_from AND date_to >= :date_to AND dep_id IN (". implode(',', $dep_id) .")");
                }
                
                $stmt->bindValue(':date_from',strtotime(date('Y-m-d')),PDO::PARAM_INT);
                $stmt->bindValue(':date_to',strtotime(date('Y-m-d')),PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if(!empty($result)) {
                    foreach ($result as $item) {
                        if($item['start_hour'] <= (int)(date('G') . date('i')) && $item['end_hour'] > (int)(date('G') . date('i')))
                            $rowsNumber++;
                    }
                } else {
                    $daysColumns = array('mod','tud','wed','thd','frd','sad','sud');
                    $column = date('N') - 1;
                    $startHoursColumnName = $daysColumns[$column].'_start_hour';
                    $endHoursColumnName = $daysColumns[$column].'_end_hour';

                    if (is_numeric($dep_id)) {
                        $stmt = $db->prepare("SELECT COUNT(id) AS found FROM lh_departament WHERE (lh_departament.pending_group_max = 0 || lh_departament.pending_group_max > lh_departament.pending_chats_counter) AND (lh_departament.pending_max = 0 || lh_departament.pending_max > lh_departament.pending_chats_counter) AND online_hours_active = 1 AND {$startHoursColumnName} <= :start_hour AND {$endHoursColumnName} > :end_hour AND {$startHoursColumnName} != -1 AND {$endHoursColumnName} != -1 AND id = :dep_id");
                        $stmt->bindValue(':dep_id', $dep_id);
                    } elseif (is_array($dep_id)) {
                        $stmt = $db->prepare("SELECT COUNT(id) AS found FROM lh_departament WHERE (lh_departament.pending_group_max = 0 || lh_departament.pending_group_max > lh_departament.pending_chats_counter) AND (lh_departament.pending_max = 0 || lh_departament.pending_max > lh_departament.pending_chats_counter) AND online_hours_active = 1 AND {$startHoursColumnName} <= :start_hour AND {$endHoursColumnName} > :end_hour AND {$startHoursColumnName} != -1 AND {$endHoursColumnName} != -1 AND id IN (" . implode(',', $dep_id) . ")");
                    }
                    
                    $stmt->bindValue(':start_hour', date('G') . date('i'), PDO::PARAM_INT);
                    $stmt->bindValue(':end_hour', date('G') . date('i'), PDO::PARAM_INT);
                    $stmt->execute();
                    $rowsNumber = $stmt->fetchColumn();                     
                }
			}					
			
       } else {
       	
	       	if ($ignoreUserStatus === false) {
	           $stmt = $db->prepare('SELECT COUNT(lh_userdep.id) AS found FROM lh_userdep LEFT JOIN lh_departament ON lh_departament.id = lh_userdep.dep_id WHERE (lh_departament.pending_group_max IS NULL || lh_departament.pending_group_max = 0 || lh_departament.pending_group_max > lh_departament.pending_chats_counter) AND (lh_departament.pending_max IS NULL || lh_departament.pending_max = 0 || lh_departament.pending_max > lh_departament.pending_chats_counter) AND (lh_departament.hidden IS NULL || lh_departament.hidden = 0) AND last_activity > :last_activity AND hide_online = 0 AND (lh_departament.disabled IS NULL || lh_departament.disabled = 0)');
	           $stmt->bindValue(':last_activity',(time()-$isOnlineUser),PDO::PARAM_INT);
	           $stmt->execute();
	           $rowsNumber = $stmt->fetchColumn();
	       }
         
           if ($rowsNumber == 0){ // Perhaps auto active is turned on for some of departments
               $stmt = $db->prepare("SELECT lh_departament_custom_work_hours.start_hour, lh_departament_custom_work_hours.end_hour FROM lh_departament_custom_work_hours INNER JOIN lh_departament ON lh_departament.id = lh_departament_custom_work_hours.dep_id WHERE (lh_departament.pending_group_max = 0 || lh_departament.pending_group_max > lh_departament.pending_chats_counter) AND (lh_departament.pending_max = 0 || lh_departament.pending_max > lh_departament.pending_chats_counter) AND lh_departament.hidden = 0 AND lh_departament.disabled = 0 AND date_from <= :date_from AND date_to >= :date_to");
               $stmt->bindValue(':date_from',strtotime(date('Y-m-d')),PDO::PARAM_INT);
               $stmt->bindValue(':date_to',strtotime(date('Y-m-d')),PDO::PARAM_INT);
               $stmt->execute();
               $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                 
               if(!empty($result)) {
                   foreach ($result as $item) {
                       if($item['start_hour'] <= (int)(date('G') . date('i')) && $item['end_hour'] > (int)(date('G') . date('i')))
                           $rowsNumber++;
                   }
               } else {                   
                   $daysColumns = array('mod','tud','wed','thd','frd','sad','sud');
                   $column = date('N') - 1;
                   $startHoursColumnName = $daysColumns[$column].'_start_hour';
                   $endHoursColumnName = $daysColumns[$column].'_end_hour';

                   $stmt = $db->prepare("SELECT COUNT(id) AS found FROM lh_departament WHERE (lh_departament.pending_group_max = 0 || lh_departament.pending_group_max > lh_departament.pending_chats_counter) AND (lh_departament.pending_max = 0 || lh_departament.pending_max > lh_departament.pending_chats_counter) AND online_hours_active = 1 AND {$startHoursColumnName} <= :start_hour AND {$endHoursColumnName} > :end_hour AND {$startHoursColumnName} != -1 AND {$endHoursColumnName} != -1");
                   $stmt->bindValue(':start_hour', date('G') . date('i'), PDO::PARAM_INT);
                   $stmt->bindValue(':end_hour', date('G') . date('i'), PDO::PARAM_INT);
                   $stmt->execute();
                  
                   $rowsNumber = $stmt->fetchColumn();
               }
           }
       }
       
       return $rowsNumber >= 1;
    }

    /**
     * Returns departments with atleast one logged 
     */
    public static function getLoggedDepartmentsIds($departmentsIds, $exclipic = false)
    {
        if (empty($departmentsIds))
        {
            return array();
        }

        $isOnlineUser = (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'];

        $db = ezcDbInstance::get();

        if ($exclipic == true)
        {
            $stmt = $db->prepare("SELECT dep_id AS found FROM lh_userdep WHERE (last_activity > :last_activity AND hide_online = 0) AND dep_id IN (" . implode(',', $departmentsIds) . ")");
            $stmt->bindValue(':last_activity',(time()-$isOnlineUser),PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_COLUMN);

        } else {
            
            $stmt = $db->prepare("SELECT count(id) AS found FROM lh_userdep WHERE (last_activity > :last_activity AND hide_online = 0) AND (dep_id = 0 OR dep_id IN (" . implode(',', $departmentsIds) . "))");
            $stmt->bindValue(':last_activity',(time()-$isOnlineUser),PDO::PARAM_INT);
            $stmt->execute();
            
            $rowsNumber = $stmt->fetchColumn();
            
            // Return same departments because one of operators are online and has assigned all departments
            if ($rowsNumber > 0) {
                return $departmentsIds;
            } else {
                return array();
            }
        }
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
       $onlyOnline = isset($params['hide_online']) ? ' AND lh_userdep.hide_online = :hide_online' : false;
       
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

       $SQL = 'SELECT lh_users.* FROM lh_users INNER JOIN lh_userdep ON lh_userdep.user_id = lh_users.id WHERE lh_userdep.last_activity > :last_activity '.$NotUser.$limitationSQL.$onlyOnline.' GROUP BY lh_users.id';
       $stmt = $db->prepare($SQL);
       $stmt->bindValue(':last_activity',(time()-$isOnlineUser),PDO::PARAM_INT);

       if ($onlyOnline !== false) {
           $stmt->bindValue(':hide_online',(int)$onlyOnline,PDO::PARAM_INT);
       }

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
	   	$extensions = erConfigClassLhConfig::getInstance()->getOverrideValue( 'site', 'extensions' );

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
	   	
	   	if ( $dep !== false && ($dep->inform_close == 1 || $dep->inform_close_all == 1)) {
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
			if (($chat->status_sub != erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT || $skipStatusCheck == true) && ($chat->last_user_msg_time > time()-600 || $chat->last_user_msg_time == 0)) {
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
		   		
		   		if ($chat instanceof erLhcoreClassModelChat && ($chat->status_sub != erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT) && ($chat->last_user_msg_time > time()-600 || $chat->last_user_msg_time == 0) && (!isset($params['reopen_closed']) || $params['reopen_closed'] == 1 || ($params['reopen_closed'] == 0 && $chat->status != erLhcoreClassModelChat::STATUS_CLOSED_CHAT))) {
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
   
   /*
    * Example of call
    * This method can prefill first and second level objects without
    * requirement for each object to be fetched separately
    * Increases performance drastically
   erLhcoreClassModuleFunctions::prefillObjects($items, array(
       array(
           'order_id',
           'order',
           'dommyClass::getList'
       ),      
       array(
           'status_id',
           'status',
           'dommyClass::getList'
       ),
       array(
           array(
               'order',
               'registration_id'
           ),
           array(
               'order',
               'registration'
           ),
           'dommyClass::getList',
           'id'
       )
   ));
   */
   public static function prefillObjects(& $objects, $attrs = array(), $params = array())
   {
       $cache = CSCacheAPC::getMem();
   
       foreach ($attrs as $attr) {
           $ids = array();
           foreach ($objects as $object) {
               if (is_array($attr[0])) {
                   if (is_object($object->{$attr[0][0]}) && $object->{$attr[0][0]}->{$attr[0][1]} > 0) {
                       $ids[] = $object->{$attr[0][0]}->{$attr[0][1]};
                   }
               } else {
                   if ($object->{$attr[0]} > 0) {
                       $ids[] = $object->{$attr[0]};
                   }
               }
           }
   
           $ids = array_unique($ids);
   
           if (! empty($ids)) {
   
               // First try to fetch from memory
               if (isset($params['use_cache'])) {
                   list ($class) = explode('::', $attr[2]);
                   $class = strtolower($class);
   
                   $cacheKeyPrefix = $cache->cacheGlobalKey . 'object_' . $class . '_';
                   $cacheKeyPrefixStore = 'object_' . $class . '_';
   
                   $cacheKeys = array();
                   foreach ($ids as $id) {
                       $cacheKeys[] = $cacheKeyPrefix . $id;
                   }
   
                   $cachedObjects = $cache->restoreMulti($cacheKeys);
   
                   if (! empty($cachedObjects)) {
                       foreach ($objects as & $item) {
                           if (is_array($attr[0])) {
                               if (isset($cachedObjects[$cacheKeyPrefix . $item->{$attr[0][0]}->{$attr[0][1]}]) && $cachedObjects[$cacheKeyPrefix . $item->{$attr[0][0]}->{$attr[0][1]}] !== false) {
                                   $item->{$attr[1][0]}->{$attr[1][1]} = $cachedObjects[$cacheKeyPrefix . $item->{$attr[0][0]}->{$attr[0][1]}];
                                   $key = array_search($item->{$attr[0][0]}->{$attr[0][1]}, $ids);
                                   if ($key !== false) {
                                       unset($ids[$key]);
                                   }
                               }
                           } else {
                               if (isset($cachedObjects[$cacheKeyPrefix . $item->{$attr[0]}]) && $cachedObjects[$cacheKeyPrefix . $item->{$attr[0]}] !== false) {
                                   $item->{$attr[1]} = $cachedObjects[$cacheKeyPrefix . $item->{$attr[0]}];
                                   $key = array_search($item->{$attr[0]}, $ids);
                                   if ($key !== false) {
                                       unset($ids[$key]);
                                   }
                               }
                           }
                       }
                   }
               }
   
               // Check again that ID's were not filled
               if (! empty($ids)) {
                   $filter_attr = 'id';
   
                   if (isset($attr[3]) && $attr[3]) {
                       $filter_attr = $attr[3];
                   }
   
                   $objectsPrefill = call_user_func($attr[2], array(
                       'limit' => false,
                       'filterin' => array(
                           $filter_attr => $ids
                       )
                   ));
   
                   if ($filter_attr != 'id') {
                       $objectsPrefillNew = array();
                       foreach ($objectsPrefill as $key => $value) {
                           $objectsPrefillNew[$value->$filter_attr] = $value;
                       }
                       $objectsPrefill = $objectsPrefillNew;
                   }
   
                   foreach ($objects as & $item) {
   
                       if (is_array($attr[0])) {
                           if (is_object($item->{$attr[0][0]}) && isset($objectsPrefill[$item->{$attr[0][0]}->{$attr[0][1]}])) {
                               $item->{$attr[1][0]}->{$attr[1][1]} = $objectsPrefill[$item->{$attr[0][0]}->{$attr[0][1]}];
   
                               if (isset($params['use_cache']) && $params['use_cache'] == true) {
                                   $cache->store($cacheKeyPrefixStore . $item->{$attr[0][0]}->{$attr[0][1]}, $objectsPrefill[$item->{$attr[0][0]}->{$attr[0][1]}]);
                               }
                           }
                       } else {
                           if (isset($objectsPrefill[$item->{$attr[0]}])) {
   
                               $item->{$attr[1]} = $objectsPrefill[$item->{$attr[0]}];
   
                               if (isset($params['fill_cache']) && $params['fill_cache'] == true) {
                                   $GLOBALS[get_class($objectsPrefill[$item->{$attr[0]}]) . '_' . $item->{$attr[0]}] = $item->{$attr[1]};
                               }
   
                               if (isset($params['use_cache']) && $params['use_cache'] == true) {
                                   $cache->store($cacheKeyPrefixStore . $item->{$attr[0]}, $objectsPrefill[$item->{$attr[0]}]);
                               }
                           }
                       }
                   }
               }
           }
       }
   }
   
   /**
    * Sets chats status directly
    * */
   public static function setOnlineStatusDirectly($chatLists)
   {
       $onlineUserId = array();
        
       foreach ($chatLists as $chat) {
           if (isset($chat->online_user_id) && $chat->online_user_id > 0) {
               $onlineUserId[] = (int)$chat->online_user_id;
           }
       }
        
       if (!empty($onlineUserId)) {
           $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.setonlinestatus_directly',array('list' => & $chatLists, 'online_users_id' => $onlineUserId));
            
           // Event listener has done it's job
           if (isset($response['status']) && $response['status'] === erLhcoreClassChatEventDispatcher::STOP_WORKFLOW) {
               return ;
           }
   
           $onlineVisitors = erLhcoreClassModelChatOnlineUser::getList( array (
               'sort' => false,
               'filterin' => array (
                   'id' => $onlineUserId
               )
           ), array (
               'vid',
               'current_page',
               'invitation_seen_count',
               'page_title',
               'chat_id',
               'last_visit',
               'first_visit',
               'user_agent',
               'user_country_name',
               'user_country_code',
               'operator_message',
               'operator_user_id',
               'operator_user_proactive',
               'message_seen',
               'message_seen_ts',
               'pages_count',
               'tt_pages_count',
               'lat',
               'lon',
               'city',
               'identifier',
               'time_on_site',
               'tt_time_on_site',
               'referrer',
               'invitation_id',
               'total_visits',
               'invitation_count',
               'requires_email',
               'requires_username',
               'requires_phone',
               'dep_id',
               'reopen_chat',
               'operation',
               'operation_chat',
               'screenshot_id',
               'online_attr',
               'online_attr_system',
               'visitor_tz',
               'notes'
           ));
   
           foreach ($chatLists as & $chat) {
               if (isset($chat->online_user_id) && $chat->online_user_id > 0 && isset($onlineVisitors[$chat->online_user_id])) {
                   $chat->user_status_front = self::setActivityByChatAndOnlineUser($chat, $onlineVisitors[$chat->online_user_id]);
               } else {
                   $chat->user_status_front = (isset($chat->user_status) && $chat->user_status == erLhcoreClassModelChat::USER_STATUS_CLOSED_CHAT) ? 1 : 0;
               }
           }
       }
   }
   
   /**
    * Sets chat's status by online visitors records in efficient way
    * */
   public static function setOnlineStatus($chatLists, $chatListOriginal) {
       $onlineUserId = array();
       foreach ($chatLists as $chatList) {
           foreach ($chatList as $chat) {               
               if (isset($chat->online_user_id) && $chat->online_user_id > 0) {
                   $onlineUserId[] = (int)$chat->online_user_id;
               }
           }
       }

       if (!empty($onlineUserId)) {
           
           $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.setonlinestatus',array('list' => & $chatLists, 'online_users_id' => $onlineUserId));
           
           // Event listener has done it's job
           if (isset($response['status']) && $response['status'] === erLhcoreClassChatEventDispatcher::STOP_WORKFLOW) {
               return ;
           }
           
           $onlineVisitors = erLhcoreClassModelChatOnlineUser::getList( array (
                'sort' => false,
                'filterin' => array (
                    'id' => $onlineUserId
                )
            ), array (
                'vid',
                'current_page',
                'invitation_seen_count',
                'page_title',
                'chat_id',
                'last_visit',
                'first_visit',
                'user_agent',
                'user_country_name',
                'user_country_code',
                'operator_message',
                'operator_user_id',
                'operator_user_proactive',
                'message_seen',
                'message_seen_ts',
                'pages_count',
                'tt_pages_count',
                'lat',
                'lon',
                'city',
                'identifier',
                'time_on_site',
                'tt_time_on_site',
                'referrer',
                'invitation_id',
                'total_visits',
                'invitation_count',
                'requires_email',
                'requires_username',
                'requires_phone',
                'dep_id',
                'reopen_chat',
                'operation',
                'operation_chat',
                'screenshot_id',
                'online_attr',
                'online_attr_system',
                'visitor_tz',
                'notes'
            ));

           foreach ($chatLists as & $chatList) {
               foreach ($chatList as & $chat) {
                   if (isset($chatListOriginal[$chat->id]) && $chatListOriginal[$chat->id]->lsync > 0) {

                       // Because mobile devices freezes background tabs we need to have bigger timeout
                       $timeout = 60;

                       if ($chatListOriginal[$chat->id]->device_type != 0 && (strpos($chatListOriginal[$chat->id]->uagent,'iPhone') !== false || strpos($chatListOriginal[$chat->id]->uagent,'iPad') !== false)) {
                           $timeout = 240;
                       }

                       $chat->user_status_front =  (time() - $timeout > $chatListOriginal[$chat->id]->lsync || in_array($chatListOriginal[$chat->id]->status_sub,array(erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW,erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT))) ? 1 : 0;

                       unset($chat->lsync);

                   } elseif (isset($chat->online_user_id) && $chat->online_user_id > 0 && isset($onlineVisitors[$chat->online_user_id])) {
                       $chat->user_status_front = self::setActivityByChatAndOnlineUser($chat, $onlineVisitors[$chat->online_user_id]);
                   } else {
                       $chat->user_status_front = (isset($chat->user_status) && $chat->user_status == erLhcoreClassModelChat::USER_STATUS_CLOSED_CHAT) ? 1 : 0;
                   }

                   if (isset($chat->online_user_id)){
                       unset($chat->online_user_id);
                   }

                   if (isset($chat->uagent)){
                       unset($chat->uagent);
                   }

                   if (isset($chat->user_status)){
                       unset($chat->user_status);
                   }

                   if (isset($chat->last_user_msg_time)){
                       unset($chat->last_user_msg_time);
                   }
               }
           }           
       }
   }
   
   /**
    * @desc Returns user status based on the following logic
    * 1. Green - if widget is not closed
    * 2. Green - if widget is closed and user activity tracking enabled and user still on site and he is active
    * 3. Green - if widget is closed and user activity tracking is disabled, but we still receive pings and from last user message has not passed 5 minutes
    *
    * 4. Yellow - if widget is closed and user activity tracking enabled, but user is not active but he is still on site
    * 5. Yellow - from last user message has passed 5 minutes but user still on site
    *
    * 6. Widget is closed and we could not determine online user || None of above conditions are met.
    *
    * If user activity tracking is enabled but status checking not we default to 10 seconds status checks timeout
    *
    * @param array $params
    *
    * 1 GREEN user has activity in last 5 minutes and ping respond
    * 2 ORANGE user has no activity in last 5 minutes and ping respond
    * 3 GREY Offline user fails to respond pings for X number of times in a row
    *
    * @return int
    */
   public static function setActivityByChatAndOnlineUser($chat, erLhcoreClassModelChatOnlineUser $onlineUser)
   {
        $user_status_front = (!isset($chat->user_status) || $chat->user_status == erLhcoreClassModelChat::USER_STATUS_JOINED_CHAT) ? 0 : 1;
        
        if (($user_status_front == erLhcoreClassModelChat::USER_STATUS_CLOSED_CHAT) || (erLhcoreClassChat::$onlineCondition == 1)) {
           $timeout = (int)erLhcoreClassChat::$trackTimeout || 10;
           if (erLhcoreClassChat::$trackActivity == true) {
               if ($onlineUser->last_check_time_ago < ($timeout+10) && isset($onlineUser->user_active) && $onlineUser->user_active == 1) { //User still on site, it does not matter that he have closed widget.
                   $user_status_front = 0;
               } elseif ((!isset($onlineUser->user_active) || $onlineUser->user_active == 0) && $onlineUser->last_check_time_ago < ($timeout+10)) {
                   $user_status_front = 2;
               }
           } else {
               if ($onlineUser->last_check_time_ago < ($timeout+10) && time()-$chat->last_user_msg_time < 300) { //User still on site, it does not matter that he have closed widget.
                   $user_status_front = 0;
               } elseif (isset($chat->last_user_msg_time) && time()-$chat->last_user_msg_time >= 300 && $onlineUser->last_check_time_ago < ($timeout+10)) {
                   $user_status_front = 2;                 
               }
           }
        }
        
        return $user_status_front;
   }
   
   public static function updateActiveChats($user_id)
   {
       $db = ezcDbInstance::get();
       $stmt = $db->prepare('SELECT id FROM lh_userdep WHERE user_id = :user_id');
       $stmt->bindValue(':user_id', $user_id,PDO::PARAM_STR);
       $stmt->execute();

       $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

       if (!empty($ids)) {
           $stmt = $db->prepare('UPDATE lh_userdep SET active_chats = :active_chats, pending_chats = :pending_chats, inactive_chats = :inactive_chats WHERE id IN (' . implode(',', $ids) . ');');
           $stmt->bindValue(':active_chats',erLhcoreClassChat::getCount(array('filter' => array('user_id' => $user_id, 'status' => erLhcoreClassModelChat::STATUS_ACTIVE_CHAT))),PDO::PARAM_INT);
           $stmt->bindValue(':pending_chats',erLhcoreClassChat::getCount(array('filter' => array('user_id' => $user_id, 'status' => erLhcoreClassModelChat::STATUS_PENDING_CHAT))),PDO::PARAM_INT);
           $stmt->bindValue(':inactive_chats',erLhcoreClassChat::getCount(array('filterin' => array('status' => array(erLhcoreClassModelChat::STATUS_PENDING_CHAT, erLhcoreClassModelChat::STATUS_ACTIVE_CHAT),'status_sub' => array(erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT,erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW)), 'filter' => array('user_id' => $user_id))),PDO::PARAM_INT);
           $stmt->execute();
       }
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

   public static function lockDepartment($depId, $db)
   {
       $stmt = $db->prepare('SELECT id FROM lh_userdep WHERE dep_id = :dep_id');
       $stmt->bindValue(':dep_id',$depId);
       $stmt->execute();

       $recordIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

       if (!empty($recordIds)) {
           $stmt = $db->prepare('SELECT 1 FROM lh_userdep WHERE id IN (' . implode(',', $recordIds) . ') ORDER BY id ASC FOR UPDATE;');
           $stmt->execute();
       }
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
   
   /**
    * @see https://github.com/LiveHelperChat/livehelperchat/pull/809
    *
    * @param array $value
    * */
   public static function safe_json_encode($value) {
        
       $encoded = json_encode($value);
        
       switch (json_last_error()) {
           case JSON_ERROR_NONE:
               return $encoded;
           case JSON_ERROR_DEPTH:
               return 'Maximum stack depth exceeded'; // or trigger_error() or throw new Exception()
           case JSON_ERROR_STATE_MISMATCH:
               return 'Underflow or the modes mismatch'; // or trigger_error() or throw new Exception()
           case JSON_ERROR_CTRL_CHAR:
               return 'Unexpected control character found';
           case JSON_ERROR_SYNTAX:
               return 'Syntax error, malformed JSON'; // or trigger_error() or throw new Exception()
           case JSON_ERROR_UTF8:
               $clean = self::utf8ize($value);
               return self::safe_json_encode($clean);
           default:
               return 'Unknown error'; // or trigger_error() or throw new Exception()
                
       }
   }
    
   public static function getAgoFormat($ts) {
       
       $lastactivity_ago = '';
       
       if ( $ts > 0 ) {
       
           $periods         = array("s.", "m.", "h.", "d.", "w.", "m.", "y.", "dec.");
           $lengths         = array("60","60","24","7","4.35","12","10");
       
           $difference     = time() - $ts;
       
           for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
               $difference /= $lengths[$j];
           }
       
           $difference = round($difference);
       
           $lastactivity_ago = "$difference $periods[$j]";
       };
       
       return $lastactivity_ago;       
   }
   
   /**
    * Make conversion if required
    *
    * @param unknown $mixed
    *
    * @return string
    */
   public static function utf8ize($mixed) {
       if (is_array($mixed)) {
           foreach ($mixed as $key => $value) {
               $mixed[$key] = self::utf8ize($value);
           }
       } else if (is_string ($mixed)) {
           return utf8_encode($mixed);
       }
       return $mixed;
   }
   
   // Static attribute for class
   public static $trackActivity = false;
   public static $trackTimeout = 0;
   public static $onlineCondition = 0;
   
   private static $persistentSession;
}

?>