<?php

class erLhcoreClassModelUser {

    public function getState()
    {
        return array(
            'id' => $this->id,
            'username' => $this->username,
            'password' => $this->password,
            'email' => $this->email,
            'name' => $this->name,
            'surname' => $this->surname,
            'disabled' => $this->disabled,
            'hide_online' => $this->hide_online,
            'all_departments' => $this->all_departments,
            'filepath' => $this->filepath,
            'filename' => $this->filename,
            'skype' => $this->skype,
            'job_title' => $this->job_title,
            'time_zone' => $this->time_zone,
            'invisible_mode' => $this->invisible_mode,
            'inactive_mode' => $this->inactive_mode,
            'xmpp_username' => $this->xmpp_username,
            'rec_per_req' => $this->rec_per_req,
            'session_id' => $this->session_id,
            'active_chats_counter' => $this->active_chats_counter,
            'closed_chats_counter' => $this->closed_chats_counter,
            'pending_chats_counter' => $this->pending_chats_counter,
            'departments_ids' => $this->departments_ids,
            'chat_nickname' => $this->chat_nickname,
            'max_active_chats' => $this->max_active_chats,
            'auto_accept' => $this->auto_accept,
            'attr_int_1' => $this->attr_int_1,
            'attr_int_2' => $this->attr_int_2,
            'attr_int_3' => $this->attr_int_3,
            'operation_admin' => $this->operation_admin,
            'exclude_autoasign' => $this->exclude_autoasign
        );
   }
      
   public function setState( array $properties )
   {
       foreach ( $properties as $key => $val )
       {
           $this->$key = $val;
       }
   }

   public function setPassword($password)
   {
		
		$hash = password_hash($password, PASSWORD_DEFAULT);
       
		if ($hash) {
			$this->password = $hash;
		} else {
			return false;
		}		
       
   }

   public static function fetch($user_id, $useCache = false)
   {   	
	   	if ($useCache == true) {
	   		
		   	if (isset($GLOBALS['erLhcoreClassModelUser_'.$user_id])) return $GLOBALS['erLhcoreClassModelUser_'.$user_id];
			   	
		   	try {
		   		$GLOBALS['erLhcoreClassModelUser_'.$user_id] = erLhcoreClassUser::getSession('slave')->load( 'erLhcoreClassModelUser', (int)$user_id );
		   	} catch (Exception $e) {
		   		$GLOBALS['erLhcoreClassModelUser_'.$user_id] = null;
		   	}
		   	
		   	return $GLOBALS['erLhcoreClassModelUser_'.$user_id];
	   	}
	   		   	
   	 	$user = $GLOBALS['erLhcoreClassModelUser_'.$user_id] = erLhcoreClassUser::getSession('slave')->load( 'erLhcoreClassModelUser', (int)$user_id );
   	 	   	 	
   	 	return $user;
   }

   public function __toString()
   {
   		return $this->username.' ('.$this->email.')';
   }
   public static function getUserCount($params = array())
   {
       $session = erLhcoreClassUser::getSession();
       $q = $session->database->createSelectQuery();
       $q->select( "COUNT(id)" )->from( "lh_users" );

       if (isset($params['filter']) && count($params['filter']) > 0)
       {
           $conditions = array();

           foreach ($params['filter'] as $field => $fieldValue)
           {
               $conditions[] = $q->expr->eq( $field, $q->bindValue($fieldValue) );
           }

           $q->where(
                 $conditions
           );
      }

      $stmt = $q->prepare();
      $stmt->execute();
      $result = $stmt->fetchColumn();

      return $result;
   }

   public function __get($param)
   {
       switch ($param) {

       	case 'name_support':
       			return $this->chat_nickname != '' ? trim($this->chat_nickname) : trim($this->name_official);
       		break;

       	case 'name_official':
       			$this->name_official = trim($this->name.' '.$this->surname);
       			$this->name_official = $this->name_official != '' ? $this->name_official : $this->chat_nickname;
       			return $this->name_official;
       		break;

       	case 'user_groups_id':
       		   $userGroups = erLhcoreClassModelGroupUser::getList(array('filter' => array('user_id' => $this->id)));
       		   $this->user_groups_id = array();

       		   if (!empty($userGroups)) {
       		   		foreach ($userGroups as $userGroup) {
       		   	 		$this->user_groups_id[] = $userGroup->group_id;
       		   		}
       		   }

       		   return $this->user_groups_id;
       		break;

       	case 'lastactivity':
       	        $db = ezcDbInstance::get();
       	        $stmt = $db->prepare('SELECT last_activity FROM lh_userdep WHERE user_id = :user_id LIMIT 1');
       	        $stmt->bindValue(':user_id',$this->id,PDO::PARAM_INT);
                $stmt->execute();

                $this->lastactivity = (int)$stmt->fetchColumn();
                return $this->lastactivity;

       	    break;

       	case 'has_photo':
       	    	return $this->filename != '';
       	    break;

       	case 'photo_path':
       			$this->photo_path = ($this->filepath != '' ? erLhcoreClassSystem::instance()->wwwDir() : erLhcoreClassSystem::instance()->wwwImagesDir() ) .'/'. $this->filepath . $this->filename;
       			return $this->photo_path;
       		break;

       	case 'file_path_server':
       			return $this->filepath . $this->filename;
       		break;

       	case 'lastactivity_front':
       		   $this->lastactivity_front = '';

       		   if ( $this->lastactivity > 0 ) {
       		       $this->lastactivity_front = date(erLhcoreClassModule::$dateDateHourFormat);
       		   };

       		   return $this->lastactivity_front;
       		break;

       	case 'lastactivity_ago':
       		   $this->lastactivity_ago = erLhcoreClassChat::getAgoFormat($this->lastactivity);       		   
       		   return $this->lastactivity_ago;
       		break;

       	default:
       		break;
       }
   }

