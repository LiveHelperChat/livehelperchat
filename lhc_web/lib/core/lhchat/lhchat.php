<?php

/**
 * Status -
 * 0 - Pending
 * 1 - Active
 * 2 - Closed
 * 3 - Blocked
 * */

class erLhcoreClassChat {

    /**
     * Gets pending chats
     */
    public static function getPendingChats($limit = 50, $offset = 0)
    {
    	$limitation = self::getDepartmentLimitation();

    	// Does not have any assigned department
    	if ($limitation === false) { return array(); }

    	$filter = array();
    	$filter['filter'] = array('status' => 0);

    	if ($limitation !== true) {
    		$filter['customfilter'][] = $limitation;
    		$filter['use_index'] = 'status_dep_id_id';
    	}

    	$filter['limit'] = $limit;
    	$filter['offset'] = $offset;
    	$filter['smart_select'] = true;

    	return self::getList($filter);
    }


    public static function getPendingChatsCount()
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

    	return self::getCount($filter);
    }

    public static function getList($paramsSearch = array(), $class = 'erLhcoreClassModelChat', $tableName = 'lh_chat')
    {
	       $paramsDefault = array('limit' => 32, 'offset' => 0);

	       $params = array_merge($paramsDefault,$paramsSearch);

	       $session = erLhcoreClassChat::getSession();
	       $q = $session->createFindQuery( $class );

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

    	if (isset($params['customfilter']) && count($params['customfilter']) > 0)
    	{
    		foreach ($params['customfilter'] as $fieldValue)
    		{
    			$conditions[] = $fieldValue;
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
    public static function getUnreadMessagesChats($limit = 10, $offset = 0) {

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

    	$filter['limit'] = $limit;
    	$filter['offset'] = $offset;
    	$filter['smart_select'] = true;

    	$rows = self::getList($filter);

    	return $rows;
    }

    // Get's unread messages from users | COUNT
    public static function getUnreadMessagesChatsCount() {

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

    	$rows = self::getCount($filter);

    	return $rows;
    }

    public static function getActiveChats($limit = 50, $offset = 0)
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

    	return self::getList($filter);
    }

    public static function getActiveChatsCount()
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

    	return self::getCount($filter);
    }

    public static function getClosedChats($limit = 50, $offset = 0)
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

    	return self::getList($filter);
    }

    public static function getClosedChatsCount()
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

    	return self::getCount($filter);
    }

    public static function getOperatorsChats($limit = 50, $offset = 0)
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

    	return self::getList($filter);
    }

    public static function getOperatorsChatsCount()
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

    	return self::getCount($filter);
    }

    public static function isOnline($dep_id = false)
    {
       $isOnlineUser = (int)erConfigClassLhConfig::getInstance()->getSetting('chat','online_timeout');

       $db = ezcDbInstance::get();

       if ($dep_id !== false){
           $stmt = $db->prepare('SELECT COUNT(id) AS found FROM lh_userdep WHERE (last_activity > :last_activity AND hide_online = 0) AND (dep_id = :dep_id OR dep_id = 0)');
           $stmt->bindValue(':dep_id',$dep_id);
           $stmt->bindValue(':last_activity',(time()-$isOnlineUser));
       } else {
           $stmt = $db->prepare('SELECT COUNT(id) AS found FROM lh_userdep WHERE last_activity > :last_activity AND hide_online = 0');
           $stmt->bindValue(':last_activity',(time()-$isOnlineUser));
       }

       $stmt->execute();
       $rows = $stmt->fetchAll();

       return $rows[0]['found'] >= 1;
    }

    public static function getOnlineUsers($UserID = array())
    {
       $isOnlineUser = (int)erConfigClassLhConfig::getInstance()->getSetting('chat','online_timeout');

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

       $stmt->bindValue(':last_activity',(time()-$isOnlineUser));

       $stmt->execute();
       $rows = $stmt->fetchAll();
       return $rows;
    }



   /**
    * All messages, which should get administrator/user
    *
    * */
   public static function getPendingMessages($chat_id,$message_id)
   {
       $db = ezcDbInstance::get();
       $stmt = $db->prepare('SELECT lh_msg.* FROM lh_msg INNER JOIN ( SELECT id FROM lh_msg WHERE chat_id = :chat_id AND id > :message_id ORDER BY id ASC) AS items ON lh_msg.id = items.id');
       $stmt->bindValue( ':chat_id',$chat_id);
       $stmt->bindValue( ':message_id',$message_id);
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
       $stmt->bindValue( ':chat_id',$chat_id);
       $stmt->bindValue( ':message_id',$message_id);
       $stmt->setFetchMode(PDO::FETCH_ASSOC);
       $stmt->execute();
       $rows = $stmt->fetchAll();

       return $rows;
   }


   /**
    * Gets chats messages, used to review chat etc.
    * */
   public static function getChatMessages($chat_id)
   {
   	   $db = ezcDbInstance::get();
       $stmt = $db->prepare('SELECT lh_msg.* FROM lh_msg INNER JOIN ( SELECT id FROM lh_msg WHERE chat_id = :chat_id ORDER BY id ASC) AS items ON lh_msg.id = items.id');
       $stmt->bindValue( ':chat_id',$chat_id);
       $stmt->setFetchMode(PDO::FETCH_ASSOC);
       $stmt->execute();
       $rows = $stmt->fetchAll();
       return $rows;
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

            	if ($currentUser->hasAccessTo('lhchat','allowopenremotechat') == true){
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

   /**
    * Is chat activated and user can send messages.
    *
    * */
   public static function isChatActive($chat_id,$hash)
   {
       $db = ezcDbInstance::get();
       $stmt = $db->prepare('SELECT COUNT(id) AS found FROM lh_chat WHERE id = :chat_id AND hash = :hash AND status = 1');
       $stmt->bindValue( ':chat_id',$chat_id);
       $stmt->bindValue( ':hash',$hash);

       $stmt->execute();
       $rows = $stmt->fetchAll();
       return $rows[0]['found'] == 1;
   }

   public static function generateHash()
   {
       return sha1(mt_rand().time());
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

   private static $persistentSession;
}

?>