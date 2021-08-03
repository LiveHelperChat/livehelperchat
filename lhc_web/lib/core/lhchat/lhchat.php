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
			'cls_us',
			//'user_status',
			'email',
			'support_informed',
			'phone',
			'user_typing',
			'operator_typing',
			//'has_unread_messages',
			'operation',			
			'operation_admin',			
			'screenshot_id',			
			'last_msg_id',
			'mail_send',
			'lat',
			'lon',
			'city',
			//'additional_data',
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
			//'chat_variables',
			// Angular remake
			'referrer',
			//'last_op_msg_time',
			'has_unread_op_messages',
			'unread_op_messages_informed',
			'tslasign',
			'user_closed_ts',
			'usaccept',
			'auto_responder_id',
			'chat_locale',
			'anonymized',
			'uagent',
			'user_tz_identifier',
			'invitation_id',
	);

	public static $limitMessages = 50;

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
        $filter['use_index'] = 'status';

    	if ($limitation !== true) {
    		$filter['customfilter'][] = $limitation;
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
        $filter['use_index'] = 'status';

    	if ($limitation !== true) {
    		$filter['customfilter'][] = $limitation;
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
        $filter['use_index'] = 'status';

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
        if ($table == 'lh_chat' && $operation == 'COUNT(id)') {
            $operation = 'count(`lh_chat`.`id`)';
        }

    	$session = erLhcoreClassChat::getSession();
    	$q = $session->database->createSelectQuery();
    	$q->select( $operation )->from( $table );
    	$conditions = array();

    	if (isset($params['filter']) && count($params['filter']) > 0)
    	{
    		foreach ($params['filter'] as $field => $fieldValue)
    		{
                if (is_array($fieldValue)) {
                    if (!empty($fieldValue)) {
                        $conditions[] = $q->expr->in($field, $fieldValue);
                    }
                } else {
    			    $conditions[] = $q->expr->eq( $field, $q->bindValue($fieldValue) );
                }
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

        if (isset($params['filternot']) && count($params['filternot']) > 0)
        {
            foreach ($params['filternot'] as $field => $fieldValue) {
                if (is_array($fieldValue)) {
                    if (!empty($fieldValue)) {
                        $conditions[] = $q->expr->not($q->expr->in($field, $fieldValue));
                    }
                } else {
                    $conditions[] = $q->expr->neq($field, $q->bindValue($fieldValue));
                }
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
    		$userDepartaments = erLhcoreClassUserDep::getUserDepartaments($userId, $userData->cache_version);

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
    	}
    	
    	// Give 5 seconds to operator to sync a chat and avoid annoying him
    	$filter['filterlt']['last_user_msg_time'] = time()-5;
    	
    	$filter['limit'] = $limit;
    	$filter['offset'] = $offset;
    	$filter['smart_select'] = true;

    	if (!empty($filterAdditional)) {
    		$filter = array_merge_recursive($filter,$filterAdditional);
    	}

    	// Optimization - we get these stats only from last 50 chats
        $filter['customfilter'][] = '`lh_chat`.`id` IN (SELECT `id` FROM (SELECT `id` FROM `lh_chat` ORDER BY `id` DESC LIMIT 50) AS `sq`)';

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
        $filter['use_index'] = 'status';

    	if ($limitation !== true) {
    		$filter['customfilter'][] = $limitation;
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
        $filter['use_index'] = 'status';

    	if ($limitation !== true) {
    		$filter['customfilter'][] = $limitation;
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
        $filter['use_index'] = 'status';

    	if ($limitation !== true) {
    		$filter['customfilter'][] = $limitation;
    	}

    	$filter['limit'] = $limit;
    	$filter['offset'] = $offset;
    	$filter['smart_select'] = true;

    	if (!empty($filterAdditional)) {
    		$filter = array_merge_recursive($filter,$filterAdditional);
    	}

    	// Optimization - we get these stats only from last 50 chats
        $filter['customfilter'][] = '`lh_chat`.`id` IN (SELECT `id` FROM (SELECT `id` FROM `lh_chat` ORDER BY `id` DESC LIMIT 50) AS `sq`)';

    	return self::getList($filter);
    }

    public static function getBotChats($limit = 50, $offset = 0, $filterAdditional = array())
    {
    	$limitation = self::getDepartmentLimitation();

    	// Does not have any assigned department
    	if ($limitation === false) { return array(); }

    	$filter = array();
    	$filter['filter'] = array('status' => 5);
        $filter['use_index'] = 'status';

    	if ($limitation !== true) {
    		$filter['customfilter'][] = $limitation;
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
        $filter['use_index'] = 'status';

    	if ($limitation !== true) {
    		$filter['customfilter'][] = $limitation;
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
        $filter['use_index'] = 'status';

    	if ($limitation !== true) {
    		$filter['customfilter'][] = $limitation;
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
        $filter['use_index'] = 'status';

    	if ($limitation !== true) {
    		$filter['customfilter'][] = $limitation;
    	}

    	if (!empty($filterAdditional)) {
    		$filter = array_merge_recursive($filter, $filterAdditional);
    	}

    	return self::getCount($filter);
    }

    public static $botOnlyOnline = null;

    public static function isOnline($dep_id = false, $exclipic = false, $params = array())
    {
       $isOnlineUser = isset($params['online_timeout']) ? $params['online_timeout'] : (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'];
       $ignoreUserStatus = (isset($params['ignore_user_status']) && $params['ignore_user_status'] == 1) ? true : false;
       
       $db = ezcDbInstance::get();
	   $rowsNumber = 0;
       $userFilter = (isset($params['user_id']) && is_numeric($params['user_id'])) ? ' AND `lh_userdep`.`user_id` = '.(int)$params['user_id'] : '';

       if ($dep_id !== false && $dep_id !== '') {
       		$exclipicFilter = ($exclipic == false) ? ' OR dep_id = 0' : '';

       		if ($ignoreUserStatus === false) {

				if (is_numeric($dep_id)) {
		           $stmt = $db->prepare("SELECT COUNT(lh_userdep.id) AS found FROM lh_userdep INNER JOIN lh_departament ON lh_departament.id = :dep_id_dest WHERE (lh_departament.pending_group_max = 0 || lh_departament.pending_group_max > lh_departament.pending_chats_counter) AND (lh_departament.pending_max = 0 || lh_departament.pending_max > lh_departament.pending_chats_counter) AND ((last_activity > :last_activity OR `lh_userdep`.`always_on` = 1) AND hide_online = 0 AND ro = 0) AND (dep_id = :dep_id {$exclipicFilter}) {$userFilter}");
		           $stmt->bindValue(':dep_id',$dep_id,PDO::PARAM_INT);
		           $stmt->bindValue(':dep_id_dest',$dep_id,PDO::PARAM_INT);
		           $stmt->bindValue(':last_activity',(time()-$isOnlineUser),PDO::PARAM_INT);
				} elseif ( is_array($dep_id) ) {
					if (empty($dep_id)) {
						$dep_id = array(-1);
					}
					$stmt = $db->prepare('SELECT COUNT(lh_userdep.id) AS found FROM lh_userdep, lh_departament WHERE lh_departament.id IN ('. implode(',', $dep_id) .') AND (lh_departament.pending_group_max = 0 || lh_departament.pending_group_max > lh_departament.pending_chats_counter) AND (lh_departament.pending_max = 0 || lh_departament.pending_max > lh_departament.pending_chats_counter) AND ((last_activity > :last_activity OR `lh_userdep`.`always_on` = 1) AND hide_online = 0 AND ro = 0) AND (dep_id IN ('. implode(',', $dep_id) .") {$exclipicFilter}) {$userFilter}");
					$stmt->bindValue(':last_activity',(time()-$isOnlineUser),PDO::PARAM_INT);
				}
				$stmt->execute();
				$rowsNumber = $stmt->fetchColumn();	
       		}

			if ($rowsNumber == 0 && (!isset($params['exclude_online_hours']) || $params['exclude_online_hours'] == false)) { // Perhaps auto active is turned on for some of departments
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

			// Check is bot enabled for department
			if ($rowsNumber == 0 && (is_numeric($dep_id) || count($dep_id) == 1) && (!isset($params['exclude_bot']) || $params['exclude_bot'] == false)) {
                if (is_numeric($dep_id)) {
                    $stmt = $db->prepare("SELECT bot_configuration FROM lh_departament WHERE id = :dep_id");
                    $stmt->bindValue(':dep_id', $dep_id);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                } else {
                    $stmt = $db->prepare("SELECT bot_configuration FROM lh_departament WHERE id IN (" . implode(',', $dep_id) . ")");
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                }

                if (!empty($result['bot_configuration'])) {
                    $botData = json_decode($result['bot_configuration'], true);
                    if (isset($botData['bot_id']) && $botData['bot_id'] > 0 && (!isset($botData['bot_foh']) || $botData['bot_foh'] == false)) {
                        $rowsNumber = 1;
                        self::$botOnlyOnline = true;
                    }
                }
            }

       } else {
       	
	       	if ($ignoreUserStatus === false) {
	           $stmt = $db->prepare('SELECT COUNT(lh_userdep.id) AS found FROM lh_userdep LEFT JOIN lh_departament ON lh_departament.id = lh_userdep.dep_id WHERE (lh_departament.pending_group_max IS NULL || lh_departament.pending_group_max = 0 || lh_departament.pending_group_max > lh_departament.pending_chats_counter) AND (lh_departament.pending_max IS NULL || lh_departament.pending_max = 0 || lh_departament.pending_max > lh_departament.pending_chats_counter) AND (lh_departament.hidden IS NULL || lh_departament.hidden = 0) AND (last_activity > :last_activity OR `lh_userdep`.`always_on` = 1) AND ro = 0 AND hide_online = 0 AND (lh_departament.disabled IS NULL || lh_departament.disabled = 0) '.$userFilter);
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

    public static function isOnlyBotOnline($department) {

        if ( self::$botOnlyOnline == null) {
            self::isOnline($department, false, array('ignore_user_status'=> (int)erLhcoreClassModelChatConfig::fetch('ignore_user_status')->current_value, 'online_timeout' => (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout']));
        }

        $onlyBotOnline = false;

        if ((is_numeric($department) && $department > 0) || (is_array($department) && count($department) == 1)) {
            $onlyBotOnline = self::$botOnlyOnline;

            // Check does chat is started with bot
            if ($onlyBotOnline == false) {
                $departmentObject = erLhcoreClassModelDepartament::fetch($department);
                if ($departmentObject instanceof erLhcoreClassModelDepartament) {
                    if ((!isset($departmentObject->bot_configuration_array['bot_only_offline']) || $departmentObject->bot_configuration_array['bot_only_offline'] == 0) && isset($departmentObject->bot_configuration_array['bot_id']) && $departmentObject->bot_configuration_array['bot_id'] > 0) {
                        $onlyBotOnline = true;
                    }
                }
            }
        }

        return $onlyBotOnline;
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
            $stmt = $db->prepare("SELECT dep_id AS found FROM lh_userdep WHERE ((last_activity > :last_activity OR `lh_userdep`.`always_on` = 1) AND hide_online = 0) AND dep_id IN (" . implode(',', $departmentsIds) . ")");
            $stmt->bindValue(':last_activity',(time()-$isOnlineUser),PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_COLUMN);

        } else {
            
            $stmt = $db->prepare("SELECT count(id) AS found FROM lh_userdep WHERE ((last_activity > :last_activity OR `lh_userdep`.`always_on` = 1) AND hide_online = 0) AND (dep_id = 0 OR dep_id IN (" . implode(',', $departmentsIds) . "))");
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
		
    	$SQL = 'SELECT count(*) FROM (SELECT count(`lh_users`.`id`) FROM `lh_users` INNER JOIN `lh_userdep` ON `lh_userdep`.`user_id` = `lh_users`.`id` WHERE (`lh_userdep`.`last_activity` > :last_activity OR `lh_userdep`.`always_on` = 1) AND `lh_userdep`.`hide_online` = 0 ' . $filterOperators . ' GROUP BY `lh_users`.`id`) as `online_users`';
    	$stmt = $db->prepare($SQL);
    	$stmt->bindValue(':last_activity',$agoTime,PDO::PARAM_INT);
    	$stmt->execute();
    	$count = $stmt->fetchColumn();

    	if ($count > 0){
	    	$offsetRandom = rand(0, $count-1);

	    	$SQL = "SELECT `lh_users`.`id` FROM `lh_users` INNER JOIN `lh_userdep` ON `lh_userdep`.`user_id` = `lh_users`.`id` WHERE (`lh_userdep`.`last_activity` > :last_activity OR `lh_userdep`.`always_on` = 1) AND `lh_userdep`.`hide_online` = 0 {$filterOperators} GROUP BY `lh_users`.`id` LIMIT 1 OFFSET {$offsetRandom}";
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
       $sameDepartment = isset($params['same_dep']) ? ' AND (lh_userdep.dep_id = 0 OR lh_userdep.dep_id = :dep_id)' : false;

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

       $SQL = 'SELECT lh_users.* FROM lh_users INNER JOIN lh_userdep ON lh_userdep.user_id = lh_users.id WHERE (`lh_userdep`.`last_activity` > :last_activity OR `lh_userdep`.`always_on` = 1) '.$NotUser.$limitationSQL.$onlyOnline.$sameDepartment.' GROUP BY lh_users.id';
       $stmt = $db->prepare($SQL);
       $stmt->bindValue(':last_activity',(time()-$isOnlineUser),PDO::PARAM_INT);

       if ($onlyOnline !== false) {
           $stmt->bindValue(':hide_online',0,PDO::PARAM_INT);
       }

       if ($sameDepartment !== false) {
           $stmt->bindValue(':dep_id',$params['same_dep'],PDO::PARAM_INT);
       }

       $stmt->execute();
       $rows = $stmt->fetchAll();
       return $rows;
    }

    public static function isOnlineUser($user_id, $params = array()) {    	
    	$isOnlineUser = isset($params['online_timeout']) ? $params['online_timeout'] : (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'];
    	    	
    	$db = ezcDbInstance::get();

    	$stmt = $db->prepare('SELECT count(lh_users.id) FROM lh_users INNER JOIN lh_userdep ON lh_userdep.user_id = lh_users.id WHERE (`lh_userdep`.`last_activity` > :last_activity OR `lh_userdep`.`always_on` = 1) AND `lh_users`.`hide_online` = 0 AND `lh_users`.`id` = :user_id');
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
   public static function getPendingMessages($chat_id,$message_id, $excludeSystem = false)
   {

       $excludeFilter = '';

       if ($excludeSystem == true) {
           $excludeFilter = ' AND user_id != -1'; // It's a system message
       }

       $db = ezcDbInstance::get();
       $stmt = $db->prepare('SELECT lh_msg.* FROM lh_msg INNER JOIN (SELECT id FROM lh_msg WHERE chat_id = :chat_id AND id > :message_id ' . $excludeFilter . ' ORDER BY id ASC) AS items ON lh_msg.id = items.id');
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
   public static function getGetLastChatMessagePending($chat_id, $visitorMessages = false, $limit = 3, $implode = "\n")
   {
       $filter = '';
       if ($visitorMessages == true) {
           $filter = ' AND user_id = 0';
       }

       $db = ezcDbInstance::get();
       $stmt = $db->prepare("SELECT lh_msg.msg FROM lh_msg INNER JOIN ( SELECT id FROM lh_msg WHERE chat_id = :chat_id {$filter} ORDER BY id DESC LIMIT {$limit} OFFSET 0) AS items ON lh_msg.id = items.id");
       $stmt->bindValue( ':chat_id',$chat_id,PDO::PARAM_INT);
       $stmt->setFetchMode(PDO::FETCH_ASSOC);
       $stmt->execute();
       $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);

       $plain = erLhcoreClassBBCodePlain::make_clickable(implode($implode, array_reverse($rows)), array('sender' => 0));
       $text = mb_substr($plain,-200);
       
       return $text;
   }

   /**
    * Gets chats messages, used to review chat etc.
    * */
   public static function getChatMessages($chat_id, $limit = 1000, $lastMessageId = 0)
   {
       if ($lastMessageId == 0) {
           $db = ezcDbInstance::get();
           $stmt = $db->prepare('SELECT lh_msg.* FROM lh_msg INNER JOIN ( SELECT id FROM lh_msg WHERE chat_id = :chat_id ORDER BY id DESC LIMIT :limit) AS items ON lh_msg.id = items.id ORDER BY lh_msg.id ASC');
           $stmt->bindValue( ':chat_id',$chat_id,PDO::PARAM_INT);
           $stmt->bindValue( ':limit',$limit,PDO::PARAM_INT);
           $stmt->setFetchMode(PDO::FETCH_ASSOC);
           $stmt->execute();
           $rows = $stmt->fetchAll();
       } else {
           $db = ezcDbInstance::get();
           $stmt = $db->prepare('SELECT lh_msg.* FROM lh_msg INNER JOIN ( SELECT id FROM lh_msg WHERE chat_id = :chat_id AND lh_msg.id < :message_id ORDER BY id DESC LIMIT :limit) AS items ON lh_msg.id = items.id ORDER BY lh_msg.id ASC');
           $stmt->bindValue( ':chat_id',$chat_id,PDO::PARAM_INT);
           $stmt->bindValue( ':limit',$limit,PDO::PARAM_INT);
           $stmt->bindValue( ':message_id',$lastMessageId,PDO::PARAM_INT);
           $stmt->setFetchMode(PDO::FETCH_ASSOC);
           $stmt->execute();
           $rows = $stmt->fetchAll();
       }

       return $rows;
   }

   /**
    * Get first user mesasge for prefilling chat
    * */
   public static function getFirstUserMessage($chat_id)
   {
	   	$db = ezcDbInstance::get();
	   	$stmt = $db->prepare('SELECT lh_msg.msg,lh_msg.user_id FROM lh_msg INNER JOIN ( SELECT id FROM lh_msg WHERE chat_id = :chat_id AND (user_id = 0 OR user_id = -2) ORDER BY id ASC LIMIT 10) AS items ON lh_msg.id = items.id');
	   	$stmt->bindValue( ':chat_id',$chat_id,PDO::PARAM_INT);
	   	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	   	$stmt->execute();

	   	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

	   	$responseRows = [];
	   	foreach ($rows as $row) {
            $responseRows[] = ($row['user_id'] == 0 ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','You') : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Us')) . ': ' . $row['msg'];
        }

	   	if (empty($responseRows)) {
	   	    return '';
        }

	   	return erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Summary') . ":\n".implode("\n",$responseRows);
   }

   public static function hasAccessToWrite($chat)
   {
        $dep = erLhcoreClassUserDep::getUserReadDepartments();
        return !in_array($chat->dep_id, $dep);
   }

   public static function hasAccessToRead($chat)
   {
       $currentUser = erLhcoreClassUser::instance();

       $userData = $currentUser->getUserData(true);

       if ( $userData->all_departments == 0 && $chat->dep_id != 0) {

            /*
             * --From now permission is strictly by assigned department, not by chat owner
             *
             * Finally decided to keep this check, it allows more advance permissions configuration
             * */

       		if ($chat->user_id == $currentUser->getUserID()) return true;

            $userDepartaments = erLhcoreClassUserDep::getUserDepartaments($currentUser->getUserID(), $userData->cache_version);

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

       } elseif ($userData->all_departments != 0 && $chat->user_id != 0 && $chat->user_id != $currentUser->getUserID() && !$currentUser->hasAccessTo('lhchat','allowopenremotechat')) {
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
        $hasYears = false;
        $hasDays = false;
        $hasHours = false;

	    if ($y > 0)
	    {
	    	$parts[] = $y . ' .y';
            $hasYears = true;
	    }

	    if ($d > 0)
	    {
	    	$parts[] = $d . ' d.';
            $hasDays = true;
	    }

	    if ($h > 0 && $hasYears == false)
	    {
	    	$parts[] = $h . ' h.';
            $hasHours = true;
	    }

	    if ($m > 0 && $hasDays == false && $hasYears == false)
	    {
	    	$parts[] = $m . ' m.';
	    }

	    if ($s > 0 && $hasHours == false && $hasDays == false && $hasYears == false)
	    {
	    	$parts[] = $s . ' s.';
	    }

	    return implode(' ',$parts);
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
    * This can be calculated in background as it does not influence anything except statistic
    * */
   public static function updateDepartmentStats($dep) {
       try {

           if (erLhcoreClassSystem::instance()->backgroundMode == false && class_exists('erLhcoreClassExtensionLhcphpresque')) {
               $inst_id = class_exists('erLhcoreClassInstance') ? erLhcoreClassInstance::$instanceChat->id : 0;
               erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->enqueue('lhc_stats_resque', 'erLhcoreClassChatStatsResque', array('inst_id' => $inst_id,'type' => 'dep', 'id' => $dep->id));
               return;
           }

           erLhcoreClassChatStatsResque::updateStats($dep);

       } catch (Exception $e) {
           //Fail silently as it's just statistic update operation
       }
   }

    /**
     * @desc returns departments by department groups
     *
     * @param array $group_ids
     *
     * @return mixed
     */
   public static function getDepartmentsByDepGroup($group_ids) {
       static $group_id_by_group = array();
       $key = implode('_',$group_ids);

       if (!key_exists($key, $group_id_by_group))
       {
           $db = ezcDbInstance::get();
           $stmt = $db->prepare('SELECT dep_id FROM lh_departament_group_member WHERE dep_group_id IN (' . implode(',', $group_ids) . ')');
           $stmt->execute();
           $depIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

           $group_id_by_group[$key] = $depIds;
       }

       return $group_id_by_group[$key];
   }

    /**
     * @desc returns users id by users groups
     *
     * @param array $group_ids
     *
     * @return mixed
     */
   public static function getUserIDByGroup($group_ids) {
        static $user_id_by_group = array();
        $key = implode('_',$group_ids);

        if (!key_exists($key, $user_id_by_group))
        {
            $db = ezcDbInstance::get();
            $stmt = $db->prepare('SELECT user_id FROM lh_groupuser WHERE group_id IN ('. implode(',', $group_ids) . ')');
            $stmt->execute();
            $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $user_id_by_group[$key] = $userIds;
        }

        return $user_id_by_group[$key];
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

   public static function reopenChatWidgetV2($onlineUser, $chat, $params) {
        if ($onlineUser->chat_id > 0) {
            $chatOld = erLhcoreClassModelChat::fetch($onlineUser->chat_id);

            // Old chat was not found
            if (!($chatOld instanceof erLhcoreClassModelChat)) {
                return;
            }

            if ($chatOld->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT ||
                $chatOld->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT ||
                $chatOld->status == erLhcoreClassModelChat::STATUS_BOT_CHAT
                || ($params['reopen_closed'] == true && $chatOld->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT && ($chat->last_user_msg_time == 0 || $chat->last_op_msg_time > time() - (int)$params['open_closed_chat_timeout']))
            ) {
                // Just switch chat ID, that's it.
                // The rest will be done automatically.
                $chat->id = $chatOld->id;
                $chat->remarks = $chatOld->remarks;
                $chat->old_last_msg_id = $chatOld->last_msg_id;
            }
        }
   }

   /**
    * Is there any better way to initialize __get variables?
    * */
   public static function prefillGetAttributes(& $objects, $attrs = array(),$attrRemove = array(), $params = array()) {   		
   		foreach ($objects as & $object) {
   			foreach ($attrs as $attr) {
   				$object->{$attr};
   			};

            if (isset($params['additional_columns']) && is_array($params['additional_columns']) && !empty($params['additional_columns'])) {
                foreach ($params['additional_columns'] as $column) {
                    if (strpos($column->variable,'additional_data.') !== false) {
                        $additionalDataArray = $object->additional_data_array;
                        if (is_array($additionalDataArray)) {
                            foreach ($additionalDataArray as $additionalItem) {

                                $valueCompare = false;

                                if (isset($additionalItem['identifier'])) {
                                    $valueCompare = $additionalItem['identifier'];
                                } elseif (isset($additionalItem['key'])) {
                                    $valueCompare = $additionalItem['key'];
                                }

                                if ($valueCompare !== false && $valueCompare == str_replace('additional_data.','',$column->variable)) {
                                    $object->{'cc_'.$column->id} = $additionalItem['value'];
                                    break;
                                }
                            }
                        }
                    } elseif (strpos($column->variable,'chat_variable.') !== false) {
                        $additionalDataArray = $object->chat_variables_array;
                        if (is_array($additionalDataArray)) {
                            $variableName = str_replace('chat_variable.','', $column->variable);
                            if (isset($object->chat_variables_array[$variableName]) && $object->chat_variables_array[$variableName] != '') {
                                $object->{'cc_'.$column->id} = $object->chat_variables_array[$variableName];
                            }
                        }
                    } elseif (strpos($column->variable,'lhc.') !== false) {
                        $variableName = str_replace('lhc.','', $column->variable);
                        $variableValue = $object->{$variableName};
                        if (isset($variableValue) && $variableValue != '') {
                            $object->{'cc_'.$column->id} = $variableValue;
                        }
                    }


                }
            }


   			foreach ($attrRemove as $attr) {
   				$object->{$attr} = null;
   				if (isset($params['clean_ignore'])) {
   				    unset($object->{$attr});
                }
   			}
   			
   			if (isset($params['remove_all']) && $params['remove_all'] == true) {
   			    foreach ($object as $attr => $value) {
   			        if (!in_array($attr, $attrs)) {
   			            $object->$attr = null;
   			        }
   			    }
   			}

   			if (!isset($params['do_not_clean'])){
   			    if (isset($params['filter_function'])){
                    $object = (object)array_filter((array)$object,function ($value) {
                        return is_array($value) || strlen($value) > 0;
                    });
                } else {
                    $object = (object)array_filter((array)$object);
                }
            }
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

   public static function validateFilterInString(& $params) {
   		foreach ($params as & $param) {
   			$param =  preg_replace('/[^a-zA-Z0-9]/', '', $param );
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

   public static function updateActiveChats($user_id, $ignoreEvent = false)
   {
       if ($user_id == 0) {
           return;
       }

       $db = ezcDbInstance::get();
       $stmt = $db->prepare('SELECT id FROM lh_userdep WHERE user_id = :user_id');
       $stmt->bindValue(':user_id', $user_id,PDO::PARAM_STR);
       $stmt->execute();

       $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

       $activeChats = null;
       $pendingChats = null;
       $inactiveChats = null;

       if (!empty($ids)) {

           // Try 3 times to update table
           for ($i = 0; $i < 3; $i++)
           {
               try {

                   if ($activeChats === null){
                       $activeChats = erLhcoreClassChat::getCount(array('filter' => array('user_id' => $user_id, 'status' => erLhcoreClassModelChat::STATUS_ACTIVE_CHAT)));
                   }

                   if ($pendingChats === null) {
                       $pendingChats = erLhcoreClassChat::getCount(array('filter' => array('user_id' => $user_id, 'status' => erLhcoreClassModelChat::STATUS_PENDING_CHAT)));
                   }

                   if ($inactiveChats === null) {
                       $inactiveChats = erLhcoreClassChat::getCount(array('filterin' => array('status' => array(erLhcoreClassModelChat::STATUS_PENDING_CHAT, erLhcoreClassModelChat::STATUS_ACTIVE_CHAT), 'status_sub' => array(erLhcoreClassModelChat::STATUS_SUB_SURVEY_COMPLETED, erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT, erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW)), 'filter' => array('user_id' => $user_id)));
                   }

                   $stmt = $db->prepare('UPDATE lh_userdep SET active_chats = :active_chats, pending_chats = :pending_chats, inactive_chats = :inactive_chats WHERE id IN (' . implode(',', $ids) . ');');
                   $stmt->bindValue(':active_chats',(int)$activeChats,PDO::PARAM_INT);
                   $stmt->bindValue(':pending_chats',(int)$pendingChats,PDO::PARAM_INT);
                   $stmt->bindValue(':inactive_chats',(int)$inactiveChats,PDO::PARAM_INT);
                   $stmt->execute();

                   // Finish cycle
                   break;

               } catch (Exception $e) {
                   if ($i == 2) { // It was last try
                       if ($ignoreEvent === false) {
                           erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.update_active_chats',array('user_id' => $user_id));
                       }

                       erLhcoreClassLog::write($e->getMessage() . "\n" . $e->getTraceAsString(),
                           ezcLog::SUCCESS_AUDIT,
                           array(
                               'source' => 'lhc',
                               'category' => 'update_active_chats',
                               'line' => __LINE__,
                               'file' => __FILE__,
                               'object_id' => $user_id
                           )
                       );
                       return;
                   } else {
                       // Just sleep for fraction of second and try again
                       usleep(150);
                   }
               }
           }

           if ($ignoreEvent === false) {
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.update_active_chats',array('user_id' => $user_id));
           }
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
           try {
               $stmt = $db->prepare('SELECT 1 FROM lh_userdep WHERE id IN (' . implode(',', $recordIds) . ') ORDER BY id ASC FOR UPDATE;');
               $stmt->execute();
           } catch (Exception $e) {
               try {
                   usleep(100);
                   $stmt = $db->prepare('SELECT 1 FROM lh_userdep WHERE id IN (' . implode(',', $recordIds) . ') ORDER BY id ASC FOR UPDATE;');
                   $stmt->execute();
               } catch (Exception $e) {
                   error_log($e->getMessage() . "\n" . $e->getTraceAsString());
               }
           }
       }
   }

   public static function getChatDurationToUpdateChatID($chat) {

       $sql = 'SELECT lh_msg.time, lh_msg.user_id FROM lh_msg WHERE lh_msg.chat_id = :chat_id AND lh_msg.user_id != -1 ORDER BY id ASC';
       $db = ezcDbInstance::get();
       $stmt = $db->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
       $stmt->bindValue(':chat_id',$chat->id);
       $stmt->execute();

       $timeout_user = erLhcoreClassModelChatConfig::fetch('cduration_timeout_user')->current_value;
       $timeout_operator = erLhcoreClassModelChatConfig::fetch('cduration_timeout_operator')->current_value;

       $params = array(
           'timeout_user' => ($timeout_user > 0 ? $timeout_user : 4)*60,// How long operator can wait for message from visitor before delay between messages are ignored
           'timeout_operator' => ($timeout_operator > 0 ? $timeout_operator : 10)*60
       );

       $previousMessage = null;
       $timeToAdd = 0;
       while ($row = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
           if ($previousMessage === null) {
               //$row['time'] = $row['time']; // Consider that first user message was time plus wait time. Wait time is time then operator accepted a chat.
               $previousMessage = $row;
               continue;
           }

           if ($row['user_id'] == 0) {
               $timeout = $params['timeout_user'];
           } else {
               $timeout = $params['timeout_operator'];
           }

           $diff = $row['time'] - $previousMessage['time'];

           //$ignored = true;
           if ($diff < $timeout && $diff > 0) {
               $timeToAdd += $diff;
               //$ignored = false;
           }
           //echo date('Y-m-d H:i:s',$row['time']),'||',$diff,'||',(int)$ignored,'||',$row['msg'],"\n";
           $previousMessage = $row;
       }

	   	/*$sql = 'SELECT ((SELECT MAX(lh_msg.time) FROM lh_msg WHERE lh_msg.chat_id = lh_chat.id AND lh_msg.user_id = 0)-(lh_chat.time+lh_chat.wait_time)) AS chat_duration_counted FROM lh_chat WHERE lh_chat.id = :chat_id';
	   	$db = ezcDbInstance::get();
	   	$stmt = $db->prepare($sql);
	   	$stmt->bindValue(':chat_id',$chat->id);
	   	$stmt->bindValue(':cls_time',$chat->cls_time);
	   	$stmt->execute();
	   	$time = $stmt->fetchColumn();*/

   	   	return $timeToAdd;
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
       
           $periods         = array("s.", "m.", "h.", "d.", "w.", "M.", "y.", "dec.");
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

   public static function array_flatten($array = null) {
        $result = array();

        if (!is_array($array)) {
            $array = func_get_args();
        }

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = array_merge($result, self::array_flatten($value));
            } else {
                $result = array_merge($result, array($key => $value));
            }
        }

        return $result;
    }

    public static function cleanForDashboard($chatLists) {
       $attrsClean = array('online_user_id','uagent','user_status','last_user_msg_time','last_op_msg_time','lsync','dep_id','gbot_id');
        foreach ($chatLists as & $chatList) {
            foreach ($chatList as & $chat) {
                foreach ($attrsClean as $attrClean) {
                    if (isset($chat->{$attrClean})) {
                        unset($chat->{$attrClean});
                    }
                }
            }
        }
    }

   // Static attribute for class
   public static $trackActivity = false;
   public static $trackTimeout = 0;
   public static $onlineCondition = 0;
   
   private static $persistentSession;
}

?>