   public static function getUserList($paramsSearch = array())
   {
       $paramsDefault = array('limit' => 500000, 'offset' => 0);

       $params = array_merge($paramsDefault,$paramsSearch);

       $session = erLhcoreClassUser::getSession();
       $q = $session->createFindQuery( 'erLhcoreClassModelUser' );

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
           $q2->select( 'pid' )->from( 'lh_users' );

           if (isset($params['filter']) && count($params['filter']) > 0)
          {
               foreach ($params['filter'] as $field => $fieldValue)
               {
                   $conditions[] = $q2->expr->eq( $field, $q->bindValue($fieldValue ));
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
                   $conditions[] = $q2->expr->lt( $field, $q->bindValue($fieldValue ));
               }
          }

          if (isset($params['filtergt']) && count($params['filtergt']) > 0)
          {
               foreach ($params['filtergt'] as $field => $fieldValue)
               {
                   $conditions[] = $q2->expr->gt( $field,$q->bindValue( $fieldValue) );
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
          $q->innerJoin( $q->alias( $q2, 'items' ), 'lh_users.id', 'items.id' );
       }

       $objects = $session->find( $q );

      return $objects;
   }

   public static function userExists($username)
   {
       $db = ezcDbInstance::get();
       $stmt = $db->prepare('SELECT count(*) as foundusers FROM lh_users WHERE username = :username');
       $stmt->bindValue( ':username',$username);
       $stmt->execute();
       $rows = $stmt->fetchAll();

       return $rows[0]['foundusers'] > 0;
   }

   public static function fetchUserByEmail($email, $xmpp_username = false)
   {
       $db = ezcDbInstance::get();
       $xmppAppend = $xmpp_username !== false ? ' OR xmpp_username = :xmpp_username' : '';       
       $stmt = $db->prepare('SELECT id FROM lh_users WHERE email = :email'.$xmppAppend);
       $stmt->bindValue( ':email',$email);
       
       if ($xmpp_username !== false) {
       		$stmt->bindValue( ':xmpp_username',$xmpp_username);       		
       }
       
       $stmt->execute();
       $rows = $stmt->fetchAll();

       if (isset($rows[0]['id'])) {
            return $rows[0]['id'];
       } else {
            return false;
       }
   }

   public function saveThis(){
   		erLhcoreClassUser::getSession()->saveOrUpdate($this);
   }

   public function removeFile()
   {   		   	
	   	if ($this->filename != '') {
	   		if ( file_exists($this->filepath . $this->filename) ) {
	   			unlink($this->filepath . $this->filename);
	   		}

	   		if ($this->filepath != '') {
	   			erLhcoreClassFileUpload::removeRecursiveIfEmpty('var/userphoto/',str_replace('var/userphoto/','',$this->filepath));
	   		}
	   		
	   		erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.remove_photo', array('user' => & $this));
	   		
	   		$this->filepath = '';
	   		$this->filename = '';
	   		$this->saveThis();
	   	}
   }

   public function setUserGroups() {
   		
		erLhcoreClassModelGroupUser::removeUserFromGroups($this->id);
		
		foreach ($this->user_groups_id as $group_id) {
			$groupUser = new erLhcoreClassModelGroupUser();
			$groupUser->group_id = $group_id;
			$groupUser->user_id = $this->id;
			$groupUser->saveThis();
		}
		
   	}
   
   	public static function findOne($paramsSearch = array()) {
   		
   		$paramsSearch['limit'] = 1;
   		
   		$list = self::getUserList($paramsSearch);
   		
   		if (! empty($list)) {
   			reset($list);
   			return current($list);
   		}
   	
   		return false;
   		
   	}   	
   	
    public $id = null;
    public $username = '';
    public $password = '';
    public $email = '';
    public $name = '';
    public $filepath = '';
    public $filename = '';
    public $surname = '';
    public $job_title = '';
    public $departments_ids = '';
    public $skype = '';
    public $xmpp_username = '';
    public $disabled = 0;
    public $hide_online = 0;
    public $all_departments = 0;
    public $invisible_mode = 0;
    public $time_zone = '';
    public $rec_per_req = '';
    public $session_id = '';
    public $chat_nickname = '';
    public $active_chats_counter = 0;
    public $closed_chats_counter = 0;
    public $pending_chats_counter = 0;
    public $operation_admin = '';
    public $inactive_mode = 0;
    public $max_active_chats = 0;
    public $auto_accept = 0;
    public $exclude_autoasign = 0;

    public $attr_int_1 = 0;
    public $attr_int_2 = 0;
    public $attr_int_3 = 0;
}

?>